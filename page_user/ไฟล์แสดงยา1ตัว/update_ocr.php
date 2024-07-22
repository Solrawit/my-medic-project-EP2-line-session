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
        updateSheetDB($id, $ocrText);

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
function updateSheetDB($id, $ocrText) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v/id/' . $id; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Prepare data for upload to SheetDB
    $data_to_upload = [
        "ocr_scans_text" => $ocrText
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
