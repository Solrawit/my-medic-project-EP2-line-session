<?php
session_start();
require_once('LineLogin.php');
require_once 'db_connection.php';

// สร้างคำสั่ง SQL เพื่อดึงจำนวนบัญชีผู้ใช้ทั้งหมด
$sql = "SELECT COUNT(*) AS user_count FROM users";
$sql2 = "SELECT COUNT(*) AS user_count FROM mdpj_user";

$stmt = $db->query($sql);
$stmt2 = $db->query($sql2);

if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_count = $row['user_count'];
} else {
    $user_count = 0; // กรณีไม่พบข้อมูล
}

if ($stmt2) {
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $mdpj_user_count = $row2['user_count'];
} else {
    $mdpj_user_count = 0; // กรณีไม่พบข้อมูล
}

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (isset($_SESSION['profile'])) {
    header('Location: welcome.php');
    exit(); // Ensure the script stops executing after redirection
}

// ดึงข้อมูลตั้งค่าเว็บไซต์
$siteSettings = getSiteSettings($db);
$siteName = isset($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Default Site Name';
$contactEmail = isset($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'default@example.com';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/medic.css">
    <link rel="stylesheet" type="text/css" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
    <link rel="icon" type="image/png" href="favicon.png"> <!-- favicon image -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .banner {
            width: 100%;
            max-width: 1000px;
            height: auto;
            position: relative;
            overflow: hidden;
        }

        .banner video {
            width: 100%;
            height: auto;
            object-fit: cover;
            filter: blur(5px); /* ทำให้วิดีโอเบลอ */
        }

        .banner .text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 4em;
            font-family: Arial, sans-serif;
            text-align: center;
            animation: moveText 3s infinite;
        }

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
            background-image: url('assets/images/wpp2.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        .fade-in {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .fade-in.visible {
            opacity: 1;
        }

        .card {
            border: 1px solid #242424;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .rounded-image {
            border-radius: 32%;
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .rounded-image:hover {
            transform: scale(1.1);
        }

        .featurette-image {
            transition: transform 0.3s ease;
        }

        .featurette-image:hover {
            transform: scale(1.05);
        }

        .carousel-item img {
            transition: opacity 1s ease-in-out;
        }

        .carousel-item.active img {
            opacity: 1;
        }

        .carousel-item-next img,
        .carousel-item-prev img {
            opacity: 0;
        }
        @keyframes bounceIn {
          0% {
          opacity: 0;
          transform: scale(0.9);
        }
          50% {
          opacity: 1;
          transform: scale(1.05);
        }
          100% {
          transform: scale(1);
        }
        }

        .bounce-in {
          animation: bounceIn 0.75s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        @keyframes moveText {
            0% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.1); }
            100% { transform: translate(-50%, -50%) scale(1); }
        }
    </style>
</head>
<body>
    <?php require_once("component/nav_first.php"); ?>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © Copyright 2024 Website By Computer Science RMUT All Rights Reserved.
        <a class="text-white" href="#">MEDIC OCR</a>
    </div>

    <br>
    <center>
  <div class="banner fade-in">    
    <img src="assets/images/bg2.png" alt="Banner Image">
    <div class="text">ยินดีต้อนรับเข้าสู่เว็บไซต์<?php echo $siteName; ?></div>
  </div>
</center>
    <br>
    <div class="container fade-in">
        <h2 class="text-center">Contact Email : <?php echo $contactEmail; ?></h2>
    </div>
    <br>
    <div class="text-center p-3" style="background-color: rgba(255, 255, 255, 0.2);">
        <!-- ## -->
    </div>
    <br>
    <div class="container fade-in">
        <div class="row justify-content-center">
            <div class="col-sm-6 col-md-4">
                <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">
                        <ion-icon name="people-outline"></ion-icon>
                        บัญชีที่ลงทะเบียนทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">จำนวน <?php echo $mdpj_user_count; ?> คน</h5>
                        <p class="card-text">
                            <a class="text-dark" style="text-decoration: none;">Register All Used</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">
                        <ion-icon name="cart-outline"></ion-icon>
                        ผู้ใช้เพิ่มข้อมูล ทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">จำนวน 0 ครั้ง</h5>
                        <p class="card-text">
                            <a class="text-dark" style="text-decoration: none;">User all used Data</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">
                        <ion-icon name="desktop-outline"></ion-icon>
                        ผู้ใช้งานผ่านไลน์ทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">จำนวน <?php echo $user_count; ?> คน</h5>
                        <p class="card-text">
                            <a class="text-dark" style="text-decoration: none;">Member All Used</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container fade-in">
        <div class="row">
            <div class="col-lg-4">
                <img src="assets/images/med1.png" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 1" />
                <h2>การรักษาโรค</h2>
                <p>ยาสามารถรักษาโรคต่างๆ และช่วยให้ผู้ป่วยฟื้นตัวได้อย่างรวดเร็ว ทำให้สามารถกลับมาทำกิจกวัตรประจำวันได้อย่างมีประสิทธิภาพ</p>
            </div>
            <div class="col-lg-4">
                <img src="assets/images/med2.jpg" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 2" />
                <h2>บรรเทาอาการเจ็บปวด</h2>
                <p>ยาบรรเทาปวดช่วยลดความเจ็บปวดและความไม่สบาย ช่วยให้ผู้ป่วยสามารถใช้ชีวิตประจำวันได้อย่างสะดวกสบายขึ้น</p>
            </div>
            <div class="col-lg-4">
                <img src="assets/images/med3.jpg" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 3" />
                <h2>ป้องกันโรค</h2>
                <p>ยาบางชนิดสามารถใช้ในการป้องกันโรคต่างๆ เช่น วัคซีนที่ช่วยป้องกันการติดเชื้อและโรคระบาดต่างๆ</p>
            </div>
        </div>
    </div>
    <div class="container fade-in">
        <hr class="featurette-divider">
        <div class="row featurette">
            <div class="col-md-7">
                <h2 class="featurette-heading">การใช้ระบบ OCR (Optical Character Recognition) เพื่อแปลงข้อมูลจากภาพเป็นข้อความ <span class="text-white">มีข้อดีหลายประการ.</span></h2>
                <p class="lead">การใช้ระบบ OCR นั้นมีความสำคัญในการเพิ่มประสิทธิภาพในการประมวลผลข้อมูลและลดความผิดพลาดที่อาจเกิดขึ้นในกระบวนการป้อนข้อมูลด้วยมือโดยไม่จำเป็นต้องพิจารณาความซับซ้อนของการแปลงข้อมูลด้วยตนเอง ทั้งนี้ยังช่วยเพิ่มความสะดวกสบายและเร่งความเร็วในการเข้าถึงข้อมูลต่างๆ ด้วยลักษณะที่เป็นมิตรและสะดวกในการใช้งาน.</p>
            </div>
            <div class="col-md-5">
                <img src="assets/images/ocrbanner.jpg" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" alt="OCR Banner">
            </div>
        </div>
        <hr class="featurette-divider">
        <div class="row featurette">
            <div class="col-md-7 order-md-2">
                <h2 class="featurette-heading">การแจ้งเตือนการรับประทานยา(LINE ALERT). <span class="text-white">ผ่านทางLINE.</span></h2>
                <p class="lead">ด้วยความสะดวกสบายและความแม่นยำในการแจ้งเตือนการรับประทานยา ระบบนี้มีความเป็นประโยชน์ในการช่วยให้ผู้ใช้สามารถจัดการการรับประทานยาอย่างเป็นระเบียบและป้องกันการละเลยในการรับประทานยาที่สำคัญได้ในเวลาที่กำหนด.</p>
                <p class="lead">ตั้งเวลาและประเภทของการแจ้งเตือน: ผู้ใช้สามารถตั้งเวลาและประเภทของการแจ้งเตือนการรับประทานยาได้ตามความเหมาะสม เช่น การตั้งเวลาเพื่อแจ้งเตือนการรับประทานยาในเช้าหรือเย็น และสามารถกำหนดจำนวนของยาที่ต้องรับประทานได้ในแต่ละครั้ง.</p>
                <p class="lead">ส่งข้อความแจ้งเตือน: เมื่อถึงเวลาที่ตั้งไว้ในการแจ้งเตือน ระบบจะส่งข้อความแจ้งเตือนผ่านแอปพลิเคชัน LINE เพื่อแจ้งให้ผู้ใช้ทราบว่าถึงเวลาที่ต้องรับประทานยาแล้ว.</p>
                <p class="lead">ระบบปลอดภัย: การแจ้งเตือนการรับประทานยาทาไลน์มักมีระบบความปลอดภัยที่มั่นคง เพื่อปกป้องข้อมูลส่วนตัวของผู้ใช้ และใช้การเข้ารหัสข้อมูลเพื่อป้องกันการเข้าถึงจากบุคคลที่ไม่ได้รับอนุญาต.</p>
            </div>
            <div class="col-md-5 order-md-1">
                <img src="assets/images/linealert.png" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" alt="Line Alert">
            </div>
        </div>
        <hr class="featurette-divider">
        <div class="row featurette">
            <div class="col-md-7">
                <h2 class="featurette-heading">การเก็บฐานข้อมูลผู้ใช้ที่มี. <span class="text-white">ประสิทธิภาพ.</span></h2>
                <p class="lead">ด้วยการเก็บฐานข้อมูลผู้ใช้ที่มีประสิทธิภาพ ธุรกิจสามารถเพิ่มประสิทธิภาพในการบริการและจัดการลูกค้าได้อย่างมีประสิทธิภาพและเป็นระบบ.</p>
            </div>
            <div class="col-md-5">
                <img src="assets/images/database.png" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" alt="Database">
            </div>
        </div>
        <hr class="featurette-divider">
        <!-- Loading Spinner -->
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <!-- End Loading Spinner -->
    </div>

    <?php include 'component/footer.php';?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLR7/2vvoCw6PpRD/0YP4+Ps3TzjlPpLhXk2yjJ6hf" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7/zE9D/Vi4+S7Z7Ivc8wK2EAG7/ZdFdBEl8o0ZT0ik3rc93NxnX7nu27UCOmyl4/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVTDvR5/wix7nHk/3vwZ96D8uLUUC6K+5F82/RovzEH/SF4pPngtx2nkF9KgE4I1" crossorigin="anonymous"></script>

    <script>
        // jQuery function to add 'visible' class when the element comes into view
        $(window).on('scroll', function() {
  $('.fade-in').each(function() {
    var elementTop = $(this).offset().top;
    var windowBottom = $(window).scrollTop() + $(window).height();
    if (elementTop < windowBottom) {
      $(this).addClass('visible bounce-in');
    }
  });
});


        // Show announcement modal
        $(document).ready(function(){
            $('#announcementModal').modal('show');
        });
    </script>
</body>
</html>
