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
$line_user_id = $profile->userId;

if ($email === 'ไม่พบอีเมล์') {
    echo "ไม่พบข้อมูลอีเมล์";
    exit();
}

// Database connection
$host = 'localhost';
$db = 'mdpj_user';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert or update user information
    $stmt = $pdo->prepare("
        INSERT INTO users (line_user_id, display_name, picture_url, email, login_time)
        VALUES (:line_user_id, :display_name, :picture_url, :email, NOW())
        ON DUPLICATE KEY UPDATE
            display_name = VALUES(display_name),
            picture_url = VALUES(picture_url),
            email = VALUES(email),
            login_time = VALUES(login_time)
    ");
    $stmt->bindParam(':line_user_id', $line_user_id);
    $stmt->bindParam(':display_name', $name);
    $stmt->bindParam(':picture_url', $picture);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocr_text'])) {
    $ocrText = $_POST['ocr_text'];

    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET ocr_scans_text = :ocr_text
            WHERE line_user_id = :line_user_id
        ");
        $stmt->bindParam(':ocr_text', $ocrText);
        $stmt->bindParam(':line_user_id', $line_user_id);
        $stmt->execute();
        echo "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
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
            background-image: url('../assets/images/wpp2.jpg');
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
        <input type="file" id="imageUpload" class="form-control"><br>
        <button id="startOcrButton" class="btn btn-primary">เริ่มต้นการอ่านข้อความ.!</button>
        <div class="progress"></div>
    </div>
    <div class="bottom">
        <div>
            <img id="uploadedImage" src="" alt="">
        </div>
        <div>
            <textarea id="ocrResult" class="form-control" placeholder="Text"></textarea>
            <script>
                document.getElementById("ocrResult").addEventListener("keypress", function(event) {
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#myModal').modal('show');
    
    // ให้ปุ่ม "ปิด" ปิด Modal เมื่อคลิก
    $('#myModal .close, #myModal .modal-footer button').click(function() {
        $('#myModal').modal('hide');
    });

    // OCR process
    document.getElementById('startOcrButton').addEventListener('click', function() {
        const file = document.getElementById('imageUpload').files[0];
        if (!file) {
            alert("กรุณาอัปโหลดภาพ");
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            const image = document.getElementById('uploadedImage');
            image.src = event.target.result;
            Tesseract.recognize(image.src, 'eng+tha', {
                logger: function(m) {
                    console.log(m);
                }
            }).then(function(result) {
                document.getElementById('ocrResult').value = result.data.text;
                saveText(result.data.text);
            });
        };
        reader.readAsDataURL(file);
    });

    function saveText(text) {
        $.ajax({
            url: '',
            method: 'POST',
            data: { ocr_text: text },
            success: function(response) {
                alert(response);
            },
            error: function(xhr, status, error) {
                console.error("Error: " + status + " " + error);
            }
        });
    }
});
</script>

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
<?php include '../component/footer.php';?>
</body>
</html>
