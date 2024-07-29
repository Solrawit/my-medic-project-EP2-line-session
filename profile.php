<?php
session_start();
require_once('LineLogin.php');
require_once('db_connection.php'); // เพิ่มไฟล์การเชื่อมต่อฐานข้อมูล
include 'timeout.php';

if (!isset($_SESSION['profile'])) {
    header("location: index.php");
    exit();
}

$profile = $_SESSION['profile'];
$lineUserId = $profile->userId; // สมมติว่ามี userId ในโปรไฟล์

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$query = $db->prepare("SELECT display_name, email, picture_url, role, login_time FROM users WHERE line_user_id = ?");
$query->execute([$lineUserId]);
$userData = $query->fetch(PDO::FETCH_ASSOC);

$name = $userData['display_name'] ?? 'ไม่พบชื่อ';
$email = $userData['email'] ?? 'ไม่พบอีเมล์';
$picture = $userData['picture_url'] ?? 'ไม่มีรูปภาพโปรไฟล์';
$role = $userData['role'] ?? 'ไม่พบข้อมูล';
$loginTime = $userData['login_time'] ?? 'ไม่พบข้อมูลการล็อกอิน';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" type="text/css" href="ripples-background.css.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="favicon.png"> <!-- favicon image -->
    <style>
         body {
            background-image: url('assets/images/wpp6.jpg'); /* เปลี่ยนเป็น URL ของรูปภาพพื้นหลังที่คุณต้องการใช้ */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif; /* เปลี่ยนแบบอักษรตามที่ต้องการ */
            color: #333; /* เปลี่ยนสีตัวอักษร */
        }
        .profile-container {
            margin-top: 50px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php require_once("component/nav_user.php"); ?>  
    <div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>  
    <main class="container profile-container">
        <div class="card profile-card p-5">
            <div class="text-center">
                <?php if ($picture !== 'ไม่มีรูปภาพโปรไฟล์'): ?>
                    <img src="<?php echo htmlspecialchars($picture); ?>" class="profile-img" alt="profile img">
                <?php else: ?>
                    <p><?php echo $picture; ?></p>
                <?php endif; ?>
                <h1 class="mt-3"><?php echo htmlspecialchars($name); ?></h1>
                <p class="lead">อีเมล์ของคุณ: <?php echo $email === 'ไม่พบอีเมล์' ? 'ไม่มีอีเมล์' : htmlspecialchars($email); ?></p>
                <p class="lead">ระดับผู้ใช้: <?php echo htmlspecialchars($role); ?></p>
                <p class="lead">เวลาล็อกอินล่าสุด: <?php echo htmlspecialchars($loginTime); ?></p>
            </div>
        </div>
    </main>
    <?php include 'component/footer.php';?>
</body>
</html>
