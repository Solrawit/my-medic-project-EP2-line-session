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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['ocr_text']) && isset($_POST['slot'])) {
    $id = $_POST['id'];
    $ocrText = $_POST['ocr_text'];
    $slot = $_POST['slot'];

    // Prepare the columns and values based on the slot selection
    if ($slot == 'slot1') {
        $column = 'ocr_scans_text';
    } elseif ($slot == 'slot2') {
        $column = 'ocr_scans_text2';
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['status' => 'error', 'message' => 'Invalid slot selected']);
        exit();
    }

    // Update OCR text in database
    try {
        $stmt = $pdo->prepare("UPDATE users SET $column = :ocr_text WHERE id = :id");
        $stmt->bindParam(':ocr_text', $ocrText);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Update Google Sheets via SheetDB API
        updateSheetDB($id, $column, $ocrText);

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
function updateSheetDB($id, $column, $ocrText) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v/id/' . $id; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Prepare data for upload to SheetDB
    $data_to_upload = [
        $column => $ocrText
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
        error_log("Failed to upload data to SheetDB.");
    }
}
?>
