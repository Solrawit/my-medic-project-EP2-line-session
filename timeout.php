<?php
// ตั้งค่าช่วงเวลาหมดอายุของเซสชัน (เช่น 30 นาที)
$session_timeout = 1800; // 1800 วินาที หรือ 30 นาที

// เริ่มต้นเซสชัน
// session_start();

// ตรวจสอบว่ามีการตั้งค่าช่วงเวลาหมดอายุหรือไม่
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // เซสชันหมดอายุ
    session_unset();     // ล้างข้อมูลเซสชัน
    session_destroy();   // ทำลายเซสชัน
    header('Location: ../index'); // เปลี่ยนเส้นทางไปยังหน้าล็อกอิน
    exit();
}
$_SESSION['last_activity'] = time(); // อัปเดตเวลาล่าสุดของกิจกรรม
?>
