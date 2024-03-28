<?php
session_start();
require_once('LineLogin.php');

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WELCOME PAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <?php require_once("component/nav_user.php"); ?>    
    <main class="container">
        <div class="bg-white p-5 rounded">
            <h1>ยินดีต้อนรับคุณ, <?php echo $name; ?></h1>
            <?php if (!empty($email)): ?>
                <p class="lead">อีเมล์ของคุณ: <?php echo $email; ?></p>
            <?php else: ?>
                <p class="lead">ไม่พบอีเมล์</p>
            <?php endif; ?>
            <?php if (!empty($picture)): ?>
                <img src="<?php echo $picture; ?>" class="rounded" alt="profile img">
            <?php else: ?>
                <p>ไม่มีรูปภาพโปรไฟล์</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
