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
    <!-- CDN Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CDN Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            background-image: none; /* ลบพื้นหลังของไอคอนเดิม */
            color: #070bf5; /* สีของไอคอนเป็นสีขาว */
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

        /* Modal styles */
        .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: #fefefe;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px; /* Optional: to limit the max width */
      box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .modal-body img {
      max-width: 100%;
      height: auto;
      display: block;
      margin: 0 auto 15px;
    }
    /* Modal styles */

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
                    <a class="nav-link" href="javascript:void(0);" id="myBtn" style="color: #0C1844; font-weight: bold;"><i class="fa-brands fa-youtube"></i> วิธีการใช้งาน <span class="sr-only"></span></a>
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

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <h2>วิธีการใช้งานการแจ้งเตือนผ่านไลน์</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <ol>
          <li>เข้าไปที่หน้า <strong>ประวัติของฉัน</strong>
          <img src="./modelhowto/HISPAGE.png" alt="วิธีการใช้งาน - รูปที่ 1">
          <li>เช็คข้อมูลยาให้ครบถ้วนว่าถูกต้องหรือไม่</li>
          <li>กดปุ่ม <strong>แจ้งเตือนผ่านไลน์</strong> จะขึ้นเมนูหน้าต่างการตั้งค่าการแจ้งเตือนยาดังนี้</li>
          <img src="./modelhowto/notify.png" alt="วิธีการใช้งาน - รูปที่ 2">
          <li>ช่องใส่ Token LINE ให้เอาจากเว็บ <a href="https://notify-bot.line.me/th/" target="_blank">LINE Notify</a> หรือสแกน QR Code เพื่อไปที่หน้าสร้างTokenID <img src="./modelhowto/qrcode.png" alt="วิธีการใช้งาน - รูปที่ 3"></li>
          <li>เข้าสู่ระบบให้เรียบร้อย จากนั้นเข้าไปที่<strong>My page หรือ หน้าของฉัน</strong></li>
          <img src="./modelhowto/linenoti.png" alt="วิธีการใช้งาน - รูปที่ 7"></li>
          <li>ผู้ใช้สามารถสร้างกลุ่มเพื่อแจ้งเตือนเป็นครอบครัวได้ หรือจะสร้างใช้เฉพาะผู้ใช้ 1 คนก็ได้</li>
          <img src="./modelhowto/linenoti3.png" alt="วิธีการใช้งาน - รูปที่ 4"></li>
          <li>หลังจากกำหนดตามหัวข้อทั้งหมดแล้ว ให้กดปุ่ม<strong>Generate token ได้เลย</strong></li>
          <img src="./modelhowto/linenoti2.png" alt="วิธีการใช้งาน - รูปที่ 5"></li>
          <li>หลังจากได้ Token มาให้คัดลอก Token ที่ได้มาไปใส่ในช่องใส่ Token ได้เลย</li>
          <img src="./modelhowto/last.png" alt="วิธีการใช้งาน - รูปที่ 6"></li>
          <li>สามารถเก็บ Token ของคุณเอาไว้กรณีจะใช้แจ้งเตือนยาครั้งต่อไปได้ไม่จำเป็นต้องสร้างใหม่ <strong>กรณีเปลี่ยนจากแจ้งเตือนคนเดียวเป็นไลน์กลุ่ม หรือ จากแจ้งเตือนกลุ่มเป็นผู้ใช้คนเดียว</strong></li>
        </ol>
      </div>
    </div>
  </div>

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
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
      modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>

