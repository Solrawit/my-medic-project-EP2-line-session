<?php
require_once('LineLogin.php');
require_once('db_connection.php');
##session_start(); // Ensure session is started

// ดึงข้อมูลตั้งค่าเว็บไซต์
$siteSettings = getSiteSettings($db);
$siteName = isset($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Default Site Name';
$contactEmail = isset($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'default@example.com';
$siteNav = isset($siteSettings['site_nav']) ? $siteSettings['site_nav'] : 'Test';
$imagePath = isset($siteSettings['image_path']) ? $siteSettings['image_path'] : 'logo.png'; // เพิ่มการดึงข้อมูล image_path
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/loadweb.css">
    <!--ใช้ได้แต่ไม่ใช้ <script src="animation.js"></script> ใช้ได้แต่ไม่ใช้-->
    <link rel="icon" type="image/png" href="favicon.png"> <!-- favicon image -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    
    <style>
        .navbar {
            background-color: #ffffff; /* สีพื้นหลังของ navbar เป็นสีขาว */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        .navbar:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.1);
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: top 0.3s ease-in-out;
        }

        .sticky-top.navbar-scrolled {
            top: -60px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #070bf5; /* สีแดงสำหรับข้อความ */
            position: relative;
            font-weight: bold; /* ทำให้ข้อความในลิงก์ของ navbar เป็นตัวหนา */
        }

        .navbar-dark .navbar-toggler {
            background-color: #070bf5; /* สีพื้นหลังของปุ่ม toggler เป็นสีแดง */
            border: none; /* ไม่มีเส้นขอบ */
        }

        .navbar-dark .navbar-toggler-icon {
            background-image: #ffffff; /* ลบพื้นหลังของไอคอนเดิม */
        }

        .navbar-dark .navbar-nav .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #070bf5; /* สีแดงสำหรับเส้นขีด */
            transition: width 0.3s;
            position: absolute;
            bottom: -5px;
            left: 0;
        }

        .navbar-dark .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .dropdown-menu {
            background-color: #070bf5; /* สีพื้นหลังของ dropdown เป็นสีแดง */
            animation: fadeIn 0.3s ease-in-out;
        }

        .dropdown-item {
            color: #ffffff; /* สีข้อความของ dropdown item เป็นสีแดง */
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: bold; /* ทำให้ข้อความใน dropdown item เป็นตัวหนา */
        }

        .dropdown-item:hover {
            background-color: #070bf5; /* สีพื้นหลังเมื่อ hover เป็นสีแดงเข้ม */
            color: #ffffff; /* สีข้อความเมื่อ hover เป็นสีขาว */
        }

        .dropdown-item i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .dropdown-item:hover i {
            transform: translateX(5px);
        }

        .btn-light.dropdown-toggle {
            background-color: #09f; /* สีพื้นหลังของปุ่ม dropdown เป็นสีแดง */
            color: #ffffff; /* สีข้อความของปุ่ม dropdown เป็นสีขาว */
            border: 1px solid #09f; /* เส้นขอบของปุ่ม dropdown เป็นสีแดง */
            font-weight: bold; /* ทำให้ข้อความในปุ่ม dropdown เป็นตัวหนา */
        }

        .btn-light.dropdown-toggle:hover {
            background-color: #0C1844; /* สีพื้นหลังของปุ่ม dropdown เมื่อ hover เป็นสีแดงเข้ม */
            color: #ffffff; /* สีข้อความของปุ่ม dropdown เป็นสีขาว */
            border: 1px solid #0C1844; /* สีเส้นขอบของปุ่ม dropdown เมื่อ hover เป็นสีแดงเข้ม */
        }

        .btn-secondary:active {
            background-color: #070bf5; /* เปลี่ยนสีพื้นหลังเมื่อถูกแตะ */
            border-color: #070bf5; /* เปลี่ยนสีเส้นขอบเมื่อถูกแตะ */
        }
        
    </style>
</head>
<body>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-custom">
    <div class="container">
        <a class="navbar-brand" href="./welcome" style="color: #0C1844; font-weight: bold;">
            <img src="uploads/<?php echo htmlspecialchars($siteSettings['image_path']); ?>" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            <?php echo $siteNav; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="./welcome" style="color: #0C1844; font-weight: bold;"><i class="fa fa-home fa-lg"></i> หน้าหลัก <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo" style="color: #0C1844; font-weight: bold;"><i class="fa-solid fa-capsules"></i> เพิ่มรายการยา <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/history" style="color: #0C1844; font-weight: bold;"><i class="fa fa-history"></i> รายการของฉัน <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./รูปภาพใช้ทดลองOCR/Ex1.png" style="color: #0C1844; font-weight: bold;"><i class="fa fa-download"></i> ดาวน์โหลดตัวอย่างซองยา <span class="sr-only"></span></a>
                </li>
            </ul>
            <div class="col-md-3 text-end">
                <?php if (!isset($_SESSION['profile'])): ?>
                    <?php
                    $line = new LineLogin();
                    $link = $line->getLink();
                    ?>
                    <a href="<?php echo $link; ?>" class="btn btn-success me-2" style="font-weight: bold;">LOGIN WITH LINE</a>
                <?php else: ?>
                    <?php $profile = $_SESSION['profile']; ?>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: bold;">
                            <?php if (!empty($profile->pictureUrl)): ?>
                                <img src="<?php echo htmlspecialchars($profile->pictureUrl); ?>" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                            <?php endif; ?>
                            ยินดีต้อนรับคุณ <?php echo htmlspecialchars($profile->displayName); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="./profile"><i class="fa fa-user" aria-hidden="true"></i> โปรไฟล์ของฉัน</a></li>
                            <li><a class="dropdown-item" href="./medicine_vote"><i class="fa fa-comments" aria-hidden="true"></i> แบบประเมิน</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/admin"><i class="fa fa-database" aria-hidden="true"></i> ระบบหลังบ้าน</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="./logout"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Bootstrap JavaScript Bundle with Popper. -->
<script>
  $(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
      $(".navbar").addClass("navbar-scrolled");
    } else {
      $(".navbar").removeClass("navbar-scrolled");
    }
  });
</script>
</body>
</html>

