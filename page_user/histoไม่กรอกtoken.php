<?php
session_start();
require_once('../LineLogin.php');
require_once('../db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['profile'])) {
    header("location: ../index.php");
    exit();
}

$line_user_id = $_SESSION['profile']->userId;
$line_name = $_SESSION['profile']->displayName;

try {
    // Fetch OCR history
    $stmt = $db->prepare("
        SELECT id, ocr_scans_text, ocr_image_data, login_time
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


// Function to send notification via LINE Notify
function sendLineNotification($access_token, $message, $image_path = null) {
    $url = 'https://notify-api.line.me/api/notify';
    $headers = [
        'Authorization: Bearer ' . $access_token,
    ];

    $data = [
        'message' => $message,
    ];

    if ($image_path) {
        $data['imageFile'] = new CURLFile($image_path);
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        error_log("Error sending LINE notification. HTTP status code: $httpcode. Response: $result");
        return false; // Notify calling function that notification failed
    }

    return true; // Notification sent successfully
}

// Handle notification request for OCR history with file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $access_token = '2TOlLhUrIhnDC4w2Bxq6x7g9oNKTBWure7CXm0mItOd'; // Replace with your actual LINE Notify access token
    $upload_dir = 'uploads/';
    $file_path = $upload_dir . basename($_FILES['file']['name']);
    $ocr_id = $_POST['ocr_id'];

    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // Fetch the corresponding OCR entry
        $message = '';
        foreach ($ocrHistory as $entry) {
            if ($entry['id'] == $ocr_id) {
                $message = "รายการยา: " . $entry['ocr_scans_text'] . " แจ้งเตือนโดยคุณ " . $line_name;
                break;
            }
        }

        $success = sendLineNotification($access_token, $message, $file_path);

        if ($success) {
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

// Fetch site settings
$siteSettings = getSiteSettings($db);
$siteName = isset($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Default Site Name';
$contactEmail = isset($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'default@example.com';
$siteNav = isset($siteSettings['site_nav']) ? $siteSettings['site_nav'] : 'Test';
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
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- favicon images -->
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
    <h4 class="mt-2 mb-2 text-white">ประวัติของฉัน</h4>

        <?php foreach ($ocrHistory as $entry) : ?>
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($entry['ocr_image_data'])) : ?>
                        <img src="<?= htmlspecialchars($entry['ocr_image_data']) ?>" alt="OCR Image">
                    <?php endif; ?>
                    <div>
                        <p class="card-text"><?= htmlspecialchars($entry['ocr_scans_text']) ?></p>
                        <p class="text-muted"><?= date('F j, Y, g:i a', strtotime($entry['login_time'])) ?></p>
                        <?php if (!empty($entry['ocr_scans_text'])) : ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="editOCR(<?= $entry['id'] ?>)">แก้ไข</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteOCR(<?= $entry['id'] ?>)">ลบ</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="showUploadForm(<?= $entry['id'] ?>)" disabled>แจ้งเตือนผ่าน LINE</button>

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
            $('#ocrId').val(ocrId);
            $('#uploadModal').modal('show');
        }

        function sendOCRNotification(ocrId) {
            $.ajax({
                url: '', // Use the same path URL
                method: 'POST',
                data: { notify: true, ocr_id: ocrId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'แจ้งเตือนผ่าน LINE เรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: result.message,
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถส่งการแจ้งเตือนผ่าน LINE ได้',
                        showConfirmButton: true
                    });
                }
            });
        }

        function editOCR(id) {
            Swal.fire({
                title: 'แก้ไขข้อความตัวยา',
                input: 'textarea',
                inputLabel: 'ข้อความตัวยา',
                inputValue: '',
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
                        url: 'update_ocr.php', // ตั้งค่า URL ที่เป็น path ไปยังไฟล์ PHP ของคุณ
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
                        url: 'delete_ocr.php', // ตั้งค่า URL ที่เป็น path ไปยังไฟล์ PHP ของคุณ
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
            
            $.ajax({
                url: '', // Use the same path URL
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('body').append(response); // Append the response to the body to execute the script
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
