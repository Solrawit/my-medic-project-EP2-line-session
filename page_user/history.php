<?php
session_start();
require_once('../LineLogin.php');
require_once('../db_connection.php');
require_once('line_notification.php');

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
        SELECT id, line_user_id, display_name, ocr_scans_text, ocr_image_data, login_time, medicine_alert_time, access_token, 
               ocr_scans_text2, ocr_image_data2, medicine_alert_time2, access_token2, image, image2
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

// Handle notification request for OCR history without file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['ocr_id']) && isset($_POST['slot'])) {
    $access_token = $_POST['token'];
    $ocr_id = $_POST['ocr_id'];
    $slot = $_POST['slot'];
    $notify_hour = $_POST['notify_hour'];
    $notify_minute = $_POST['notify_minute'];
    $notify_time = sprintf('%02d:%02d', $notify_hour, $notify_minute);

    $message = '';
    $image_url = '';
    $sheet_data = [];

    foreach ($ocrHistory as $entry) {
        if ($entry['id'] == $ocr_id) {
            if ($slot === 'slot1') {
                $message = "รายการยา: " . $entry['ocr_scans_text'];
                $image_url = $entry['image'];
                $sheet_data = [
                    'id' => $entry['id'],
                    'line_user_id' => $entry['line_user_id'],
                    'display_name' => $entry['display_name'],
                    'medicine_alert_time' => $notify_time,
                    'ocr_scans_text' => $entry['ocr_scans_text'],
                    'access_token' => $access_token,
                    'ocr_image_data' => $entry['ocr_image_data'],
                    'medicine_alert_time2' => $entry['medicine_alert_time2'],
                    'ocr_scans_text2' => $entry['ocr_scans_text2'],
                    'access_token2' => $entry['access_token2'],
                    'ocr_image_data2' => $entry['ocr_image_data2'],
                ];
                $stmt = $db->prepare("
                    UPDATE users
                    SET medicine_alert_time = :medicine_alert_time, access_token = :access_token
                    WHERE line_user_id = :line_user_id AND id = :id
                ");
            } elseif ($slot === 'slot2') {
                $message = "รายการยา: " . $entry['ocr_scans_text2'];
                $image_url = $entry['image2'];
                $sheet_data = [
                    'id' => $entry['id'],
                    'line_user_id' => $entry['line_user_id'],
                    'display_name' => $entry['display_name'],
                    'medicine_alert_time' => $entry['medicine_alert_time'],
                    'ocr_scans_text' => $entry['ocr_scans_text'],
                    'access_token' => $entry['access_token'],
                    'ocr_image_data' => $entry['ocr_image_data'],
                    'medicine_alert_time2' => $notify_time,
                    'ocr_scans_text2' => $entry['ocr_scans_text2'],
                    'access_token2' => $access_token,
                    'ocr_image_data2' => $entry['ocr_image_data2'],
                ];
                $stmt = $db->prepare("
                    UPDATE users
                    SET medicine_alert_time2 = :medicine_alert_time, access_token2 = :access_token
                    WHERE line_user_id = :line_user_id AND id = :id
                ");
            }
            break;
        }
    }

    $stmt->bindParam(':medicine_alert_time', $notify_time, PDO::PARAM_STR);
    $stmt->bindParam(':access_token', $access_token, PDO::PARAM_STR);
    $stmt->bindParam(':line_user_id', $line_user_id, PDO::PARAM_STR);
    $stmt->bindParam(':id', $ocr_id, PDO::PARAM_INT);
    $stmt->execute();

    $success = sendLineNotification($access_token, $message, $image_url);

    if ($success) {
        // Send data to Google Sheets
        $sheetUrl = 'https://sheetdb.io/api/v1/98locb0xjprmo';
        sendToGoogleSheet($sheet_data, $sheetUrl);

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
    exit();
}

// Handle delete OCR request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ocr_id'])) {
    $ocr_id = $_POST['delete_ocr_id'];
    $slot = $_POST['slot'];

    try {
        if ($slot === 'slot1') {
            $stmt = $db->prepare("UPDATE users SET ocr_scans_text = NULL, ocr_image_data = NULL, medicine_alert_time = NULL, access_token = NULL, image = NULL WHERE id = :ocr_id AND line_user_id = :line_user_id");
        } elseif ($slot === 'slot2') {
            $stmt = $db->prepare("UPDATE users SET ocr_scans_text2 = NULL, ocr_image_data2 = NULL, medicine_alert_time2 = NULL, access_token2 = NULL, image2 = NULL WHERE id = :ocr_id AND line_user_id = :line_user_id");
        }
        $stmt->bindParam(':ocr_id', $ocr_id, PDO::PARAM_INT);
        $stmt->bindParam(':line_user_id', $line_user_id, PDO::PARAM_STR);
        $stmt->execute();

        // Delete from Google Sheet
        if (deleteFromGoogleSheet($ocr_id, $slot)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update Google Sheet']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

function deleteFromGoogleSheet($id, $slot) {
    $sheetUrl = 'https://sheetdb.io/api/v1/98locb0xjprmo/search?id=' . $id;
    $updateUrl = 'https://sheetdb.io/api/v1/98locb0xjprmo/id/' . $id;

    // Fetch current data from Google Sheet
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $sheetUrl,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    $existingData = json_decode($result, true);

    if (!empty($existingData)) {
        $data = $existingData[0];
        if ($slot === 'slot1') {
            $data['medicine_alert_time'] = '';
            $data['ocr_scans_text'] = '';
            $data['access_token'] = '';
            $data['ocr_image_data'] = '';
            $data['image'] = '';
        } elseif ($slot === 'slot2') {
            $data['medicine_alert_time2'] = '';
            $data['ocr_scans_text2'] = '';
            $data['access_token2'] = '';
            $data['ocr_image_data2'] = '';
            $data['image2'] = '';
        }

        // Update Google Sheet
        $payload = json_encode($data);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $updateUrl,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200 && $httpcode !== 201) {
            error_log("Error updating Google Sheet. HTTP status code: $httpcode. Response: $result");
            return false;
        }

        return true;
    }

    return false;
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
    <link rel="stylesheet" type="text/css" href="../assets/css/forwelcome.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/loadweb.css">
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
            background-image: url('../assets/images/back.jpg');
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
     <!-- โหลดหน้าเว็ป -->
<div id="loader" class="loader">
        <div class="container">
            <div class="carousel">
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
            </div> 
        </div>
        <div class="container">
            <div class="carousel">
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
            </div> 
        </div>
        <div class="container">
            <div class="carousel">
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
            </div> 
        </div>
    </div>
    
    <script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            document.getElementById('loader').classList.add('hidden');
        }, 1500); // รอ 1.5 วินาทีก่อนที่จะซ่อนตัวโหลด
    });
