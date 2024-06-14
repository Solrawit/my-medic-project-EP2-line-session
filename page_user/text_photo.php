<?php
session_start();
require_once('../LineLogin.php');

if (!isset($_SESSION['profile'])) {
    header("location: ../index.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/medic.css">
    <link rel="stylesheet" type="text/css" href="../assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Photo To Text</title>
    <style type="text/css">
         body {
      background-image: url('../assets/images/bluewhite.jpg');
      background-size: cover;
      background-position: center;
        }
    .blurry-img {
      filter: blur(10px); /* Adjust as needed */
        }
        body {
            padding: 20px 100px;
            font-family: 'Sarabun', sans-serif;
        }
        .upper div {
            display: inline;
            margin-left: 100px;
            white-space: pre;
        }
        .bottom {
            margin-top: 30px;
            display: flex;
        }
        .bottom div {
            flex: 1;
            border: 1px solid rgb(118, 118, 118);
            height: 400px;
            margin: 10px;
            border-radius: 10px;
            padding: 10px;
            position: relative;
            overflow: hidden;
        }
        .bottom div img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .bottom div textarea {
            resize: none;
            width: calc(100% - 20px);
            height: calc(100% - 20px);
            padding: 10px;
            font-size: 20px;
            outline: none;
            border: none;
        }
        .btn-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include '../component/nav_textphoto.php';?>
<br>
<div class="container">
    <div class="upper">
        <input type="file" class="form-control"><br>
        <button class="btn btn-primary">เริ่มต้นการอ่านข้อความ.!</button>
        <div class="progress"></div>
    </div>
    <div class="bottom">
        <div>
            <img src="" alt="">
        </div>
        <div>
        <textarea id="myTextarea" class="form-control" placeholder="Text"></textarea>

        <script>
            document.getElementById("myTextarea").addEventListener("keypress", function(event) {
            event.preventDefault(); // ยกเลิกการกระทำของเหตุการณ์ keypress ไม่ให้ผู้ใช้พิมพ์หรือแก้ไขข้อมูล
            });
        </script>
        </div>
    </div>
</div>
<div class="container btn-container">
    <center><button type="button" class="btn btn-secondary btn-lg">ตั้งค่าการแจ้งเตือน</button></center>
    <br>
    <center><button type="button" class="btn btn-danger btn-lg" onclick="window.location.reload();">ยกเลิก</button></center>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">วิธีการใช้งาน</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <!-- เพิ่มข้อความวิธีการใช้งานที่นี่ -->
        ทดสอบPopup model วิธีการใช้งาน
      </div>

      <!-- Modal footer -->
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
      </div>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js"></script>
<script src="script.js"></script>

<!-- js สำหรับ แสดง popup model -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#myModal').modal('show');
    
    // ให้ปุ่ม "ปิด" ปิด Modal เมื่อคลิก
    $('#myModal .close, #myModal .modal-footer button').click(function() {
        $('#myModal').modal('hide');
    });
});
</script>
<!-- -------------------------- -->

<?php include '../component/footer.php';?>
</body>
</html>
