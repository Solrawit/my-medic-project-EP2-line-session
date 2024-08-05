<?php
require_once('../LineLogin.php');
require_once('../db_connection.php');
require_once('../TCPDF-main/tcpdf.php'); // Path to the TCPDF library

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Add Logo
$logoPath = '../admin/logopdf/logox.png'; // เปลี่ยน path เป็นตำแหน่งที่อยู่ของโลโก้
$logoWidth = 60; // ความกว้างของโลโก้
$logoHeight = 60; // ความสูงของโลโก้

// Get page width and set logo position
$pageWidth = $pdf->getPageWidth();
$x = ($pageWidth - $logoWidth) / 2; // คำนวณตำแหน่ง X เพื่อให้โลโก้อยู่กลางหน้า

// Add top margin for logo
$topMargin = 15; // ระยะห่างจากด้านบนของหน้า
$pdf->Image($logoPath, $x, $topMargin, $logoWidth, $logoHeight, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);

// Move below the logo to start content
$pdf->SetY($topMargin + $logoHeight + 10); // เพิ่มระยะห่างด้านล่างของโลโก้เพื่อไม่ให้ทับตารางข้อมูล

// Check if user ID is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Database connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch detailed feedback
        $stmt = $pdo->prepare("SELECT * FROM user_feedback WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$feedback) {
            die("Feedback not found");
        }

        // Generate PDF content
        $html = '
        <h2>Feedback Detail</h2>
        <h3>From. MEDICINERMUT.NET</h3>
        <h4>Mr. : ' . htmlspecialchars($feedback['display_name']) . ' </h4>
        <table border="1" cellpadding="4">
            <tr>
                <th>ID</th>
                <td>' . htmlspecialchars($feedback['id']) . '</td>
            </tr>
            <tr>
                <th>User</th>
                <td>' . htmlspecialchars($feedback['display_name']) . '</td>
            </tr>
            <tr>
                <th>Design Appeal</th>
                <td>' . htmlspecialchars($feedback['design_appeal']) . '</td>
            </tr>
            <tr>
                <th>Ease of Use</th>
                <td>' . htmlspecialchars($feedback['ease_of_use']) . '</td>
            </tr>
            <tr>
                <th>User Feedback Experience</th>
                <td>' . htmlspecialchars($feedback['user_feedback_experience']) . '</td>
            </tr>
            <tr>
                <th>Notification Accuracy</th>
                <td>' . htmlspecialchars($feedback['notification_accuracy']) . '</td>
            </tr>
            <tr>
                <th>Feature Functionality</th>
                <td>' . htmlspecialchars($feedback['feature_functionality']) . '</td>
            </tr>
            <tr>
                <th>System Reliability</th>
                <td>' . htmlspecialchars($feedback['system_reliability']) . '</td>
            </tr>
            <tr>
                <th>User Manual Completeness</th>
                <td>' . htmlspecialchars($feedback['user_manual_completeness']) . '</td>
            </tr>
            <tr>
                <th>Page Load Speed</th>
                <td>' . htmlspecialchars($feedback['page_load_speed']) . '</td>
            </tr>
            <tr>
                <th>Server Responsiveness</th>
                <td>' . htmlspecialchars($feedback['server_responsiveness']) . '</td>
            </tr>
            <tr>
                <th>Server Memory Management</th>
                <td>' . htmlspecialchars($feedback['server_memory_management']) . '</td>
            </tr>
            <tr>
                <th>OCR Processing Speed</th>
                <td>' . htmlspecialchars($feedback['ocr_processing_speed']) . '</td>
            </tr>
            <tr>
                <th>Navigation Ease</th>
                <td>' . htmlspecialchars($feedback['navigation_ease']) . '</td>
            </tr>
            <tr>
                <th>User Friendly Interface</th>
                <td>' . htmlspecialchars($feedback['user_friendly_interface']) . '</td>
            </tr>
            <tr>
                <th>Responsive Design</th>
                <td>' . htmlspecialchars($feedback['responsive_design']) . '</td>
            </tr>
            <tr>
                <th>Accessibility</th>
                <td>' . htmlspecialchars($feedback['accessibility']) . '</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>' . htmlspecialchars($feedback['evaluated_date']) . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('feedback_' . $id . '.pdf', 'D');

    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
} else {
    die("Invalid request");
}