</script>
<!-- โหลดหน้าเว็ป -->
    <?php include '../component/nav_textphoto.php'; ?>
    <div class="container">
        <h1 class="mt-4 mb-4 text-white">Medicine History</h1>
        <h4 class="mt-2 mb-2 text-white">ข้อมูลยาของฉัน</h4>
        <?php foreach ($ocrHistory as $entry) : ?>
            <div class="card">
                <div class="card-body">
                    <!-- Slot 1 -->
                    <?php if (!empty($entry['ocr_image_data'])) : ?>
                        <img src="<?= htmlspecialchars($entry['ocr_image_data']) ?>" alt="OCR Image">
                    <?php endif; ?>
                    <div>
                        <h5>รายการยาที่ 1</h5>
                        <p class="card-text" id="ocrText<?= $entry['id'] ?>"><?= htmlspecialchars($entry['ocr_scans_text']) ?></p>
                        <p class="text-muted">เวลาที่แจ้งเตือน: <?= htmlspecialchars($entry['medicine_alert_time']) ?></p>
                        <?php if (!empty($entry['ocr_scans_text'])) : ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="editOCR(<?= $entry['id'] ?>, 'slot1')">แก้ไข</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteOCR(<?= $entry['id'] ?>, 'slot1')">ลบ</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="showNotificationForm(<?= $entry['id'] ?>, 'slot1')">แจ้งเตือนผ่าน LINE</button>
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="featurette-divider">
                <div class="card-body">
                    <!-- Slot 2 -->
                    <?php if (!empty($entry['ocr_image_data2'])) : ?>
                        <img src="<?= htmlspecialchars($entry['ocr_image_data2']) ?>" alt="OCR Image">
                    <?php endif; ?>
                    <div>
                        <h5>รายการยาที่ 2</h5>
                        <p class="card-text" id="ocrText2<?= $entry['id'] ?>"><?= htmlspecialchars($entry['ocr_scans_text2']) ?></p>
                        <p class="text-muted">เวลาที่แจ้งเตือน: <?= htmlspecialchars($entry['medicine_alert_time2']) ?></p>
                        <?php if (!empty($entry['ocr_scans_text2'])) : ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="editOCR(<?= $entry['id'] ?>, 'slot2')">แก้ไข</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteOCR(<?= $entry['id'] ?>, 'slot2')">ลบ</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="showNotificationForm(<?= $entry['id'] ?>, 'slot2')">แจ้งเตือนผ่าน LINE</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include '../component/footer.php'; ?>

    <!-- Notification form modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Send Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="notificationForm" method="post">
                        <input type="hidden" name="ocr_id" id="ocrId">
                        <input type="hidden" name="slot" id="slot">
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
                        <button type="submit" class="btn btn-success">Send Notification</button>
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
        function showNotificationForm(ocrId, slot) {
            const ocrEntry = <?= json_encode($ocrHistory) ?>.find(entry => entry.id == ocrId);
            let message = '';
            if (slot === 'slot1') {
                message = `รายการยา: ${ocrEntry.ocr_scans_text}`;
            } else if (slot === 'slot2') {
                message = `รายการยา: ${ocrEntry.ocr_scans_text2}`;
            }
            $('#ocrId').val(ocrId);
            $('#slot').val(slot);
            $('#messagePreview').val(message);
            $('#notificationModal').modal('show');
        }

        function editOCR(id, slot) {
            let ocrText = '';
            if (slot === 'slot1') {
                ocrText = document.getElementById(`ocrText${id}`).innerText;
            } else if (slot === 'slot2') {
                ocrText = document.getElementById(`ocrText2${id}`).innerText;
            }
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
                        data: { id: id, ocr_text: result.value, slot: slot },
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

        function deleteOCR(id, slot) {
            Swal.fire({
                title: 'ลบข้อมูลตัวยา',
                text: 'คุณแน่ใจหรือไม่ที่จะลบข้อมูลตัวยานี้?',
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
                        data: { id: id, slot: slot },
                        success: function(response) {
                            var res = JSON.parse(response);
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบข้อมูลเรียบร้อยแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: res.message,
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error: ' + status + ' ' + error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถลบข้อมูลได้',
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        }

        $('#notificationForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            
            $.ajax({
                url: 'history.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('body').append(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' - ' + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถส่งการแจ้งเตือนได้',
                        showConfirmButton: true
                    });
                }
            });
        });
    </script>
</body>
</html>
