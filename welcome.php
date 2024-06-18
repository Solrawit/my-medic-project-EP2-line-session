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


if (!isset($_SESSION['profile'])) {
    header("location: index.php");
    exit();
}

$profile = $_SESSION['profile'];

$name = isset($profile->displayName) ? $profile->displayName : 'ไม่พบชื่อ';
$email = isset($profile->email) ? $profile->email : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? $profile->pictureUrl : 'ไม่มีรูปภาพโปรไฟล์';

if ($email === 'ไม่พบอีเมล์') {
    // กรณีไม่พบข้อมูลอีเมล์ให้แสดงข้อความเพื่อแจ้งให้ผู้ใช้ทราบ
    echo "ไม่พบข้อมูลอีเมล์";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/medic.css">
    <link rel="stylesheet" type="text/css" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="animation.js">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <style>
      body {
        background-image: url('assets/images/bluewhite.jpg');
        background-size: cover;
        background-position: center;
        animation: fadeIn 2s ease-in-out;
      }

      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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
    </style>
</head>
<body>

    <?php require_once("component/nav_user.php"); ?>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © Copyright 2024 Website By Computer Science RMUT All Rights Reserved.
    <a class="text-white" href="#">MEDIC OCR</a>
  </div>

  <br>
  <div class="container carousel-container">
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img class="d-block w-100" src="assets/images/banner2.png" alt="First slide">
          <div class="carousel-caption d-none d-md-block">
            <h5>ระบบใช้งานง่าย</h5>
            <p>ระบบลองรับการแปรงจากรูปภาพเป็นข้อความ.</p>
            <p><a class="btn btn-lg btn-primary" href="#">ทดลองใช้งานฟรี</a></p>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
  <br>
  <div class="container">
    <h2 class="text-center">MEDIC OCR PROJECT SUPPORT FOR WEBSITE OR MOBILE</h2>
    <!-- <h4 class="text-center">รองรับ PC / Android / IOS</h4> -->
  </div>
  <br>
  <div class="text-center p-3" style="background-color: rgba(255, 255, 255, 0.2);">
    <!-- ## -->
  </div>
  <br>
  <div class="container">
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
            ผู้ใช้ OCR-SCAN ทั้งหมด
          </div>
          <div class="card-body">
            <h5 class="card-title">จำนวน 0 ครั้ง</h5>
            <p class="card-text">
              <a class="text-dark" style="text-decoration: none;">Comming soon</a>
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
  <div class="container">
    <div class="row">
      <div class="col-lg-4">
        <img src="assets/images/140.png" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 1" />
        <h2>Heading</h2>
        <p>Some representative placeholder content for the first column of three columns of text below the carousel. This is the first column.</p>
        <p><a class="btn btn-secondary" href="#">View details »</a></p>
      </div>
      <div class="col-lg-4">
        <img src="assets/images/140.png" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 2" />
        <h2>Heading</h2>
        <p>Another exciting bit of representative placeholder content. This time, we've moved on to the second column.</p>
        <p><a class="btn btn-secondary" href="#">View details »</a></p>
      </div>
      <div class="col-lg-4">
        <img src="assets/images/140.png" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 3" />
        <h2>Heading</h2>
        <p>And lastly this, the third column of representative placeholder content.</p>
        <p><a class="btn btn-secondary" href="#">View details »</a></p>
      </div>
    </div>
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
  <script type="text/javascript" src="assets/jquery/jquery-slim.min.js"></script>
  <script type="text/javascript" src="assets/popper/popper.min.js"></script>
  <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#announcementModal').modal('show');
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLR7/2vvoCw6PpRD/0YP4+Ps3TzjlPpLhXk2yjJ6hf" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7/zE9D/Vi4+S7Z7Ivc8wK2EAG7/ZdFdBEl8o0ZT0ik3rc93NxnX7nu27UCOmyl4/" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVTDvR5/wix7nHk/3vwZ96D8uLUUC6K+5F82/RovzEH/SF4pPngtx2nkF9KgE4I1" crossorigin="anonymous"></script>
</body>
</html>