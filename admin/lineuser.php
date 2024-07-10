<?php
require_once('../db_connection.php'); // Adjust path as necessary

session_start();

// Check if user is authenticated as admin
if (!isset($_SESSION['profile']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./welcome.php'); // Redirect unauthorized users
    exit;
}

// Fetch all users from the database
$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- Adjust favicon path -->
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

  .blurry-img {
    filter: blur(10px); /* Adjust as needed */
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
    <a href="manage_users.php" class="btn btn-danger me-2">
    <i class="fa fa-address-book" aria-hidden="true"></i> ย้อนกลับ
</a>
<br>
<br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Line User ID</th>
                <th>Display Name</th>
                <th>Email</th>
                <th>Login Time</th>
                <th>Role</th>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../component/footer.php'; ?> <!-- Adjust footer path -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
