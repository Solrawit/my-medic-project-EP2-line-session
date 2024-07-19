<?php
session_start();
require_once('../LineLogin.php');

// Database connection
$host = 'localhost';
$db = 'mdpj_user';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch website settings
    $stmt = $pdo->query("SELECT maintenance_mode FROM settings WHERE id = 1");
    $settings = $stmt->fetch();
    $maintenance_mode = $settings['maintenance_mode'];

    // Check user role
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

    // Redirect to maintenance page if maintenance mode is enabled and user is not admin
    if ($maintenance_mode && $user_role !== 'admin') {
        header('Location: ../maintenance');
        exit;
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['profile'])) {
    header("location: ../index");
    exit();
}

$profile = $_SESSION['profile'];
$name = isset($profile->displayName) ? $profile->displayName : 'ไม่พบชื่อ';
$email = isset($profile->email) ? $profile->email : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? $profile->pictureUrl : 'ไม่มีรูปภาพโปรไฟล์';
$line_user_id = $profile->userId;

// Check if email is available
if ($email === 'ไม่พบอีเมล์') {
    echo "ไม่พบข้อมูลอีเมล์";
    exit();
}

try {
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

// Handle file upload and OCR processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        // Path to Tesseract executable
        $tesseractPath = '"C:/Program Files/Tesseract-OCR/tesseract.exe"';
        // Path to uploaded image
        $imagePath = '"' . realpath($uploadFile) . '"';
        // Path to store the output text
        $outputPath = '"' . realpath($uploadDir) . '/output"';

        // Command to execute Tesseract OCR with Thai and English languages
        $cmd = "$tesseractPath $imagePath $outputPath -l tha+eng";

        // Execute the command
        $output = shell_exec($cmd . " 2>&1");

        // Check if output file is created
        if (file_exists($uploadDir . 'output.txt')) {
            // Read the output text file
            $outputText = file_get_contents($uploadDir . 'output.txt');

            // Query to fetch words from the database
            $sql = "SELECT text_column FROM medicine";
            $result = $pdo->query($sql);

            $foundWords = [];

            if ($result->rowCount() > 0) {
                // Check each word from the database against the OCR output
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $word = $row["text_column"];
                    if (strpos($outputText, $word) !== false) {
                        $foundWords[] = $word;
                    }
                }
            }

            // Generate text to display based on found words
            $outputTextFiltered = '';
            if (!empty($foundWords)) {
                foreach ($foundWords as $word) {
                    $outputTextFiltered .= $word . "\n";
                }
            }

            // Prepare response data
            $responseData = [
                'status' => 'success',
                'image' => htmlspecialchars($uploadFile),
                'text' => $outputTextFiltered,
                'found_words' => $foundWords
            ];

            echo json_encode($responseData);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create output file. Command output: ' . htmlspecialchars($output)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
    }
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocr_text']) && isset($_POST['ocr_image_data'])) {
    $ocrText = $_POST['ocr_text'];
    $ocrImageData = $_POST['ocr_image_data'];

    // Get selected time, meal, and quantity
    $selectedTime = $_POST['selected_time'];
    $selectedMeal = $_POST['selected_meal'];
    $selectedQuantity = $_POST['selected_quantity'];

    // Combine OCR text with selected values
    $combinedText = $ocrText . "\n" . "ช่วงเวลา: " . $selectedTime . "\n" . "รับประทาน: " . $selectedMeal . "\n" . "ครั้งละ: " . $selectedQuantity . " เม็ด";

    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET ocr_scans_text = :ocr_text,
                ocr_image_data = :ocr_image_data
            WHERE line_user_id = :line_user_id
        ");
        $stmt->bindParam(':ocr_text', $combinedText);
        $stmt->bindParam(':ocr_image_data', $ocrImageData);
        $stmt->bindParam(':line_user_id', $line_user_id);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'text_with_time' => $combinedText]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/png" href="../favicon.png"> <!-- favicon images -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Photo To Text</title>
    <style type="text/css">
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
            background-image: url('../assets/images/wpp3.png');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .blurry-img {
            filter: blur(10px); /* Adjust as needed */
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
            transition: background-color 0.3s ease-in-out; /* เพิ่ม transition เมื่อเปลี่ยนพื้นหลัง */
        }
        .btn-container {
            margin-top: 20px;
        }
        /* เพิ่มเอฟเฟกต์ hover ให้กับปุ่ม */
        #startOcrButton {
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }
        #startOcrButton:hover {
            background-color: #6c757d; /* เปลี่ยนสีพื้นหลังเมื่อโฮเวอร์ */
            transform: scale(1.05); /* เพิ่มขนาดของปุ่มเล็กน้อย */
        }
        .blurry-img {
            filter: blur(10px); /* Adjust as needed */
        }
        .upper {
            text-align: center; /* Center align the form */
        }
        .upper label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .upper input[type="file"] {
            display: none; /* Hide the default file input */
        }
        .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00000f; /* Blue background color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }
        .upload-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
<?php include '../component/nav_textphoto.php';?>
<br>
<div class="container fade-in">
    <div class="upper">
        <form id="uploadForm" enctype="multipart/form-data">
        <label for="image" style="color: white;">กรุณาเลือกรูปภาพ:</label>
            <label for="image" class="upload-btn">
                <i class="fa fa-upload"></i> เลือกไฟล์
            </label>
            <input type="file" id="image" name="image" accept="image/*">
        </form>
    </div>
    <center><button type="button" id="startOcrButton" class="btn btn-warning">แปลงภาพเป็นข้อความ</button></center>
    <div class="bottom">
        <div>
            <img id="uploadedImage" src="" alt="">
        </div>
        <div>
            <textarea id="ocrResult" readonly></textarea>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ocrModal" tabindex="-1" aria-labelledby="ocrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ocrModalLabel">ตรวจสอบข้อความ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>กรุณาตรวจสอบและแก้ไขข้อความตามที่ต้องการก่อนบันทึก</p>
                <textarea id="editedOcrText" class="form-control animate__animated animate__fadeInDown" rows="5"></textarea>
                <label for="timeSelect">ช่วงเวลา:</label>
                <select id="timeSelect" class="form-control">
                    <option value="เช้า">เช้า</option>
                    <option value="กลางวัน">กลางวัน</option>
                    <option value="เย็น">เย็น</option>
                    <option value="ก่อนนอน">ก่อนนอน</option>
                </select>
                <label for="mealSelect">รับประทาน:</label>
                <select id="mealSelect" class="form-control">
                    <option value="ก่อนอาหาร">ก่อนอาหาร</option>
                    <option value="หลังอาหาร">หลังอาหาร</option>
                </select>
                <label for="quantitySelect">ครั้งละ:</label>
                <select id="quantitySelect" class="form-control">
                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <input type="hidden" id="ocrImageData" name="ocr_image_data">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="confirmSave">ยืนยันบันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#startOcrButton').click(function() {
        const formData = new FormData($('#uploadForm')[0]);
        $.ajax({
            url: '', // Set to the path of your PHP file
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#startOcrButton').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                $('#startOcrButton').prop('disabled', true);
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    $('#uploadedImage').attr('src', data.image); // Set the image source
                    $('#ocrResult').val(data.text); // Set the text in the textarea
                    $('#ocrModal').modal('show');
                    $('#editedOcrText').val(data.text);
                    $('#ocrImageData').val(data.image);
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + status + ' ' + error);
            },
            complete: function() {
                $('#startOcrButton').html('แปลงภาพเป็นข้อความ');
                $('#startOcrButton').prop('disabled', false);
            }
        });
    });

    $('#confirmSave').click(function() {
        let editedText = $('#editedOcrText').val();
        const imageData = $('#ocrImageData').val();
        const selectedTime = $('#timeSelect').val();
        const selectedMeal = $('#mealSelect').val();
        const selectedQuantity = $('#quantitySelect').val();

        $.ajax({
            url: '', // Set to the path of your PHP file
            method: 'POST',
            data: {
                ocr_text: editedText,
                ocr_image_data: imageData,
                selected_time: selectedTime,
                selected_meal: selectedMeal,
                selected_quantity: selectedQuantity
            },
            beforeSend: function() {
                $('#confirmSave').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังบันทึก...');
                $('#confirmSave').prop('disabled', true);
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    $('#ocrModal').modal('hide');
                    Swal.fire('บันทึกข้อมูลสำเร็จ', '', 'success');
                    setTimeout(function() {
                    window.location.href = 'history';
                }, 2500); // Redirect after 5 seconds
                    $('#ocrResult').val(''); // Clear text after saving
                    $('#uploadedImage').attr('src', ''); // Clear image after saving
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + status + ' ' + error);
            },
            complete: function() {
                $('#confirmSave').html('ยืนยันบันทึก');
                $('#confirmSave').prop('disabled', false);
            }
        });
    });
});
</script>
<?php include '../component/footer.php';?>
</body>
</html>
