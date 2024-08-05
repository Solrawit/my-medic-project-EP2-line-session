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

// Sanitize and prepare user details
$name = isset($profile->displayName) ? htmlspecialchars($profile->displayName, ENT_QUOTES, 'UTF-8') : 'ไม่พบชื่อ';
$email = isset($profile->email) ? htmlspecialchars($profile->email, ENT_QUOTES, 'UTF-8') : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? htmlspecialchars($profile->pictureUrl, ENT_QUOTES, 'UTF-8') : 'ไม่มีรูปภาพโปรไฟล์';

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the user_feedback table if it does not exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `user_feedback` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `line_user_id` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
        `display_name` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_general_ci',
        `design_appeal` INT(1) NOT NULL,
        `ease_of_use` INT(1) NOT NULL,
        `user_feedback_experience` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
        `notification_accuracy` INT(1) NOT NULL,
        `feature_functionality` INT(1) NOT NULL,
        `system_reliability` INT(1) NOT NULL,
        `user_manual_completeness` INT(1) NOT NULL,
        `page_load_speed` INT(1) NOT NULL,
        `server_responsiveness` INT(1) NOT NULL,
        `server_memory_management` INT(1) NOT NULL,
        `ocr_processing_speed` INT(1) NOT NULL,
        `navigation_ease` INT(1) NOT NULL,
        `user_friendly_interface` INT(1) NOT NULL,
        `responsive_design` INT(1) NOT NULL,
        `accessibility` INT(1) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
        `evaluated_date` DATE NULL DEFAULT NULL,
        PRIMARY KEY (`id`) USING BTREE
    ) COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;";
    $pdo->exec($createTableSQL);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

// Pagination setup
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$start = ($page - 1) * $limit;

// Fetch feedback data with date filter and limit
$dateFilter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateFilter = htmlspecialchars($dateFilter, ENT_QUOTES, 'UTF-8');

$stmt = $pdo->prepare("SELECT * FROM user_feedback WHERE DATE(evaluated_date) = :dateFilter ORDER BY id DESC LIMIT :start, :limit");
$stmt->bindParam(':dateFilter', $dateFilter, PDO::PARAM_STR);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total count for pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM user_feedback WHERE DATE(evaluated_date) = :dateFilter");
$stmtCount->bindParam(':dateFilter', $dateFilter, PDO::PARAM_STR);
$stmtCount->execute();
$totalFeedbacks = $stmtCount->fetchColumn();
$totalPages = ceil($totalFeedbacks / $limit);

// Handle delete all feedback action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_all'])) {
    $confirmDelete = $_POST['confirm_delete_all'];
    if ($confirmDelete === 'ยืนยัน') {
        try {
            $deleteStmt = $pdo->prepare("DELETE FROM user_feedback WHERE DATE(evaluated_date) = :dateFilter");
            $deleteStmt->bindParam(':dateFilter', $dateFilter, PDO::PARAM_STR);
            $deleteStmt->execute();
            // Redirect back to the same page after deletion
            header("Location: {$_SERVER['PHP_SELF']}?date=" . urlencode($dateFilter));
            exit();
        } catch (PDOException $e) {
            die("Error deleting feedback: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Data Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" href="../favicon.png">
    <style>
        body {
            position: relative;
            font-family: 'Sarabun', sans-serif;
            padding: 0px 0px;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../assets/images/bg4.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .sidebar {
            flex: 0 0 250px;
            background-color: #f8f9fa;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <?php require_once("../component/nav_admin.php"); ?>
</div>
<div class="main-content">
    <div class="container">
        <h2 class="mt-5">Admin Feedback Page</h2>
        <h4 class="mt-1">ข้อมูลการประเมิน</h4>
        <div class="mb-3">
            <p><strong>ยินดีต้อนรับ Admin :</strong> <?php echo $name; ?></p>
        </div>
        
        <!-- Date Filter -->
        <form method="get" class="mb-3">
            <label for="dateFilter" class="form-label">กรองข้อมูลการประเมิน วัน/เดือน/ปี:</label>
            <input type="date" id="dateFilter" name="date" value="<?php echo $dateFilter; ?>" class="form-control" required>
            <button type="submit" class="btn btn-primary mt-2">กรองข้อมูลตาม วัน/เดือน/ปี</button>
        </form>
        <!-- Delete All Feedback Form -->
        <form method="post" class="mt-3" id="delete-all-form">
            <button type="button" id="delete-all-btn" class="btn btn-danger">ลบข้อมูลการประเมินทั้งหมด</button>
            <input type="hidden" name="confirm_delete_all" id="confirm_delete_all">
        </form>
        
        <div class="row">
            <div class="col-md-12">
                <h3>Feedback Table</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Design Appeal</th>
                            <th>Ease of Use</th>
                            <th>User Feedback Experience</th>
                            <th>Notification Accuracy</th>
                            <th>Feature Functionality</th>
                            <th>System Reliability</th>
                            <th>User Manual Completeness</th>
                            <th>Page Load Speed</th>
                            <th>Server Responsiveness</th>
                            <th>Server Memory Management</th>
                            <th>OCR Processing Speed</th>
                            <th>Navigation Ease</th>
                            <th>User Friendly Interface</th>
                            <th>Responsive Design</th>
                            <th>Accessibility</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['display_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['design_appeal']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['ease_of_use']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['user_feedback_experience']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['notification_accuracy']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['feature_functionality']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['system_reliability']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['user_manual_completeness']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['page_load_speed']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['server_responsiveness']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['server_memory_management']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['ocr_processing_speed']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['navigation_ease']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['user_friendly_interface']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['responsive_design']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['accessibility']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['evaluated_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?date=<?php echo urlencode($dateFilter); ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?date=<?php echo urlencode($dateFilter); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?date=<?php echo urlencode($dateFilter); ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#delete-all-btn').on('click', function() {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูลทั้งหมด',
                text: "ข้อมูลการประเมินทั้งหมดในวันที่นี้จะถูกลบออก!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#confirm_delete_all').val('ยืนยัน');
                    $('#delete-all-form').submit();
                }
            });
        });
    });
</script>
</body>
</html>

