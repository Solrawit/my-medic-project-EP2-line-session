<?php
require_once('../db_connection.php');

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome');
    exit;
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
    $stmt = $db->prepare("SELECT * FROM mdpj_user WHERE user_name LIKE ? LIMIT ?, ?");
    $search_term_like = "%" . $search_term . "%";
    $stmt->bindValue(1, $search_term_like, PDO::PARAM_STR);
    $stmt->bindValue(2, $start, PDO::PARAM_INT);
    $stmt->bindValue(3, $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
} else {
    // ถ้าไม่มีการค้นหาใช้ SQL query ปกติ
    $stmt = $db->prepare("SELECT * FROM mdpj_user LIMIT ?, ?");
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
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- favicon image -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- เพิ่ม SweetAlert ไฟล์ -->
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
            color: black; /* ใช้เพื่อให้ข้อความยังคงมองเห็นได้ */
        }

        h1 {
            color: white;
        }
    </style>
</head>
<body>
<?php include '../component/nav_admin.php'; ?>
    <div class="container mt-5">
        <!-- ส่วนของหัวข้อและปุ่มเพิ่มเติม เช่น Manage Users และ ลิงก์ไปหน้า lineuser เป็นต้น -->
        <h1>Manage Users</h1>
        <a href="lineuser" class="btn btn-success me-2">
            <img src="../assets/images/line.png" alt="LINE Logo" width="20" height="20" class="me-1">
            ข้อมูลผู้ใช้LINE
        </a>
        <br><br>
        <!-- เพิ่มส่วนของ Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php
                // คำนวณจำนวนหน้าทั้งหมด
                $stmt_count = $db->query("SELECT COUNT(*) as total FROM mdpj_user");
                $total_rows = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
                $total_pages = ceil($total_rows / $rows_per_page);

                // แสดงปุ่มเพจ
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
                ?>
            </ul>
        </nav>
        <table class="table table-striped">
            <!-- ส่วนตารางที่มีข้อมูลผู้ใช้ -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Sex</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <!-- วนลูปแสดงผลข้อมูลของผู้ใช้ -->
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_username']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_surname']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_sex']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_level']); ?></td>
                        <td>
                            <!-- Dropdown สำหรับเลือกบทบาท -->
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $user['user_id']; ?>" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                    แก้ไขบทบาท
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $user['user_id']; ?>">
                                    <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['user_id']; ?>, 'admin')">Admin</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['user_id']; ?>, 'user')">User</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- เพิ่มส่วนของ Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php
                // คำนวณจำนวนหน้าทั้งหมด
                $stmt_count = $db->query("SELECT COUNT(*) as total FROM mdpj_user");
                $total_rows = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
                $total_pages = ceil($total_rows / $rows_per_page);

                // แสดงปุ่มเพจ
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
                ?>
            </ul>
        </nav>
    
<?php include '../component/footer.php'; ?>

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
