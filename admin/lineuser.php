<?php
require_once('../db_connection.php'); // ปรับเปลี่ยนเส้นทางตามที่เหมาะสม

session_start();

// ตรวจสอบว่าผู้ใช้เป็น admin ที่มีการยืนยันตัวตนหรือไม่
if (!isset($_SESSION['profile']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./welcome.php'); // นำผู้ใช้ที่ไม่ได้รับอนุญาตไปที่หน้า welcome
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

    // แสดง SweetAlert เมื่อทำการอัปเดตสำเร็จ
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                showConfirmButton: false,
                timer: 1500
            });
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users LINE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- ปรับเปลี่ยนเส้นทางไอคอนตามที่เหมาะสม -->
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
            background-image: url('../assets/images/7788.jpg'); /* ปรับเปลี่ยนเส้นทางรูปภาพพื้นหลังตามที่เหมาะสม */
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

<div class="container mt-5">
    <h1>Manage Users LINE</h1>
    <a href="manage_users" class="btn btn-danger me-2">
        <i class="fa fa-address-book" aria-hidden="true"></i> ย้อนกลับ
    </a>
    <br><br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Line User ID</th>
                <th>Display Name</th>
                <th>Email</th>
                <th>Login Time</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['line_user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['login_time']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <!-- ปุ่มแก้ไขบทบาท Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                แก้ไขบทบาท
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['id']; ?>, 'admin')">Admin</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateRole(<?php echo $user['id']; ?>, 'user')">User</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

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

<?php include '../component/footer.php'; ?> <!-- ปรับเปลี่ยนเส้นทาง footer ตามที่เหมาะสม -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
