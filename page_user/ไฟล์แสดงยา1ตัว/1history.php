<?php
session_start();
require_once('../LineLogin.php');
require_once('../db_connection.php');

// ตั้งค่าการปิดปรับปรุง
$stmt = $db->query("SELECT maintenance_mode FROM settings WHERE id = 1");
$settings = $stmt->fetch();
$maintenance_mode = $settings['maintenance_mode'];

// ตรวจสอบสถานะการเข้าสู่ระบบและบทบาทของผู้ใช้
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

if ($maintenance_mode && $user_role !== 'admin') {
    header('Location: ../maintenance');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['profile'])) {
    header("location: ../index");
    exit();
}

$line_user_id = $_SESSION['profile']->userId;

try {
    // Fetch OCR history
    $stmt = $db->prepare("
        SELECT id, ocr_scans_text, ocr_image_data, login_time, medicine_alert_time, access_token, image
        FROM users
        WHERE line_user_id = :line_user_id
        ORDER BY login_time DESC
    ");
    $stmt->bindParam(':line_user_id', $line_user_id, PDO::PARAM_STR);
    $stmt->execute();
    $ocrHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

// Function to upload image to imgbb
function uploadToImgbb($file) {
    $api_key = '22ff1cd06128d8e6dedb746a314f57f0';
    $url = 'https://api.imgbb.com/1/upload';

    $image_data = base64_encode(file_get_contents($file));

    $data = [
        'key' => $api_key,
        'image' => $image_data
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    $result_data = json_decode($result, true);

    if (isset($result_data['data']['url'])) {
        return $result_data['data']['url'];
    }

    return false;
}

// Function to send notification via LINE Notify
function sendLineNotification($access_token, $message, $image_url = null) {
    $url = 'https://notify-api.line.me/api/notify';
    $headers = [
        'Authorization: Bearer ' . $access_token,
    ];

    $data = [
        'message' => $message,
    ];

    if ($image_url) {
        $data['imageThumbnail'] = $image_url;
        $data['imageFullsize'] = $image_url;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        error_log("Error sending LINE notification. HTTP status code: $httpcode. Response: $result");
        return false;
    }

    return true;
}

// Function to update Google Sheets via SheetDB API
function updateSheetDB($id, $medicine_alert_time, $access_token, $image_url) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v/id/' . $id; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Prepare data for upload to SheetDB
    $data_to_upload = [
        "medicine_alert_time" => $medicine_alert_time,
        "access_token" => $access_token,
        "image" => $image_url
    ];

    // Send data to SheetDB API via cURL
    $ch = curl_init($sheetdb_api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); // Use PATCH for updating existing data
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_upload));
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("Failed to update data in SheetDB.");
    }
}

// Handle notification request for OCR history with file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $access_token = $_POST['token'];
    $ocr_id = $_POST['ocr_id'];
    $notify_hour = $_POST['notify_hour'];
    $notify_minute = $_POST['notify_minute'];
    $notify_time = sprintf('%02d:%02d', $notify_hour, $notify_minute);

    $file_path = $_FILES['file']['tmp_name'];
    $image_url = uploadToImgbb($file_path);

    if ($image_url) {
        $message = '';
        foreach ($ocrHistory as $entry) {
            if ($entry['id'] == $ocr_id) {
                $message = "รายการยา: " . $entry['ocr_scans_text'];
                break;
            }
        }

        $success = sendLineNotification($access_token, $message, $image_url);

        if ($success) {
            try {
                // Update medicine_alert_time, access_token and image in users table
                $stmt = $db->prepare("
                    UPDATE users
                    SET medicine_alert_time = :medicine_alert_time, access_token = :access_token, image = :image
                    WHERE line_user_id = :line_user_id AND id = :id
                ");
                $stmt->bindParam(':medicine_alert_time', $notify_time, PDO::PARAM_STR);
                $stmt->bindParam(':access_token', $access_token, PDO::PARAM_STR);
                $stmt->bindParam(':image', $image_url, PDO::PARAM_STR);
                $stmt->bindParam(':line_user_id', $line_user_id, PDO::PARAM_STR);
                $stmt->bindParam(':id', $ocr_id, PDO::PARAM_INT);
                $stmt->execute();

                // Update SheetDB
                updateSheetDB($ocr_id, $notify_time, $access_token, $image_url);

                echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "แจ้งเตือนผ่าน LINE เรียบร้อยแล้ว",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = window.location.href; // Reload the page
                        });
                      </script>';
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
                exit();
            }
        } else {
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "เกิดข้อผิดพลาด",
                        text: "Failed to send notification",
                        showConfirmButton: true
                    });
                  </script>';
        }
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "เกิดข้อผิดพลาด",
                    text: "Failed to upload file",
                    showConfirmButton: true
                });
              </script>';
    }
    exit();
}

