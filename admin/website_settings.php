<?php
require_once('../db_connection.php');

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $site_nav = $_POST['site_nav'];
    $contact_email = $_POST['contact_email'];
    $announce = $_POST['announce'];
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE settings SET site_name = ?, site_nav = ?, contact_email = ?, announce = ?, maintenance_mode = ? WHERE id = 1");
    $stmt->execute([$site_name, $site_nav, $contact_email, $announce, $maintenance_mode]);

    echo "Settings updated!";
}

// ดึงข้อมูลการตั้งค่าเว็บไซต์
$stmt = $db->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-image: url('../assets/images/7788.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .blurry-img {
            filter: blur(10px);
        }
        .form-label {
            color: white;
        }
        h1 {
            color: white;
        }
    </style>
</head>
<body>
<?php require_once("../component/nav_admin.php"); ?>
<div class="container mt-5">
    <h1>Website Settings</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="site_name" class="form-label">Site Name (ชื่อแบนเนอร์)</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="site_nav" class="form-label">Site_navbar_name (ชื่อเว็ปไซต์)</label>
            <textarea class="form-control" id="site_nav" name="site_nav" required><?php echo htmlspecialchars($settings['site_nav']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label">Contact Email (อีเมล์)</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="announce" class="form-label">Announcement (ข้อความประกาศ)</label>
            <textarea class="form-control" id="announce" name="announce"><?php echo htmlspecialchars($settings['announce']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="maintenance_mode" class="form-label">Maintenance Mode (โหมดปิดปรับปรุง)</label>
            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php if ($settings['maintenance_mode']) echo 'checked'; ?>>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php include '../component/footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
