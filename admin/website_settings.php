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
    
    // ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพหรือไม่
    if (isset($_FILES['image_path']) && $_FILES['image_path']['size'] > 0) {
        $target_dir = "../component/uploads/"; // โฟลเดอร์ที่เก็บไฟล์ที่อัปโหลด
        $target_file = $target_dir . basename($_FILES['image_path']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // เช็คว่าไฟล์ที่อัปโหลดเป็นรูปภาพหรือไม่
        $check = getimagesize($_FILES['image_path']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        
        // เช็คว่าไฟล์มีอยู่จริงหรือไม่
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        
        // เช็คขนาดไฟล์
        if ($_FILES['image_path']['size'] > 5000000) {
            echo "ขออภัยไฟล์รูปภาพมีขนาดใหญ่เกินไป";
            $uploadOk = 0;
        }
        
        // อัปโหลดไฟล์
        if ($uploadOk == 0) {
            echo "มีไฟล์ที่มีชื่อเดียวกันอยู่แล้วในระบบกรุณาเปลี่ยนชื่อไฟล์";
        } else {
            if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_file)) {
                echo "The file ". htmlspecialchars(basename($_FILES['image_path']['name'])). " has been uploaded.";
                
                // บันทึกที่อยู่ของไฟล์ในฐานข้อมูล
                $image_path = $target_file;
                $stmt = $db->prepare("UPDATE settings SET site_name = ?, site_nav = ?, contact_email = ?, announce = ?, maintenance_mode = ?, image_path = ? WHERE id = 1");
                $stmt->execute([$site_name, $site_nav, $contact_email, $announce, $maintenance_mode, $image_path]);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // ถ้าไม่มีการอัปโหลดไฟล์ ให้ใช้ค่าเดิมจากฐานข้อมูล
        $stmt = $db->prepare("UPDATE settings SET site_name = ?, site_nav = ?, contact_email = ?, announce = ?, maintenance_mode = ? WHERE id = 1");
        $stmt->execute([$site_name, $site_nav, $contact_email, $announce, $maintenance_mode]);
    }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
    <form method="POST" enctype="multipart/form-data">
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
        <div class="mb-3">
            <label for="image_path" class="form-label">Upload Image</label>
            <input type="file" class="form-control" id="image_path" name="image_path">
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php include '../component/footer.php';?>
<script>
    // ตรวจสอบว่ามีการอัปเดตการตั้งค่าเว็บไซต์หรือไม่
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
        // เช็คว่ามีการอัปเดตเสร็จสมบูรณ์หรือไม่
        <?php if (isset($site_name) && isset($site_nav) && isset($contact_email) && isset($announce) && isset($maintenance_mode)) { ?>
            // แสดง SweetAlert เมื่ออัปเดตเสร็จสมบูรณ์
            Swal.fire({
                title: 'Settings Updated!',
                icon: 'success',
                timer: 1500, // แสดง SweetAlert จนกว่า 1500 milliseconds (1.5 วินาที)
                showConfirmButton: false
            });
        <?php } ?>
    <?php } ?>
</script>
</body>
</html>