// Handle delete OCR request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ocr_id'])) {
    $ocr_id = $_POST['delete_ocr_id'];

    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :ocr_id AND line_user_id = :line_user_id");
        $stmt->bindParam(':ocr_id', $ocr_id, PDO::PARAM_INT);
        $stmt->bindParam(':line_user_id', $line_user_id, PDO::PARAM_STR);
        $stmt->execute();

        echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "ลบข้อมูลเรียบร้อยแล้ว",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = window.location.href; // Reload the page
                });
              </script>';
    } catch (PDOException $e) {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "เกิดข้อผิดพลาด",
                    text: "Failed to delete data",
                    showConfirmButton: true
                });
              </script>';
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/medic.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/forwelcome.css">
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- favicon image -->
    <title>Medicine History</title>
    <style>
        body {
            position: relative;
            font-family: 'Sarabun', sans-serif;
            padding: 0px 0px;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../assets/images/wpp3.png');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-body {
            display: flex;
            align-items: center;
        }

        .card-body img {
            max-width: 200px;
            max-height: 200px;
            margin-right: 20px;
            border-radius: 5px;
            object-fit: cover;
        }

        .card-text {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <?php include '../component/nav_textphoto.php'; ?>
    <div class="container">
        <h1 class="mt-4 mb-4 text-white">Medicine History</h1>
        <h4 class="mt-2 mb-2 text-white">ข้อมูลยาของฉัน</h4>
        <?php foreach ($ocrHistory as $entry) : ?>
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($entry['ocr_image_data'])) : ?>
                        <img src="<?= htmlspecialchars($entry['ocr_image_data']) ?>" alt="OCR Image">
                    <?php endif; ?>
                    <div>
                        <p class="card-text" id="ocrText<?= $entry['id'] ?>"><?= htmlspecialchars($entry['ocr_scans_text']) ?></p>
                        <p class="text-muted"><?= date('F j, Y, g:i a', strtotime($entry['login_time'])) ?></p>
                        <p class="text-muted">เวลาที่แจ้งเตือน: <?= htmlspecialchars($entry['medicine_alert_time']) ?></p>
                        <?php if (!empty($entry['ocr_scans_text'])) : ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="editOCR(<?= $entry['id'] ?>)">แก้ไข</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteOCR(<?= $entry['id'] ?>)">ลบ</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="showUploadForm(<?= $entry['id'] ?>)">แจ้งเตือนผ่าน LINE</button>
                            <!-- tokenทดสอบ t1VVF2xuiQUoBKTrOkcFOtvzj9Yjptiq6ixUNIIdvgv -->
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include '../component/footer.php'; ?>

    <!-- Upload form modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <input type="hidden" name="ocr_id" id="ocrId">
                        <div class="mb-3">
                            <label for="token" class="form-label">LINE Notify Token</label>
                            <input type="text" class="form-control" id="token" name="token" required>
                        </div>
                        <div class="mb-3">
                            <label for="messagePreview" class="form-label">ข้อความที่จะแจ้งเตือน</label>
                            <textarea class="form-control" id="messagePreview" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notifyHour" class="form-label">เวลาที่ต้องการให้แจ้งเตือน</label>
                            <select id="notifyHour" name="notify_hour" class="form-control" required>
                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?= sprintf('%02d', $hour) ?>"><?= sprintf('%02d', $hour) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select id="notifyMinute" name="notify_minute" class="form-control" required>
                                <?php for ($minute = 0; $minute < 60; $minute++): ?>
                                    <option value="<?= sprintf('%02d', $minute) ?>"><?= sprintf('%02d', $minute) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showUploadForm(ocrId) {
            const ocrEntry = <?= json_encode($ocrHistory) ?>.find(entry => entry.id == ocrId);
            const message = `รายการยา: ${ocrEntry.ocr_scans_text}`;
            $('#ocrId').val(ocrId);
            $('#messagePreview').val(message);
            $('#uploadModal').modal('show');
        }

        function editOCR(id) {
            const ocrText = document.getElementById(`ocrText${id}`).innerText;
            Swal.fire({
                title: 'แก้ไขข้อความตัวยา',
                input: 'textarea',
                inputLabel: 'ข้อความตัวยา',
                inputValue: ocrText,
                inputPlaceholder: 'กรอกข้อความ ที่ต้องการแก้ไข',
                showCancelButton: true,
                confirmButtonText: 'บันทึกการแก้ไข',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกข้อความตัวยา';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to update OCR text
                    $.ajax({
                        url: 'update_ocr.php',
                        method: 'POST',
                        data: { id: id, ocr_text: result.value },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error: ' + status + ' ' + error);
                        }
                    });
                }
            });
        }

        function deleteOCR(id) {
            Swal.fire({
                title: 'ลบข้อมูลตัวยา',
                text: 'คุณแน่ใจหรือไม่ที่จะลบข้อมูลตัวยา นี้?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete OCR data
                    $.ajax({
                        url: 'delete_ocr.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error: ' + status + ' ' + error);
                        }
                    });
                }
            });
        }

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var hour = $('#notifyHour').val();
            var minute = $('#notifyMinute').val();
            var notifyTime = `${hour}:${minute}`;
            formData.append('notify_time', notifyTime);
            
            $.ajax({
                url: 'history.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('body').append(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถอัปโหลดไฟล์ได้',
                        showConfirmButton: true
                    });
                }
            });
        });
    </script>
</body>
</html>
