<?php
session_start();

// ตรวจสอบว่ามีข้อมูลโปรไฟล์ของผู้ใช้ใน session หรือไม่
if (isset($_SESSION['profile'])) {
    // ลบค่าตัวแปรใน session variables
    $_SESSION['profile'] = null;  // หรือ $_SESSION = array(); ก็ได้
    session_destroy();  // ทำลาย session ทั้งหมด
}

// เปลี่ยนเส้นทางไปยังหน้า index.php
header('Location: index.php');
exit;
?>
