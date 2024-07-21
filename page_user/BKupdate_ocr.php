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
?>
