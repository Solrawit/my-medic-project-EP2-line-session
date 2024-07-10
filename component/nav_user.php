
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
    <title>Title</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .navbar {
            background-color: #34445d; /* Dark blue color */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow with specific values */
            transition: box-shadow 0.3s ease; /* Smooth transition for box-shadow */
        }

        .navbar:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Larger shadow on hover */
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.1); /* Scale up on hover */
        }

        /* Sticky Navbar Animation */
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: top 0.3s ease-in-out; /* Smooth transition for top position */
        }

        .sticky-top.navbar-scrolled {
            top: -60px; /* Adjust as per your Navbar height */
        }

        /* Custom styles to change text color to white */
        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff; /* White color */
            position: relative; /* Make it relative to position the underline */
        }

        .navbar-dark .navbar-toggler-icon {
            color: #ffffff; /* White color */
        }

        /* Adjusting Font Awesome icons color */
        .navbar-dark .navbar-nav .nav-link .fa {
            color: #ffffff; /* White color */
        }

        /* CSS for underline effect on hover */
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
    </style>
</head>
<body>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-custom">
    <div class="container">
        <!-- Navbar brand with image -->
        <a class="navbar-brand" href="./welcome.php">
            <img src="assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            <?php echo $siteNav; ?>
        </a>
        <!-- Navbar toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="./welcome"><i class="fa fa-home fa-lg"></i> หน้าหลัก <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo"><i class="fa fa-language fa-lg"></i> เพิ่มรายการยา <span class="sr-only"></span></a>
                </li>
   <!-- Navbar              <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo.php"><i class="fa fa-language fa-lg"></i> OCR V.2 <span class="sr-only"></span></a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="page_user/history"><i class="fa fa-history"></i> รายการของฉัน <span class="sr-only"></span></a>
                </li>
   <!-- Navbar     <li class="nav-item">
                    <a class="nav-link" href="page_user/alert_time.php"><i class="fa fa-clock-o fa-lg"></i> ทดสอบการแจ้งเตือน <span class="sr-only"></span></a>
         </li> -->
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
  // jQuery to collapse the Navbar on scroll
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
