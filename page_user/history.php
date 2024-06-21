<?php
session_start();
require_once('../LineLogin.php');

if (!isset($_SESSION['profile'])) {
    header("location: ../index.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'mdpj_user';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

$line_user_id = $_SESSION['profile']->userId;

try {
    // Fetch OCR history
    $stmt = $pdo->prepare("
        SELECT id, ocr_scans_text, ocr_image_data, login_time
        FROM users
        WHERE line_user_id = :line_user_id
        ORDER BY login_time DESC
    ");
    $stmt->bindParam(':line_user_id', $line_user_id);
    $stmt->execute();
    $ocrHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/medic.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/index.css">
    <title>OCR History</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Sarabun', sans-serif;
            padding: 20px;
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
    <h1 class="mt-4 mb-4">OCR History</h1>
    <h4 class="mt-2 mb-2">ประวัติของฉัน</h4>
    <?php foreach ($ocrHistory as $entry): ?>
        <div class="card">
            <div class="card-body">
                <?php if (!empty($entry['ocr_image_data'])): ?>
                    <img src="<?= $entry['ocr_image_data'] ?>" alt="OCR Image">
                <?php endif; ?>
                <div>
                    <p class="card-text"><?= htmlspecialchars($entry['ocr_scans_text']) ?></p>
                    <p class="text-muted"><?= date('F j, Y, g:i a', strtotime($entry['login_time'])) ?></p>
                    <?php if (!empty($entry['ocr_scans_text'])): ?>
                        <button type="button" class="btn btn-primary btn-sm" onclick="editOCR(<?= $entry['id'] ?>)">แก้ไข</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteOCR(<?= $entry['id'] ?>)">ลบ</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include '../component/footer.php'; ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function editOCR(id) {
        Swal.fire({
            title: 'แก้ไขข้อความ OCR',
            input: 'textarea',
            inputLabel: 'ข้อความ OCR',
            inputValue: '',
            inputPlaceholder: 'กรอกข้อความ OCR ที่ต้องการแก้ไข',
            showCancelButton: true,
            confirmButtonText: 'บันทึกการแก้ไข',
            cancelButtonText: 'ยกเลิก',
            inputValidator: (value) => {
                if (!value) {
                    return 'กรุณากรอกข้อความ OCR';
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
            title: 'ลบข้อมูล OCR',
            text: 'คุณแน่ใจหรือไม่ที่จะลบข้อมูล OCR นี้?',
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
</script>
</body>
</html>
