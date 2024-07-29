<?php
// db_connection.php

$host = 'localhost';
$dbname = 'mdpj_user';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Function to get site settings
function getSiteSettings($db) {
    $sql = "SELECT * FROM settings";
    $stmt = $db->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//Set ว/ด/ป เวลา ให้เป็นของประเทศไทย
date_default_timezone_set('Asia/Bangkok');
?>
