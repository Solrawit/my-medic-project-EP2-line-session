<?php
session_start();

// ตรวจสอบว่ามีข้อมูลโปรไฟล์ของผู้ใช้ใน session หรือไม่
if (isset($_SESSION['profile'])) {
    // ถ้ามี ก็ทำการลบ session ทั้งหมด
    $_SESSION = array();
    session_destroy();
}

// เปลี่ยนเส้นทางไปยังหน้า index.php
header('Location: index.php');
exit;
?>
