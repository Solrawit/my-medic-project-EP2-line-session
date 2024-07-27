<?php
session_start();
require_once('LineLogin.php');
require_once 'db_connection.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage());
}

// Fetch user counts
try {
    $stmt_users = $pdo->query("SELECT COUNT(*) AS user_count FROM users");
    $user_count = $stmt_users ? $stmt_users->fetch(PDO::FETCH_ASSOC)['user_count'] : 0;
    
    $stmt_mdpj_user = $pdo->query("SELECT COUNT(*) AS user_count FROM mdpj_user");
    $mdpj_user_count = $stmt_mdpj_user ? $stmt_mdpj_user->fetch(PDO::FETCH_ASSOC)['user_count'] : 0;
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}

// Fetch medicines
try {
    $stmt = $pdo->query("SELECT * FROM medicine");
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $medicine_count = count($medicines);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูลยา: " . $e->getMessage());
}

// Fetch site settings
$siteSettings = getSiteSettings($pdo);
$siteName = htmlspecialchars($siteSettings['site_name'] ?? 'Default Site Name');
$contactEmail = htmlspecialchars($siteSettings['contact_email'] ?? 'default@example.com');
$announce = htmlspecialchars($siteSettings['announce'] ?? 'ข้อความประกาศ');
$siteNav = isset($siteSettings['site_nav']) ? $siteSettings['site_nav'] : 'Test';

// ดึงจำนวนการแจ้งเตือนทั้งหมด
try {
    $stmt_notify = $pdo->query("SELECT COUNT(*) AS notify_count FROM notify");
    $result_notify = $stmt_notify->fetch(PDO::FETCH_ASSOC);
    $notify_count = $result_notify['notify_count'];
  } catch (PDOException $e) {
    $notify_count = 0; // กรณีไม่พบข้อมูล
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="favicon.png"> <!-- favicon image -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/loadweb.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(20px); }
            to { transform: translateY(0); }
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
            animation: fadeIn 1s ease-in-out;
            font-family: 'Arial', sans-serif;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/images/wpp3.png');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .maintenance-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: slideIn 1s ease-in-out;
        }
        .maintenance-container h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .maintenance-container p {
            color: #666;
            margin-bottom: 30px;
        }
        .logout-btn {
            color: white;
            background-color: #8B0000;
            border-color: #8B0000;
            animation: fadeIn 1s ease-in-out 1s forwards;
            opacity: 0;
        }
        .logout-btn:hover {
            background-color: #b22222;
            border-color: #b22222;
        }
        a {
            color: #8B0000;
            text-decoration: none;
        }
        a:hover {
            color: #b22222;
        }
    </style>
</head>
<body>
<div id="loader" class="loader">
        <div class="container">
            <div class="carousel">
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
                <div class="love"></div>
            </div> 
        </div>
        <div class="container">
            <div class="carousel">
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
                <div class="death"></div>
            </div> 
        </div>
        <div class="container">
            <div class="carousel">
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
                <div class="robots"></div>
            </div> 
        </div>
    </div>
    
    <script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            document.getElementById('loader').classList.add('hidden');
        }, 2000); // รอ 2 วินาทีก่อนที่จะซ่อนตัวโหลด
    });
</script>
    <div class="background"></div>
    <div class="maintenance-container">
        <u><h1><i class="fas fa-tools"></i> <?php echo $siteNav; ?></h1></u>
        <h1><i class="fas fa-tools"></i> ขออภัยในความไม่สะดวก</h1>
        <p>เว็บไซต์ของเรากำลังอยู่ในช่วงปิดปรับปรุง กรุณากลับมาใหม่ในภายหลัง<br>Our website is maintenance. Please come back again.</p>
        <a href="logout" class="btn logout-btn"><i class="fas fa-sign-out-alt"></i> กลับไปหน้าหลัก</a>
        <br>
        <br>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<p>คุณเป็นผู้ดูแลระบบ คุณสามารถ <a href="welcome">เข้าสู่ระบบ</a> ได้</p>';
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

