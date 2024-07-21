<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['profile'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

// Database connection
require_once('../LineLogin.php'); // Assuming this file handles Line login and session
$host = 'localhost';
$db = 'mdpj_user';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Database error: " . $e->getMessage();
    exit();
}

// Validate input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['ocr_text'])) {
    $id = $_POST['id'];
    $ocrText = $_POST['ocr_text'];

    // Update OCR text in database
    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET ocr_scans_text = :ocr_text
            WHERE id = :id
        ");
        $stmt->bindParam(':ocr_text', $ocrText);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Update Google Sheets via SheetDB API
        updateSheetDB($pdo);

        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit();
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Database error: " . $e->getMessage();
        exit();
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

// Function to update Google Sheets via SheetDB API
function updateSheetDB($pdo) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v'; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Fetch all user data from database
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for upload to SheetDB
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

    // Send data to SheetDB API via cURL
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

    if ($response === false) {
        error_log("Failed to upload data to SheetDB.");
    }
}
?>
