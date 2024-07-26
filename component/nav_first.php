<?php
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
  <title>Title</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .navbar {
            background-color: #ffffff; /* สีพื้นหลังเป็นสีขาว */
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
            color: #FF0000; /* สีแดงสำหรับข้อความ */
            position: relative;
            font-weight: bold; /* ทำให้ข้อความในลิงก์ของ navbar เป็นตัวหนา */
        }

        .navbar-dark .navbar-toggler-icon {
            color: #FF0000; /* สีของ icon เป็นสีแดง */
        }

        .navbar-dark .navbar-nav .nav-link .fa {
            color: #FF0000; /* สีของ icon เป็นสีแดง */
        }

        .navbar-dark .navbar-nav .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #FF0000; /* สีแดงสำหรับเส้นขีด */
            transition: width 0.3s;
            position: absolute;
            bottom: -5px;
            left: 0;
        }

        .navbar-dark .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .dropdown-menu {
            background-color: #FF0000; /* สีพื้นหลังของ dropdown เป็นสีแดง */
            animation: fadeIn 0.3s ease-in-out;
        }

        .dropdown-item {
            color: #FF0000; /* สีข้อความของ dropdown item เป็นสีแดง */
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: bold; /* ทำให้ข้อความใน dropdown item เป็นตัวหนา */
        }

        .dropdown-item:hover {
            background-color: #dd4b39; /* สีพื้นหลังเมื่อ hover เป็นสีแดงเข้ม */
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
            background-color: #FF0000; /* สีพื้นหลังของปุ่ม dropdown เป็นสีแดง */
            color: #ffffff; /* สีข้อความของปุ่ม dropdown เป็นสีขาว */
            border: 1px solid #FF0000; /* เส้นขอบของปุ่ม dropdown เป็นสีแดง */
            font-weight: bold; /* ทำให้ข้อความในปุ่ม dropdown เป็นตัวหนา */
        }

        .btn-light.dropdown-toggle:hover {
            background-color: #dd4b39; /* สีพื้นหลังของปุ่ม dropdown เมื่อ hover เป็นสีแดงเข้ม */
            border: 1px solid #dd4b39; /* สีเส้นขอบของปุ่ม dropdown เมื่อ hover เป็นสีแดงเข้ม */
        }

        .btn-secondary:active {
            background-color: #6c757d; /* เปลี่ยนสีพื้นหลังเมื่อถูกแตะ */
            border-color: #6c757d; /* เปลี่ยนสีเส้นขอบเมื่อถูกแตะ */
        }
  </style>
</head>
<body>
<b>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark ">
  <div class="container">
    <!-- Navbar brand with image -->
    <a class="navbar-brand" href="index" style="color: #FF0000; font-weight: bold;">
    <img src="uploads/<?php echo htmlspecialchars($siteSettings['image_path']); ?>" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
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
                    <a class="nav-link" href="./welcome" style="color: #FF0000; font-weight: bold;"><i class="fa fa-home fa-lg"></i> หน้าหลัก <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./welcome" style="color: #FF0000; font-weight: bold;"><i class="fa fa-youtube-play fa-lg"></i> วิธีการใช้งาน <span class="sr-only"></span></a>
                </li>
        <!-- Add more menu items here -->
      </ul>
      <div class="col-md-3 text-end">
        <?php 
        if (!isset($_SESSION['profile'])) {
            // เรียกใช้งานคลาส LineLogin หรือเมทอดที่ให้คุณสร้างลิงก์การเข้าสู่ระบบ Line แล้วกำหนดให้ตัวแปร $link
            $line = new LineLogin();
            $link = $line->getLink();
        ?>
        <a href="<?php echo $link; ?>" class="btn btn-success me-2">
          <img src="assets/images/line.png" alt="LINE Logo" width="20" height="20" class="me-1">
          LOGIN WITH LINE
        </a>
        <?php } else { ?>
            <a href="logout" class="btn btn-danger">Logout</a>
        <?php } ?>
      </div>
    </div>
  </div>
</nav>
</b>
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
