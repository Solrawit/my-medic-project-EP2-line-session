<?php
// เชื่อมต่อ MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mdpj_user";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // หยุดการทำงานถ้าไม่สามารถเชื่อมต่อฐานข้อมูลได้
}

// สร้าง URL สำหรับ SheetDB API โดยใช้ SheetDB API URL และ API Key
$sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v'; // เปลี่ยนเป็น URL ของ SheetDB API ที่ใช้งานจริง
$sheetdb_api_key = '6sy4fvkc8go7v'; // เปลี่ยนเป็น API Key ของ SheetDB ที่ใช้งานจริง

// ดึงข้อมูลจาก MySQL
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับอัปโหลดไปยัง SheetDB
$data_to_upload = [];
foreach ($rows as $row) {
    $data_to_upload[] = [
        "id" => $row['id'],
        "line_user_id" => $row['line_user_id'],
        "display_name" => $row['display_name'],
        "picture_url" => $row['picture_url'],
        "email" => $row['email'],
        "login_time" => $row['login_time'],
        "role" => $row['role'],
        "medicine_alert_time" => $row['medicine_alert_time'],
        "medicine_alert_message" => $row['medicine_alert_message'],
        "ocr_scans_text" => $row['ocr_scans_text'],
        "ocr_image_data" => $row['ocr_image_data'],
        "password" => $row['password']
    ];
}

// ส่งข้อมูลไปยัง SheetDB API โดยใช้ cURL
$ch = curl_init($sheetdb_api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $sheetdb_api_key,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_upload));
$response = curl_exec($ch);
curl_close($ch);

// ตรวจสอบการอัปโหลด
if ($response === false) {
    echo "Failed to upload data to SheetDB.";
} else {
    echo "Data uploaded successfully!";
}
?>
