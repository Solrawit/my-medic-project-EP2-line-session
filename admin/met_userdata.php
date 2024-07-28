<?php
require_once('../db_connection.php');
include '../timeout.php';

session_start();

// Fetch user profile from session
$profile = $_SESSION['profile'];

// Sanitize and prepare user details
$name = isset($profile->displayName) ? htmlspecialchars($profile->displayName, ENT_QUOTES, 'UTF-8') : 'ไม่พบชื่อ';
$email = isset($profile->email) ? htmlspecialchars($profile->email, ENT_QUOTES, 'UTF-8') : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? htmlspecialchars($profile->pictureUrl, ENT_QUOTES, 'UTF-8') : 'ไม่มีรูปภาพโปรไฟล์';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['profile']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./welcome');
    exit;
}

// Pagination setup
$limit = 8; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of records
$totalStmt = $db->query("SELECT COUNT(*) FROM users");
$totalRows = $totalStmt->fetchColumn();

// Fetch user data with limit and offset
$stmt = $db->prepare("SELECT id, picture_url, display_name, line_user_id, medicine_alert_time, ocr_scans_text, ocr_image_data, access_token, image, medicine_alert_time2, ocr_scans_text2, ocr_image_data2, access_token2, image2 FROM users LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-image: url('../assets/images/7788.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 1.5rem;
            color: #343a40;
            text-align: center; /* Center the header */
        }

        .table {
            margin-bottom: 2rem;
            background-color: rgba(0, 68, 255, 0.5); /* Blue with 50% opacity */
        }

        .table th {
            background-color: #007bff; /* Blue background for table headers */
            color: white; /* White text color for table headers */
        }

        .table td {
            vertical-align: middle;
        }

        .btn-info {
            background-color: #0044cc; /* Blue background for buttons */
            border: none;
        }

        .btn-info:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .modal-header {
            background-color: #007bff; /* Blue background for modal headers */
            color: white; /* White text color for modal headers */
        }

        .modal-body img {
            max-width: 90%;
            border-radius: 8px;
        }

        .pagination {
            justify-content: center;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-link {
            border-radius: 0.25rem;
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
<div class="container mt-5">
    <h1>User Data Medicine</h1>
    <h3>ข้อมูลการแจ้งเตือนผู้ใช้ยา</h3>
    <br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile Picture</th>
                <th>Line Name</th>
                <th>LINE User ID</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td>
                        <?php if ($user['picture_url']): ?>
                            <img src="<?php echo htmlspecialchars($user['picture_url']); ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['line_user_id']); ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo htmlspecialchars($user['id']); ?>">
                            ดูข้อมูล
                        </button>
                    </td>
                    <!-- Details Modal -->
                    <div class="modal fade" id="detailsModal<?php echo htmlspecialchars($user['id']); ?>" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailsModalLabel">รายละเอียดสำหรับ <?php echo htmlspecialchars($user['display_name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h5>ยาตัวที่ 1</h5>
                                    <p><strong>Medicine Alert Time:</strong> <?php echo htmlspecialchars($user['medicine_alert_time']); ?></p>
                                    <p><strong>OCR Scans Text:</strong> <?php echo htmlspecialchars($user['ocr_scans_text']); ?></p>
                                    <p><strong>OCR Image Data:</strong> <?php echo htmlspecialchars($user['ocr_image_data']); ?></p>
                                    <p><strong>Access Token:</strong> <?php echo htmlspecialchars($user['access_token']); ?></p>
                                    <p><strong>Image:</strong></p>
                                    <?php if ($user['image']): ?>
                                        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Medicine Image" class="img-fluid">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>

                                    <hr>

                                    <h5>ยาตัวที่ 2</h5>
                                    <p><strong>Medicine Alert Time 2:</strong> <?php echo htmlspecialchars($user['medicine_alert_time2']); ?></p>
                                    <p><strong>OCR Scans Text 2:</strong> <?php echo htmlspecialchars($user['ocr_scans_text2']); ?></p>
                                    <p><strong>OCR Image Data 2:</strong> <?php echo htmlspecialchars($user['ocr_image_data2']); ?></p>
                                    <p><strong>Access Token 2:</strong> <?php echo htmlspecialchars($user['access_token2']); ?></p>
                                    <p><strong>Image 2:</strong></p>
                                    <?php if ($user['image2']): ?>
                                        <img src="<?php echo htmlspecialchars($user['image2']); ?>" alt="Medicine Image 2" class="img-fluid">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
<!-- Pagination -->
<nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= ceil($totalRows / $limit); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < ceil($totalRows / $limit)): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
