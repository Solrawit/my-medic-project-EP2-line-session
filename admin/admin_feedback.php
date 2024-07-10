<?php
session_start();
require_once('../LineLogin.php');
require_once('../db_connection.php');

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

$stmt = $pdo->prepare("SELECT * FROM feedback WHERE DATE(created_at) = :dateFilter ORDER BY id DESC LIMIT :start, :limit");
$stmt->bindParam(':dateFilter', $dateFilter, PDO::PARAM_STR);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total count for pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM feedback WHERE DATE(created_at) = :dateFilter");
$stmtCount->bindParam(':dateFilter', $dateFilter, PDO::PARAM_STR);
$stmtCount->execute();
$totalFeedbacks = $stmtCount->fetchColumn();
$totalPages = ceil($totalFeedbacks / $limit);

// Handle delete all feedback action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_all'])) {
    $confirmDelete = $_POST['confirm_delete_all'];
    if ($confirmDelete === 'ยืนยัน') {
        try {
            $deleteStmt = $pdo->prepare("DELETE FROM feedback WHERE DATE(created_at) = :dateFilter");
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
    <title>Admin Feedback Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php require_once("../component/nav_admin.php"); ?>
    <div class="container">
        <h2 class="mt-5">Admin Feedback Page</h2>
        <h4 class="mt-1">ข้อมูลการประเมิน</h4>
        <div class="mb-3">
            <p><strong>ยินดีต้อนรับ Admin :</strong> <?php echo $name; ?></p>
            <!-- <p><strong>Admin Email:</strong> <?php echo $email; ?></p> -->
            <!-- Add admin profile picture display if needed -->
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
        
        <!-- Feedback Chart ENGLISH -->
        <div class="row">
            <div class="col-md-6">
                <h3>Feedback Ratings</h3>
                <canvas id="feedbackChartEN" width="400" height="200"></canvas>
            </div>
            <div class="col-md-6">
                <h3>Feedback Table</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Smoothness</th>
                            <th>Stability (Website)</th>
                            <th>Stability (System)</th>
                            <th>Ease of Use</th>
                            <th>Complexity</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['display_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['smoothness']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['stability_website']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['stability_system']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['ease_of_use']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['complexity']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Feedback Chart THAI -->
        <div class="row">
            <div class="col-md-6">
                <h3>การประเมินความพึงพอใจ</h3>
                <canvas id="feedbackChartTH" width="400" height="200"></canvas>
            </div>
            <div class="col-md-6">
                <h3>ตารางการประเมิน</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ไอดี</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>ความลื่นไหล</th>
                            <th>ความเสถียร (เว็บไซต์)</th>
                            <th>ความเสถียร (ระบบ)</th>
                            <th>ความง่ายในการใช้งาน</th>
                            <th>ความซับซ้อน</th>
                            <th>วันที่</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['display_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['smoothness']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['stability_website']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['stability_system']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['ease_of_use']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['complexity']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    
    <!-- Chart Script -->
    <script>
        // Prepare data for Chart.js
        const smoothnessData = <?php echo json_encode(array_column($feedbacks, 'smoothness')); ?>;
        const stabilityWebsiteData = <?php echo json_encode(array_column($feedbacks, 'stability_website')); ?>;
        const stabilitySystemData = <?php echo json_encode(array_column($feedbacks, 'stability_system')); ?>;
        const easeOfUseData = <?php echo json_encode(array_column($feedbacks, 'ease_of_use')); ?>;
        const complexityData = <?php echo json_encode(array_column($feedbacks, 'complexity')); ?>;
        
        const labels = ['ความราบรื่น', 'ความเสถียร (เว็บไซต์)', 'ความเสถียร (ระบบ)', 'ความง่ายในการใช้งาน', 'ความซับซ้อน'];
        const dataEN = [smoothnessData, stabilityWebsiteData, stabilitySystemData, easeOfUseData, complexityData];
        const dataTH = [smoothnessData, stabilityWebsiteData, stabilitySystemData, easeOfUseData, complexityData];
        
        // Create bar charts using Chart.js
        const ctxEN = document.getElementById('feedbackChartEN').getContext('2d');
        const ctxTH = document.getElementById('feedbackChartTH').getContext('2d');
        
        const feedbackChartEN = new Chart(ctxEN, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Feedback Ratings',
                    data: dataEN,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 1,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Rating'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Criteria'
                        }
                    }
                }
            }
        });

        const feedbackChartTH = new Chart(ctxTH, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'การประเมินความพึงพอใจ',
                    data: dataTH,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 1,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Rating'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Criteria'
                        }
                    }
                }
            }
        });

        // Handle click event on delete all button
        document.getElementById('delete-all-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'คุณแน่ใจใช่ไหม?',
                text: "กรุณาพิมพ์ 'ยืนยัน' เพื่อยืนยันการลบข้อมูลการประเมินทั้งหมด!",
                input: 'text',
                inputPlaceholder: 'พิมพ์ "ยืนยัน"',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบทั้งหมด',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (value !== 'ยืนยัน') {
                        return 'คุณต้องพิมพ์ "ยืนยัน" เพื่อยืนยันการลบ!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set the input value to confirm deletion
                    document.getElementById('confirm_delete_all').value = 'ยืนยัน';
                    // Submit the form
                    document.getElementById('delete-all-form').submit();
                }
            });
        });
    </script>
    
</body>
</html>
