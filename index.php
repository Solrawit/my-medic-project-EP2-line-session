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

// Check if user is logged in
if (isset($_SESSION['profile'])) {
    header('Location: welcome');
    exit(); // Ensure the script stops executing after redirection
}

// Fetch site settings
$siteSettings = getSiteSettings($pdo);
$siteName = htmlspecialchars($siteSettings['site_name'] ?? 'Default Site Name');
$contactEmail = htmlspecialchars($siteSettings['contact_email'] ?? 'default@example.com');
$announce = htmlspecialchars($siteSettings['announce'] ?? 'ข้อความประกาศ');

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDICINE.NET</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/forwelcome.css">
    <link rel="stylesheet" type="text/css" href="assets/css/universe.css">
    <link rel="stylesheet" type="text/css" href="assets/css/loadweb.css">
    <!--ใช้ได้แต่ไม่ใช้ <script src="animation.js"></script> ใช้ได้แต่ไม่ใช้-->
    <link rel="icon" type="image/png" href="favicon.png"> <!-- favicon image -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <style>
      body {
            position: relative;
            font-family: 'Sarabun', sans-serif;
            padding: 0;
            margin: 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/images/wpp6.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        .banner {
            width: 100%;
            position: relative;
        }

        .banner img {
            width: 100%;
            border-radius: 34px;
        }

        .banner .text {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            z-index: 1;
            color: white;
            font-size: 3.3rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .featurette-divider {
            margin: 5rem 0;
        }

        .featurette img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 576px) {
            .banner .text {
                font-size: 1rem;
                right: 5px;
            }

            .card-text {
                font-size: 0.9rem;
            }

            .featurette h2 {
                font-size: 1.5rem;
            }

            .featurette p {
                font-size: 0.9rem;
            }
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
        }, 1500); // รอ 2 วินาทีก่อนที่จะซ่อนตัวโหลด
    });
</script>

    <?php require_once("component/nav_first.php"); ?>
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2); color: white; display: flex; justify-content: center; align-items: center; gap: 10px;">
      © Copyright 2024 Website By Computer Science RMUT All Rights Reserved.
      <a class="text-white" href="#">MEDIC OCR</a>
      <div class="loading">
          <svg width="64px" height="48px">
              <polyline points="0.157 23.954, 14 23.954, 21.843 48, 43 0, 50 24, 64 24" id="back"></polyline>
              <polyline points="0.157 23.954, 14 23.954, 21.843 48, 43 0, 50 24, 64 24" id="front"></polyline>
          </svg>
      </div>
  </div>



  <br>
  <center>
  <div class="banner fade-in" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
    <div style="flex-grow: 1; text-align: right;">
      <img src="assets/images/bg5.png" alt="Banner Image" style="width: 100%; position: relative; z-index: -1; border-radius: 34px;">
      <div class="text" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 1;">
        ยินดีต้อนรับเข้าสู่<br>
        <?php echo $siteName; ?>
      </div>
    </div>
  </div>
</center>

  <br>
  <div class="container fade-in">
  <h2 class="text-center" style="color: white;">CONTACT EMAIL : <u style="color: white;"><?php echo $contactEmail; ?></u></h2>
    <!-- <h4 class="text-center">รองรับ PC / Androids / IOS</h4> -->
  </div>
  <br>
  <!-- ส่วนประกาศ -->
  <div class="container fade-in" style="background-color: rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 1px; margin-top: 15px;">
    <div style="display: flex; align-items: center; flex-wrap: wrap;">
        <h4 style="color: dark; margin: 0; white-space: nowrap;">
            <span style="background-color: white; padding: 5px 10px; border-radius: 10px; display: inline-flex; align-items: center;">
                ประกาศ 
                <i class="fa fa-bullhorn" style="color: black; margin-left: 5px;"></i>
            </span> 
        </h4>
        <marquee style="flex: 1; min-width: 200px; color: white;" direction="left"><b><?php echo htmlspecialchars($announce); ?></b></marquee>
    </div>
