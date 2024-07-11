<?php
require_once('LineLogin.php');
require_once('db_connection.php');
##session_start(); // Ensure session is started

// ดึงข้อมูลตั้งค่าเว็บไซต์
$siteSettings = getSiteSettings($db);
$siteName = isset($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Default Site Name';
$contactEmail = isset($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'default@example.com';
$siteNav = isset($siteSettings['site_nav']) ? $siteSettings['site_nav'] : 'Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .navbar {
            background-color: #34445d;
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
            color: #ffffff;
            position: relative;
        }

        .navbar-dark .navbar-toggler-icon {
            color: #ffffff;
        }

        .navbar-dark .navbar-nav .nav-link .fa {
            color: #ffffff;
        }

        .navbar-dark .navbar-nav .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s;
            position: absolute;
            bottom: -5px;
            left: 0;
        }

        .navbar-dark .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .dropdown-menu {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #ddd;
        color: #34445d;
    }

    .dropdown-item i {
        margin-right: 8px;
        transition: transform 0.3s ease;
    }

    .dropdown-item:hover i {
        transform: translateX(5px);
    }
    .btn-secondary:active {
        background-color: #6c757d; /* เปลี่ยนสีพื้นหลังเมื่อถูกแตะ */
        border-color: #6c757d; /* เปลี่ยนสีเส้นขอบเมื่อถูกแตะ */
    }
    
    </style>
</head>
<body>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-custom">
    <div class="container">
        <a class="navbar-brand" href="./welcome.php">
            <img src="assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            <?php echo $siteNav; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="./welcome"><i class="fa fa-home fa-lg"></i> หน้าหลัก <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo"><i class="fa fa-language fa-lg"></i> เพิ่มรายการยา <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/history"><i class="fa fa-history"></i> รายการของฉัน <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./welcome"><i class="fa fa-youtube-play fa-lg"></i> วิธีการใช้งาน <span class="sr-only"></span></a>
                </li>
            </ul>
            <div class="col-md-3 text-end">
                <?php if (!isset($_SESSION['profile'])): ?>
                    <?php
                    $line = new LineLogin();
                    $link = $line->getLink();
                    ?>
                    <a href="<?php echo $link; ?>" class="btn btn-success me-2">LOGIN WITH LINE</a>
                <?php else: ?>
                    <?php $profile = $_SESSION['profile']; ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
