<?php
require_once('../db_connection.php');

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $site_description = $_POST['site_description'];
    $contact_email = $_POST['contact_email'];

    $stmt = $db->prepare("UPDATE settings SET site_name = ?, site_description = ?, contact_email = ? WHERE id = 1");
    $stmt->execute([$site_name, $site_description, $contact_email]);

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
</head>
<body>
    <div class="container mt-5">
        <h1>Website Settings</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="site_name" class="form-label">Site Name</label>
                <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="site_description" class="form-label">Site Description</label>
                <textarea class="form-control" id="site_description" name="site_description" required><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="contact_email" class="form-label">Contact Email</label>
                <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
