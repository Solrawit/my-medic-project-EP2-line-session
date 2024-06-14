<?php
session_start();
require_once('LineLogin.php');

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
    <style>
    body {
      background-image: url('assets/images/bluewhite.jpg');
      background-size: cover;
      background-position: center;
    }

    .blurry-img {
      filter: blur(10px); /* Blur effect */
    }
    .rounded-image {
      border-radius: 32%; /* Rounded corners */
      max-width: 100%; /* Ensure image fits within container */
      height: auto; /* Maintain aspect ratio */
    }
    .card {
      border: 1px solid #242424; /* Border color */
      border-radius: 8px; /* Rounded corners for card */
    }
    .slider-title {
      color: #fff;
      text-align: center;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 90px; /* Space between image and text */
      white-space: nowrap;
      overflow: hidden;
      animation: slideText 10s linear infinite;
    }
    .slider-title img {
      max-width: 100px; /* Image size */
    }
    @keyframes slideText {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
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
  <div class="container">
    <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" class="active" aria-current="true" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item">
          <img src="assets/images/banner2.png" class="rounded-image" alt="Image 1">
          <div class="container">
            <div class="carousel-caption text-start">
              <h1>ระบบใช้งานง่าย</h1>
              <p>ระบบลองรับการแปรงจากรูปภาพเป็นข้อความ.</p>
              <p><a class="btn btn-lg btn-primary" href="#">ทดลองใช้งานฟรี</a></p>
            </div>
          </div>
        </div>
        <div class="carousel-item active">
          <img src="assets/images/banner2.png" class="rounded-image" alt="Image 2">
          <div class="container">
            <div class="carousel-caption">
              <h1>การเข้าสู่ระบบในไม่กี่คลิก</h1>
              <p>การเข้าสู่ระบบลองรับทั้ง2ระบบ.</p>
              <p><a class="btn btn-lg btn-primary" href="#">สมัครสมาชิก</a></p>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/images/banner2.png" class="rounded-image" alt="Image 3">
          <div class="container">
            <div class="carousel-caption text-end">
              <h1>แจ้งการทำงานผ่านไลน์</h1>
              <p>แจ้งเตือนเวลาพร้อมรูปภาพผ่านแอพพลิเคชั่นผ่านไลน์.</p>
              <p><a class="btn btn-lg btn-primary" href="#">ทดลองการใช้งาน</a></p>
            </div>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div><!-- /.carousel -->
  </div>
  <br>
  <div class="container">
    <h2 class="text-center">MEDIC OCR PROJECT SUPPORT FOR WEBSITE</h2>
  </div>
  <br>
  <!-- LOGO SLIDE BAR -->
  <div class="text-center p-3" style="background-color: rgba(255, 255, 255, 0.2);">
    <div class="container">
      <div class="slider-title">
        <img src="assets/images/css.png" alt="CSS">
        <img src="assets/images/fontawesome.png" alt="FontAwesome">
        <img src="assets/images/html.png" alt="HTML">
        <img src="assets/images/js.png" alt="JavaScript">
        <img src="assets/images/mysql.png" alt="MySQL">
        <img src="assets/images/linenotify.png" alt="Line Notify">
      </div>
    </div>
  </div>
  <!-- LOGO SLIDE BAR -->
  <br>
  <!-- Zone Card show list -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-md-4">
        <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="people-outline"></ion-icon>
            สมาชิกทั้งหมด
          </div>
          <div class="card-body">
            <h5 class="card-title">จำนวน 0 คน</h5>
            <p class="card-text">
              <a href="#" class="text-dark" style="text-decoration: none;">Comming soon</a>
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
              <a href="#" class="text-dark" style="text-decoration: none;">Comming soon</a>
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">
            <ion-icon name="desktop-outline"></ion-icon>
            ผู้ใช้งานผ่านไลน์
          </div>
          <div class="card-body">
            <h5 class="card-title">จำนวน 0 คน</h5>
            <p class="card-text">
              <a href="#" class="text-dark" style="text-decoration: none;">Comming soon</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  <!-- Body Content -->
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

  <script>
    $(document).ready(function(){
      $('#announcementModal').modal('show');
    });
  </script>
</body>
</html>
