<?php
session_start();
require_once('LineLogin.php');

$line = new LineLogin();
$get = $_GET;

// ตรวจสอบว่าได้รับโค้ดและสถานะมาจาก LINE หรือไม่ห
if (!isset($get['code']) || !isset($get['state'])) {
    header('location: index');
    exit();
}

$code = $get['code'];
$state = $get['state'];

// เรียกใช้เมทอด token เพื่อรับ Access Token และตรวจสอบข้อมูลs
$token = $line->token($code, $state);

// ตรวจสอบว่าได้รับ Access Token หรือไม่
if (!$token || property_exists($token, 'error')) {
    header('location: index');
    exit();
}

// เมื่อได้ Access Token แล้ว รับข้อมูลโปรไฟล์ผู้ใช้
if ($token->access_token) {
    $profile = $line->profile($token->access_token);

    // ตรวจสอบว่าได้รับข้อมูลโปรไฟล์ผู้ใช้หรือไม่
    if ($profile && isset($profile->userId)) {
        $_SESSION['profile'] = $profile;
        header('location: welcome');
        exit();
    } else {
        // กรณีไม่ได้รับข้อมูลโปรไฟล์ผู้ใช้
        header('location: index');
        exit();
    }
} else {
    // กรณีไม่ได้รับ Access Token
    header('location: index');
    exit();
}
?>
