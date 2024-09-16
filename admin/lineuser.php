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

if (!isset($_SESSION['profile']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../welcome');
    exit;
}

// เรียกดูข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll();

// การบันทึกการเปลี่ยนแปลงของบทบาท
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    // อัปเดตบทบาทผู้ใช้ในฐานข้อมูล
    $update_stmt = $db->prepare("UPDATE users SET role = :role WHERE id = :id");
    $update_stmt->execute(['role' => $role, 'id' => $user_id]);
}

// กำหนดจำนวนแถวที่แสดงในแต่ละหน้า
$rows_per_page = 10;

// หากมีการระบุหน้าปัจจุบันใน URL ให้ใช้ค่านั้น ไม่งั้นใช้หน้าแรกเป็นค่าเริ่มต้น
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// ตรวจสอบการค้นหา
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// คำนวณตำแหน่งเริ่มต้นของการดึงข้อมูล
$start = ($current_page - 1) * $rows_per_page;

// ดึงข้อมูลผู้ใช้ที่ต้องการแสดงในหน้าปัจจุบัน โดยใช้ LIMIT
if (!empty($search_term)) {
    // ถ้ามีการค้นหาใช้ SQL query ที่มีเงื่อนไข LIKE
    $stmt = $db->prepare("SELECT * FROM users WHERE display_name LIKE ? LIMIT ?, ?");
    $search_term_like = "%" . $search_term . "%";
    $stmt->bindValue(1, $search_term_like, PDO::PARAM_STR);
    $stmt->bindValue(2, $start, PDO::PARAM_INT);
    $stmt->bindValue(3, $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
} else {
    // ถ้าไม่มีการค้นหาใช้ SQL query ปกติ
    $stmt = $db->prepare("SELECT * FROM users LIMIT ?, ?");
    $stmt->bindValue(1, $start, PDO::PARAM_INT);
    $stmt->bindValue(2, $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
}

$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users LINE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <style>
        body {
            position: relative;
            font-family: 'Sarabun', sans-serif;
            padding: 0;
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

        .table-striped {
            background-color: white;
        }

        .table-striped th,
        .table-striped td {
            background-color: white;
            color: black;
        }

        h1 {
            color: white;
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
        .profile-pic {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    </style>
</head>
<body>
<div class="sidebar">
        <?php require_once("../component/nav_admin.php"); ?>
    </div>
    <div class="main-content">
<div class="container mt-5">
    <h1>Manage Users LINE</h1>
    <a href="manage_users" class="btn btn-danger me-2">
        <i class="fa fa-address-book" aria-hidden="true"></i> ย้อนกลับ
    </a>
    <a href="lineuser" class="btn btn-warning me-2">
        <i class="fa fa-refresh" aria-hidden="true"></i> รีเฟรช
    </a>
    <br><br>

    <!-- เพิ่มฟอร์มค้นหา -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="ค้นหาชื่อผู้ใช้..." name="search" value="<?php echo htmlspecialchars($search_term); ?>">
            <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
        </div>
    </form>

    <table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Line User ID</th>
            <th>Display Name</th>
            <th>Profile Picture</th>
            <th>Login Time</th>
            <th>Role</th>
            <!-- ปิดการใช้งานส่วนแก้ไขบทบาท <th>Action</th> -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['line_user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                <td>
                    <?php if (!empty($user['picture_url'])): ?>
                        <img src="<?php echo htmlspecialchars($user['picture_url']); ?>" class="profile-pic" alt="Profile Picture">
                    <?php else: ?>
                        <img src="../assets/images/default-avatar.png" class="profile-pic" alt="Default Avatar">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['login_time']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    
                    <!-- Dropdown สำหรับเลือกบทบาท -->
                     <!-- ปิดการใช้งานส่วนแก้ไขบทบาท 
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $user['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            แก้ไขบทบาท
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $user['id']; ?>">
                            <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['id']; ?>, 'user')">User</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['id']; ?>, 'admin')">Admin</a></li>
                        </ul>
                    </div>
                     ปิดการใช้งานส่วนแก้ไขบทบาท -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php
            // คำนวณจำนวนหน้าทั้งหมด
            if (!empty($search_term)) {
                // ถ้ามีการค้นหาใช้ SQL query ที่มีเงื่อนไข LIKE
                $stmt_count = $db->prepare("SELECT COUNT(*) as total FROM users WHERE display_name LIKE ?");
                $stmt_count->bindValue(1, $search_term_like, PDO::PARAM_STR);
            } else {
                // ถ้าไม่มีการค้นหาใช้ SQL query ปกติ
                $stmt_count = $db->query("SELECT COUNT(*) as total FROM users");
            }
            $stmt_count->execute();
            $total_rows = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
            $total_pages = ceil($total_rows / $rows_per_page);

            // แสดงลิงก์ Pagination
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '&search=' . urlencode($search_term) . '">' . $i . '</a></li>';
            }
            ?>
        </ul>
    </nav>
</div>

<?php include '../component/footer.php'; ?>
        </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ฟังก์ชันอัปเดตบทบาทผู้ใช้และแสดง SweetAlert
    function updateRole(userId, role) {
        // ส่งข้อมูลผ่าน AJAX ไปยัง PHP script ที่จัดการอัปเดตบทบาท
        var formData = new FormData();
        formData.append('user_id', userId);
        formData.append('role', role);

        fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            // แสดง SweetAlert เมื่อทำการอัปเดตสำเร็จ
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                showConfirmButton: false,
                timer: 1500
            });
            // รีโหลดหน้าเพื่อแสดงการเปลี่ยนแปลง
            setTimeout(() => {
                location.reload();
            }, 1500);
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาดในการอัปเดต: ', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถอัปเดตบทบาทได้',
            });
        });
    }
</script>
</body>
</html>
