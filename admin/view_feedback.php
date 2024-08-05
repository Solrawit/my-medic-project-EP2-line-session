<?php
session_start();
require_once('../LineLogin.php');
require_once('../db_connection.php');
include '../timeout.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['profile']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../index.php");
    exit();
}

// Fetch user profile from session
$profile = $_SESSION['profile'];

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch detailed feedback
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT * FROM user_feedback WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$feedback) {
            die("Feedback not found");
        }
    } else {
        die("Invalid request");
    }
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Feedback Detail</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($feedback['id']); ?></td>
        </tr>
        <tr>
            <th>User</th>
            <td><?php echo htmlspecialchars($feedback['display_name']); ?></td>
        </tr>
        <tr>
            <th>Design Appeal</th>
            <td><?php echo htmlspecialchars($feedback['design_appeal']); ?></td>
        </tr>
        <tr>
            <th>Ease of Use</th>
            <td><?php echo htmlspecialchars($feedback['ease_of_use']); ?></td>
        </tr>
        <tr>
            <th>User Feedback Experience</th>
            <td><?php echo htmlspecialchars($feedback['user_feedback_experience']); ?></td>
        </tr>
        <tr>
            <th>Notification Accuracy</th>
            <td><?php echo htmlspecialchars($feedback['notification_accuracy']); ?></td>
        </tr>
        <tr>
            <th>Feature Functionality</th>
            <td><?php echo htmlspecialchars($feedback['feature_functionality']); ?></td>
        </tr>
        <tr>
            <th>System Reliability</th>
            <td><?php echo htmlspecialchars($feedback['system_reliability']); ?></td>
        </tr>
        <tr>
            <th>User Manual Completeness</th>
            <td><?php echo htmlspecialchars($feedback['user_manual_completeness']); ?></td>
        </tr>
        <tr>
            <th>Page Load Speed</th>
            <td><?php echo htmlspecialchars($feedback['page_load_speed']); ?></td>
        </tr>
        <tr>
            <th>Server Responsiveness</th>
            <td><?php echo htmlspecialchars($feedback['server_responsiveness']); ?></td>
        </tr>
        <tr>
            <th>Server Memory Management</th>
            <td><?php echo htmlspecialchars($feedback['server_memory_management']); ?></td>
        </tr>
        <tr>
            <th>OCR Processing Speed</th>
            <td><?php echo htmlspecialchars($feedback['ocr_processing_speed']); ?></td>
        </tr>
        <tr>
            <th>Navigation Ease</th>
            <td><?php echo htmlspecialchars($feedback['navigation_ease']); ?></td>
        </tr>
        <tr>
            <th>User Friendly Interface</th>
            <td><?php echo htmlspecialchars($feedback['user_friendly_interface']); ?></td>
        </tr>
        <tr>
            <th>Responsive Design</th>
            <td><?php echo htmlspecialchars($feedback['responsive_design']); ?></td>
        </tr>
        <tr>
            <th>Accessibility</th>
            <td><?php echo htmlspecialchars($feedback['accessibility']); ?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo htmlspecialchars($feedback['evaluated_date']); ?></td>
        </tr>
    </table>
    <br>
    <a href="admin_feedback" class="btn btn-primary">ย้อนกลับ</a>
    <a href="generate_pdf.php?id=<?php echo $feedback['id']; ?>" class="btn btn-secondary">Download PDF</a>
</div>
</body>
</html>
