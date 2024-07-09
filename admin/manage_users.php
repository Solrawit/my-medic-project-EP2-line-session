<?php
require_once('../db_connection.php');

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome.php');
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
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Users</h1>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
