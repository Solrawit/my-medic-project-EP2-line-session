<?php
session_start();

require_once('../LineLogin.php');
require_once('../db_connection.php');

// Function to send LINE notification
function sendLineNotification($token, $message, $image_path = null) {
    $url = 'https://notify-api.line.me/api/notify';
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $token
    ];

    $data = ['message' => $message];
    if ($image_path) {
        $data['imageThumbnail'] = $image_path;
        $data['imageFullsize'] = $image_path;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// Check if user is logged in
if (!isset($_SESSION['profile'])) {
    header("Location: ../index.php");
    exit();
}

$profile = $_SESSION['profile'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alert_time = $_POST['alert_time'] ?? '';
    $token = 'oFxy3zhUONQsRo0dFS4ykSbfZdIruosnVsAP2oTABFj';

    if (empty($alert_time)) {
        $error_message = "กรุณาเลือกเวลาแจ้งเตือน";
    } else {
        // Update notification_time in the database
        $line_user_id = $profile->userId;
        $stmt = $db->prepare("UPDATE users SET notification_time = ? WHERE line_user_id = ?");
        $stmt->bindParam(1, $alert_time);
        $stmt->bindParam(2, $line_user_id);

        if ($stmt->execute()) {
            $success_message = "บันทึกเวลาแจ้งเตือนสำเร็จ";

            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if file is an image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    if (!file_exists($target_file)) {
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            $image_path = $target_file;
                            $image_message = "ไฟล์ " . basename($_FILES["image"]["name"]) . " อัปโหลดสำเร็จ";
                        } else {
                            $error_message = "ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ";
                        }
                    } else {
                        $error_message = "ขออภัย, ไฟล์นี้มีอยู่แล้ว";
                    }
                } else {
                    $error_message = "ขออภัย, ไฟล์ไม่ใช่รูปภาพ";
                }
            }

            // Send LINE notification
            if (!empty($token)) {
                $message = "ตั้งเวลาแจ้งเตือนเป็นเวลา $alert_time โดยคุณ : $profile->displayName";
                sendLineNotification($token, $message, $image_path ?: null);
            }
        } else {
            $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Alert Time</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            padding: 20px 100px;
            background-image: url('../assets/images/bluewhite.jpg');
            background-size: cover;
            background-position: center;
        }

        .containers {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        p {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="time"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            margin-bottom: 20px;
            outline: none;
        }

        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            margin-top: 20px;
        }

        .uploaded-image {
            display: block;
            margin: 20px auto;
            max-width: 100%;
        }
    </style>
</head>
<body>
<?php include '../component/nav_textphoto.php'; ?>
    <div class="containers">
        <h1>Set Alert Time Page</h1>
        <p>หน้าสำหรับการตั้งค่าเวลาแจ้งเตือน</p>
        <form id="alertForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="alert_time">Alert Time:</label>
            <input type="time" id="alert_time" name="alert_time" required>
            
            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image">
            
            <button type="submit">Set Alert Time</button>
        </form>
        <?php if (isset($success_message)) : ?>
            <p class="message" style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)) : ?>
            <p class="message" style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($image_message)) : ?>
            <p class="message" style="color: blue;"><?php echo $image_message; ?></p>
        <?php endif; ?>
        <?php if (isset($target_file) && file_exists($target_file)) : ?>
            <img src="<?php echo htmlspecialchars($target_file); ?>" alt="Uploaded Image" class="uploaded-image">
        <?php endif; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        <?php if(isset($success_message)) : ?>
            swal("Success", "<?php echo $success_message; ?>", "success");
        <?php endif; ?>
        <?php if(isset($error_message)) : ?>
            swal("Error", "<?php echo $error_message; ?>", "error");
        <?php endif; ?>
        <?php if(isset($image_message)) : ?>
            swal("Info", "<?php echo $image_message; ?>", "info");
        <?php endif; ?>
    </script>
</body>
</html>
