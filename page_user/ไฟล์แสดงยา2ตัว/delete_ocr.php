<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['profile'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Database connection
require_once('../LineLogin.php'); // Assuming this file handles Line login and session

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mdpj_user');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create PDO instance
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Validate input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['slot'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $slot = filter_var($_POST['slot'], FILTER_SANITIZE_STRING);

    // Determine the columns to update based on the slot
    if ($slot === 'slot1') {
        $columns = [
            'ocr_scans_text' => NULL,
            'ocr_image_data' => NULL,
            'medicine_alert_time' => NULL,
            'access_token' => NULL,
            'image' => NULL
        ];
    } elseif ($slot === 'slot2') {
        $columns = [
            'ocr_scans_text2' => NULL,
            'ocr_image_data2' => NULL,
            'medicine_alert_time2' => NULL,
            'access_token2' => NULL,
            'image2' => NULL
        ];
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['status' => 'error', 'message' => 'Invalid slot']);
        exit();
    }

    // Update OCR data in database
    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET " . implode(', ', array_map(function($col) {
                return "$col = :$col";
            }, array_keys($columns))) . "
            WHERE id = :id
        ");

        // Bind parameters
        foreach ($columns as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Update Google Sheets via SheetDB API
        $updateResult = updateSheetDB($id, $slot);

        // Redirect back to history.php after deletion
        header("Location: history.php");
        exit();
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

// Function to update Google Sheets via SheetDB API
function updateSheetDB($id, $slot) {
    $sheetdb_api_url = 'https://sheetdb.io/api/v1/6sy4fvkc8go7v/id/' . $id; // Change to your SheetDB API URL
    $sheetdb_api_key = '6sy4fvkc8go7v'; // Change to your SheetDB API Key

    // Prepare data for upload to SheetDB based on slot
    $data_to_upload = $slot === 'slot1' ? [
        "ocr_scans_text" => null,
        "ocr_image_data" => null,
        "medicine_alert_time" => null,
        "access_token" => null,
        "image" => null
    ] : [
        "ocr_scans_text2" => null,
        "ocr_image_data2" => null,
        "medicine_alert_time2" => null,
        "access_token2" => null,
        "image2" => null
    ];

    // Send data to SheetDB API via cURL
    $ch = curl_init($sheetdb_api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $sheetdb_api_key // Use API key if required
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); // Use PATCH for updating existing data
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_upload));
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("Failed to update data in SheetDB: " . $error);
        return ['status' => 'error', 'message' => 'Failed to update data in SheetDB'];
    }

    return ['status' => 'success', 'response' => json_decode($response, true)];
}
?>