</div>
  <!-- ส่วนประกาศ -->
  <br>
  <div class="container fade-in">
    <div class="row text-center pt-4">
    <div class="container fade-in text-white text-center">
        <b>ขณะนี้มีการแจ้งเตือนทั้งหมด : <?php echo $notify_count; ?> ข้อมูล</b>
    </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4><i class="fas fa-users"></i> ผู้ใช้ไลน์ทั้งหมด</h4>
                    <h5 class="card-title">All Users Line</h5>
                    <b><p class="card-text"><?php echo htmlspecialchars($user_count); ?> คน</p></b>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4><i class="fas fa-user-check"></i> ผู้ใช้ทั้งหมด</h4>
                    <h5 class="card-title">Registered Users</h5>
                    <b><p class="card-text"><?php echo htmlspecialchars($mdpj_user_count); ?> คน</p></b>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4><i class="fas fa-pills"></i> ฐานข้อมูลยาทั้งหมด</h4>
                    <h5 class="card-title">Total Medicines</h5>
                    <b><p class="card-text"><?php echo htmlspecialchars($medicine_count); ?> ข้อมูล</p></b>
                </div>
            </div>
        </div>
    </div>
</div>

  <br>
  <div class="container fade-in">
  <hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
  <div class="row" id="carddata">
    <div class="col-lg-4 text-center">
        <img src="assets/images/med1.png" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 1" />
        <u><h2>การรักษาโรค</h2></u>
        <b><p style="color: white;">ยาสามารถรักษาโรคต่างๆ และช่วยให้ผู้ป่วยฟื้นตัวได้อย่างรวดเร็ว ทำให้สามารถกลับมาทำกิจวัตรประจำวันได้อย่างมีประสิทธิภาพ</p></b>
    </div>
    <div class="col-lg-4 text-center">
        <img src="assets/images/med2.jpg" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 2" />
        <u><h2>บรรเทาอาการเจ็บปวด</h2></u>
        <b><p style="color: white;">ยาบรรเทาปวดช่วยลดความเจ็บปวดและความไม่สบาย ช่วยให้ผู้ป่วยสามารถใช้ชีวิตประจำวันได้อย่างสะดวกสบายขึ้น</p></b>
    </div>
    <div class="col-lg-4 text-center">
        <img src="assets/images/med3.jpg" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Image 3" />
        <u><h2>ป้องกันโรค</h2></u>
        <b><p style="color: white;">ยาบางชนิดสามารถใช้ในการป้องกันโรคต่างๆ เช่น วัคซีนที่ช่วยป้องกันการติดเชื้อและโรคระบาดต่างๆ</p></b>
    </div>
</div>

<div class="container fade-in">
<hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
    <div class="row featurette">
        <main class="container profile-container">
            <div class="card profile-card p-5">
                <div class="row">
                    <div class="col-md-7">
                        <h2 class="featurette-heading">
                            การใช้ระบบ OCR (Optical Character Recognition) เพื่อแปลงข้อมูลจากภาพเป็นข้อความ <span class="text-highlight">มีข้อดีหลายประการ.</span>
                        </h2>
                        <p class="lead">
                            การใช้ระบบ OCR นั้นมีความสำคัญในการเพิ่มประสิทธิภาพในการประมวลผลข้อมูลและลดความผิดพลาดที่อาจเกิดขึ้นในกระบวนการป้อนข้อมูลด้วยมือโดยไม่จำเป็นต้องพิจารณาความซับซ้อนของการแปลงข้อมูลด้วยตนเอง ทั้งนี้ยังช่วยเพิ่มความสะดวกสบายและเร่งความเร็วในการเข้าถึงข้อมูลต่างๆ ด้วยลักษณะที่เป็นมิตรและสะดวกในการใช้งาน.
                        </p>
                    </div>
                    <div class="col-md-5 text-center">
                        <img src="assets/images/ocrbanner.jpg" class="featurette-image img-fluid" alt="OCR Banner">
                    </div>
                </div>
            </div>
        </main>
    </div>
