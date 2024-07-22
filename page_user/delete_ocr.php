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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete OCR data from database
    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET ocr_scans_text = NULL, ocr_image_data = NULL, medicine_alert_time = Null, access_token = Null, image = Null
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Update Google Sheets via SheetDB API
        updateSheetDB($id);

        // Redirect back to history.php after deletion
        header("Location: history.php");
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
function updateSheetDB($id) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v/id/' . $id; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Prepare data for upload to SheetDB
    $data_to_upload = [
        "ocr_scans_text" => NULL,
        "ocr_image_data" => NULL,
        "medicine_alert_time" => Null, 
        "access_token" => Null, 
        "image" => Null
    ];

    // Send data to SheetDB API via cURL
    $ch = curl_init($sheetdb_api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); // Use PATCH for updating existing data
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_upload));
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("Failed to update data in SheetDB.");
    }
}
?>
