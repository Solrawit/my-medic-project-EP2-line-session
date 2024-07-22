<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mdpj_user";

// สร้างการเชื่อมต่อกับ MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL Query ที่ต้องการดึงข้อมูล
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result->num_rows > 0) {
    // ใช้ cURL สำหรับส่งข้อมูลไปยัง SheetDB
    while($row = $result->fetch_assoc()) {
        $data = array(
            "id" => $row["id"],
            "line_user_id" => $row["line_user_id"],
            "display_name" => $row["display_name"],
            "medicine_alert_time" => $row["medicine_alert_time"],
            "ocr_scans_text" => $row["ocr_scans_text"],
            "access_token" => $row["access_token"],
            "image" => $row["image"]
        );

        $ch = curl_init('https://sheetdb.io/api/v1/6sy4fvkc8go7v');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // ตั้งค่า headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $response = curl_exec($ch);
        if($response === FALSE){
            die(curl_error($ch));
        }

        curl_close($ch);
    }
} else {
    echo "0 results";
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
