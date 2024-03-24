<?php
session_start();
require_once('LineLogin.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>

    <?php require_once("nav.php"); ?>

    <main class="container">
    <div class="bg-light p-5 rounded">
        <h1>ยินดีต้อนรับเข้าสู่หน้าหลักการเข้าสู่ระบบผ่านไลน์</h1>
        <p class="lead">กรุณาล็อกอินเพื่อเข้าสู่ระบบใช้งาน.</p>
        <!-- <p><a href="../index.php" class="btn btn-info">กลับไปหน้าแรก</a></p> -->
        <img src="../assets/images/line1.png" alt="..." width="300" height="300"> <!-- รูปline login acc -->
    </div>
    </main>

    
</body>
</html>