<br>
    <div class="container fade-in text-white text-center">
        <b>ขณะนี้มีการแจ้งเตือนทั้งหมด : <?php echo $notify_count; ?> ข้อมูล</b>
    </div>
    <hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">

    <main class="container fade-in profile-container">
        <div class="card profile-card p-5">
            <div class="row featurette">
                <div class="col-md-5 text-center">
                    <img src="assets/images/linealert.png" class="featurette-image img-fluid" alt="Line Alert">
                </div>
                <div class="col-md-7">
                    <h2 class="featurette-heading">การแจ้งเตือนการรับประทานยา(LINE ALERT). <span class="text-highlight">ผ่านทาง LINE.</span></h2>
                    <p class="lead">ด้วยความสะดวกสบายและความแม่นยำในการแจ้งเตือนการรับประทานยา ระบบนี้มีความเป็นประโยชน์ในการช่วยให้ผู้ใช้สามารถจัดการการรับประทานยาอย่างเป็นระเบียบและป้องกันการละเลยในการรับประทานยาที่สำคัญได้ในเวลาที่กำหนด.</p>
                    <p class="lead">ตั้งเวลาและประเภทของการแจ้งเตือน: ผู้ใช้สามารถตั้งเวลาและประเภทของการแจ้งเตือนการรับประทานยาได้ตามความเหมาะสม เช่น การตั้งเวลาเพื่อแจ้งเตือนการรับประทานยาในเช้าหรือเย็น และสามารถกำหนดจำนวนของยาที่ต้องรับประทานได้ในแต่ละครั้ง.</p>
                    <p class="lead">ส่งข้อความแจ้งเตือน: เมื่อถึงเวลาที่ตั้งไว้ในการแจ้งเตือน ระบบจะส่งข้อความแจ้งเตือนผ่านแอปพลิเคชัน LINE เพื่อแจ้งให้ผู้ใช้ทราบว่าถึงเวลาที่ต้องรับประทานยาแล้ว.</p>
                    <p class="lead">ระบบปลอดภัย: การแจ้งเตือนการรับประทานยาทาง LINE มักมีระบบความปลอดภัยที่มั่นคง เพื่อปกป้องข้อมูลส่วนตัวของผู้ใช้ และใช้การเข้ารหัสข้อมูลเพื่อป้องกันการเข้าถึงจากบุคคลที่ไม่ได้รับอนุญาต.</p>
                </div>
            </div>
        </div>
    </main>

    <hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
    <main class="container profile-container">
        <div class="card profile-card p-5">
            <div class="row featurette fade-in">
                <div class="col-md-5 text-center">
                    <img src="assets/images/database.png" class="featurette-image img-fluid" alt="Database">
                </div>
                <div class="col-md-7">
                    <h2 class="featurette-heading">การเก็บฐานข้อมูลผู้ใช้ที่มี. <span class="text-highlight">ประสิทธิภาพ.</span></h2>
                    <p class="lead">ด้วยการเก็บฐานข้อมูลผู้ใช้ที่มีประสิทธิภาพ ธุรกิจสามารถเพิ่มประสิทธิภาพในการบริการและจัดการลูกค้าได้อย่างมีประสิทธิภาพและเป็นระบบ.</p>
                </div>
            </div>
        </div>
    </main>
</div>

<hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
    <!-- Loading Spinner -->
    <div class="spinner-grow text-white" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <!-- End Loading Spinner -->
  </div>

  <?php include 'component/footer.php';?>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/jquery/jquery-slim.min.js"></script>
  <script type="text/javascript" src="assets/popper/popper.min.js"></script>
  <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#announcementModal').modal('show');

      // Add the fade-in class when the element is scrolled into view
      $(window).on('scroll', function() {
  $('.fade-in').each(function() {
    var elementTop = $(this).offset().top;
    var windowBottom = $(window).scrollTop() + $(window).height();
    if (elementTop < windowBottom) {
      $(this).addClass('visible bounce-in');
    }
        });
      });
    });
  </script>
  
</body>
</html>

