<?php
session_start();
require_once('../LineLogin.php');
require_once '../db_connection.php';
include '../timeout.php';

if (!isset($_SESSION['profile']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../index.php");
    exit();
}

$profile = $_SESSION['profile'];

$name = isset($profile->displayName) ? htmlspecialchars($profile->displayName, ENT_QUOTES, 'UTF-8') : 'ไม่พบชื่อ';
$email = isset($profile->email) ? htmlspecialchars($profile->email, ENT_QUOTES, 'UTF-8') : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? htmlspecialchars($profile->pictureUrl, ENT_QUOTES, 'UTF-8') : 'ไม่มีรูปภาพโปรไฟล์';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Main</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/medic.css">
    <link rel="stylesheet" type="text/css" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="animation.js">
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- favicon images -->
    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/loadweb.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <style>
        body {
            display: flex;
        }
        .sidebar {
            flex: 0 0 250px;
            background-color: #f8f9fa;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        .fade-in {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .fade-in.visible {
            opacity: 1;
        }
    </style>
</head>
<body>
 <!-- โหลดหน้าเว็ป -->
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
        }, 1500); // รอ 1.5 วินาทีก่อนที่จะซ่อนตัวโหลด
    });
</script>
<div class="sidebar">
        <?php require_once("../component/nav_admin.php"); ?>
    </div>
    <div class="main-content">
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2); color: white;">
    © Copyright 2024 Website By Computer Science RMUT All Rights Reserved.
    <a class="text-white" href="#">MEDIC OCR</a>
</div>

    <br>
    <center>
    <div class="banner fade-in">    
        <img src="../assets/images/backend.jpg" alt="Banner Image">
        <!-- <div class="text">Admin Page</div> -->
    </div>
    </center>

    <br>
    <div class="container fade-in">
        <h2 class="text-center">Admin Control Panel</h2>
        <p class="text-center">Manage website content and user accounts</p>
        <p class="text-center">จัดการเนื้อหาเว็บไซต์และบัญชีผู้ใช้</p>
    </div>
    <br>
    <div class="text-center p-3 fade-in" style="background-color: rgba(255, 255, 255, 0.2);">
        <!-- ## -->
    </div>
    <br>
    <div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-4">
            <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    <ion-icon name="people-outline"></ion-icon>
                    Manage Users
                </div>
                <div class="card-body">
                    <h5 class="card-title">Manage user accounts</h5>
                    <p class="card-text">
                        <a href="manage_users.php" class="text-dark" style="text-decoration: none; background-color: #999999; padding: 5px 10px; border-radius: 5px; color: white;">Go to User Data</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    <ion-icon name="settings-outline"></ion-icon>
                    Website Settings 
                </div>
                <div class="card-body">
                    <h5 class="card-title">Confix website settings</h5>
                    <p class="card-text">
                        <a href="website_settings.php" class="text-dark" style="text-decoration: none; background-color: #999999; padding: 5px 10px; border-radius: 5px; color: white;">Go to SettingsSite</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card text-dark bg-white mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    <ion-icon name="people-outline"></ion-icon>
                    Feedback Users
                </div>
                <div class="card-body">
                    <h5 class="card-title">Manage Feedback Users</h5>
                    <p class="card-text">
                        <a href="admin_feedback.php" class="text-dark" style="text-decoration: none; background-color: #999999; padding: 5px 10px; border-radius: 5px; color: white;">Go to Feedback Users</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

    <br>
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
    <?php include '../component/footer.php';?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNgyA4aWfLFlYFg6rRtfIea2z0gVHyjOAMF6cSWvYyFh5jmn0Tv9KKM258QvM9E" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVQIY5Y6MYhkCu5WoO4pH5OpkpcF6A5iDhR8p2YtXGyyY2G5DJUR0AcKK7E5p6e3" crossorigin="anonymous"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
