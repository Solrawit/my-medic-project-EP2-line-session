<?php
require_once('../db_connection.php');

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome');
    exit;
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$stmt = $db->query("SELECT * FROM mdpj_user");
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

<?php require_once("../component/nav_admin.php"); ?>

<div class="container mt-5">
    <h1>Manage Users</h1>
    <a href="lineuser" class="btn btn-success me-2">
        <img src="../assets/images/line.png" alt="LINE Logo" width="20" height="20" class="me-1">
        ข้อมูลผู้ใช้LINE
    </a>
    <br><br>
    <table class="table table-striped">
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
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_username']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_surname']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_sex']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_level']); ?></td>
                    <td>
                        <!-- Button Dropdown สำหรับเลือกบทบาท -->
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
</div>

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
