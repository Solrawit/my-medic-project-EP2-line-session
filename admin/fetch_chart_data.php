<?php
session_start();
require_once('../db_connection.php');

// Check if the request is valid
if (isset($_GET['feedback_id'])) {
    $feedbackId = intval($_GET['feedback_id']);

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch detailed feedback data
        $stmt = $pdo->prepare("SELECT * FROM user_feedback WHERE id = :feedback_id");
        $stmt->bindParam(':feedback_id', $feedbackId, PDO::PARAM_INT);
        $stmt->execute();
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($feedback) {
            // Prepare data for charts
            $chartData = [];
            $fields = [
                'design_appeal', 'ease_of_use', 'notification_accuracy', 'feature_functionality',
                'system_reliability', 'user_manual_completeness', 'page_load_speed', 'server_responsiveness',
                'server_memory_management', 'ocr_processing_speed', 'navigation_ease', 'user_friendly_interface',
                'responsive_design', 'accessibility'
            ];

            foreach ($fields as $field) {
                $chartData[$field] = $feedback[$field];
            }

            echo json_encode(['status' => 'success', 'data' => $chartData]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Feedback not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
