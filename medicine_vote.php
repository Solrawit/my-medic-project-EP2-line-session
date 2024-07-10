<?php
session_start();
require_once('LineLogin.php');
require_once('db_connection.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage());
}

if (!isset($_SESSION['profile'])) {
    header("location: index.php");
    exit();
}

$profile = $_SESSION['profile'];

$name = isset($profile->displayName) ? $profile->displayName : 'ไม่พบชื่อ';
$email = isset($profile->email) ? $profile->email : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? $profile->pictureUrl : 'ไม่มีรูปภาพโปรไฟล์';

if ($email === 'ไม่พบอีเมล์') {
    // กรณีไม่พบข้อมูลอีเมล์ให้แสดงข้อความเพื่อแจ้งให้ผู้ใช้ทราบ
    echo "ไม่พบข้อมูลอีเมล์";
    exit();
}

// ตรวจสอบหา userId จากตาราง users โดยใช้ email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // หากไม่พบผู้ใช้ในฐานข้อมูล ให้ทำการเพิ่มข้อมูลผู้ใช้ หรือจัดการตามที่เหมาะสม
    echo "ไม่พบผู้ใช้ในระบบ";
    exit();
}

$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data (assuming you have the validation code here)
    // Example:
    $smoothness = isset($_POST['smoothness']) ? intval($_POST['smoothness']) : 0;
    $stability_website = isset($_POST['stability_website']) ? intval($_POST['stability_website']) : 0;
    $stability_system = isset($_POST['stability_system']) ? intval($_POST['stability_system']) : 0;
    $ease_of_use = isset($_POST['ease_of_use']) ? intval($_POST['ease_of_use']) : 0;
    $complexity = isset($_POST['complexity']) ? intval($_POST['complexity']) : 0;

    // Insert feedback into database
    $sql = "INSERT INTO feedback (user_id, display_name, smoothness, stability_website, stability_system, ease_of_use, complexity)
            VALUES (:user_id, :display_name, :smoothness, :stability_website, :stability_system, :ease_of_use, :complexity)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':display_name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':smoothness', $smoothness, PDO::PARAM_INT);
    $stmt->bindParam(':stability_website', $stability_website, PDO::PARAM_INT);
    $stmt->bindParam(':stability_system', $stability_system, PDO::PARAM_INT);
    $stmt->bindParam(':ease_of_use', $ease_of_use, PDO::PARAM_INT);
    $stmt->bindParam(':complexity', $complexity, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // แสดง SweetAlert สำหรับบันทึกข้อมูลสำเร็จ
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        text: 'ขอบคุณที่ให้ความคิดเห็น',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php';  // Redirect to index or any other page
                        }
                    });
                });
              </script>";
    } else {
        // แสดง SweetAlert สำหรับเกิดข้อผิดพลาดในการบันทึกข้อมูล
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถบันทึกข้อมูลได้ โปรดลองอีกครั้ง',
                        confirmButtonText: 'OK'
                    });
                });
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <h2 class="mt-5">Feedback Form</h2>
    <form method="post" id="feedback-form">
        <div class="mb-3">
            <label for="smoothness" class="form-label">ความลื่นไหลของตัวเว็ปไซต์</label>
            <select class="form-select" id="smoothness" name="smoothness" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="stability_website" class="form-label">ความสเถียรของตัวเว็ปไซต์</label>
            <select class="form-select" id="stability_website" name="stability_website" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="stability_system" class="form-label">ความสเถียรของระบบ</label>
            <select class="form-select" id="stability_system" name="stability_system" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="ease_of_use" class="form-label">ความง่ายต่อการใช้งาน</label>
            <select class="form-select" id="ease_of_use" name="ease_of_use" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="complexity" class="form-label">ความซับซ้อนในการใช้งาน</label>
            <select class="form-select" id="complexity" name="complexity" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" id="submit-btn">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
