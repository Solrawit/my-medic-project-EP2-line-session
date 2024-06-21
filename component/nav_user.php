<?php
require_once('./LineLogin.php');
##session_start(); // Ensure session is started
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
        /* Custom background color */
        .bg-custom {
            background-color: #34445d; /* Dark blue background color */
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); /* Adjusted shadow */
        }

        /* Custom text color */
        .navbar-dark .navbar-nav .nav-link,
        .navbar-dark .navbar-toggler-icon,
        .navbar-dark .navbar-brand {
            color: #ffffff !important; /* White color */
        }

        /* Customize Font Awesome icons color */
        .navbar-dark .navbar-nav .nav-link .fa {
            color: #ffffff; /* White color */
        }
    </style>
</head>
<body>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-custom">
    <div class="container">
        <!-- Navbar brand with image -->
        <a class="navbar-brand" href="./welcome.php">
            <img src="assets/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            MEDIC TEST 1.0 (LINE)
        </a>
        <!-- Navbar toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="./welcome.php"><i class="fa fa-home fa-lg"></i> หน้าหลัก <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo.php"><i class="fa fa-language fa-lg"></i> OCR V.1 <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/text_photo.php"><i class="fa fa-language fa-lg"></i> OCR V.2 <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/alert_time.php"><i class="fa fa-clock-o fa-lg"></i> ตั้งค่าการแจ้งเตือน <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="page_user/history.php"><i class="fa fa-history"></i> รายการของฉัน <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./welcome.php"><i class="fa fa-youtube-play fa-lg"></i> วิธีการใช้งาน <span class="sr-only"></span></a>
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
                            <li><a class="dropdown-item" href="./profile.php">โปรไฟล์ของฉัน</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/admin.php">ระบบหลังบ้าน</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
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
</body>
</html>
