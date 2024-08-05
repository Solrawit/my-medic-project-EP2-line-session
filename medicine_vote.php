<?php
session_start();
require_once('LineLogin.php');
require_once('db_connection.php');
include 'timeout.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage());
}

if (!isset($_SESSION['profile'])) {
    header("location: index");
    exit();
}

$profile = $_SESSION['profile'];

$name = isset($profile->displayName) ? $profile->displayName : 'ไม่พบชื่อ';
$email = isset($profile->email) ? $profile->email : 'ไม่พบอีเมล์';
$picture = isset($profile->pictureUrl) ? $profile->pictureUrl : 'ไม่มีรูปภาพโปรไฟล์';

if ($email === 'ไม่พบอีเมล์') {
    echo "ไม่พบข้อมูลอีเมล์";
    exit();
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "ไม่พบผู้ใช้ในระบบ";
    exit();
}

$user_id = $user['id'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM user_feedback WHERE line_user_id = :line_user_id AND DATE(created_at) = CURDATE()");
$stmt->bindParam(':line_user_id', $profile->userId, PDO::PARAM_STR);
$stmt->execute();
$existingFeedbackCount = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

if ($existingFeedbackCount > 0) {
    echo "<script>
            alert('คุณได้ทำการประเมินไปแล้วในวันนี้');
            window.location.href = 'index';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_appeal = isset($_POST['design_appeal']) ? intval($_POST['design_appeal']) : 0;
    $ease_of_use = isset($_POST['ease_of_use']) ? intval($_POST['ease_of_use']) : 0;
    $user_feedback_experience = isset($_POST['user_feedback_experience']) ? $_POST['user_feedback_experience'] : '';
    $notification_accuracy = isset($_POST['notification_accuracy']) ? intval($_POST['notification_accuracy']) : 0;
    $feature_functionality = isset($_POST['feature_functionality']) ? intval($_POST['feature_functionality']) : 0;
    $system_reliability = isset($_POST['system_reliability']) ? intval($_POST['system_reliability']) : 0;
    $user_manual_completeness = isset($_POST['user_manual_completeness']) ? intval($_POST['user_manual_completeness']) : 0;
    $page_load_speed = isset($_POST['page_load_speed']) ? intval($_POST['page_load_speed']) : 0;
    $server_responsiveness = isset($_POST['server_responsiveness']) ? intval($_POST['server_responsiveness']) : 0;
    $server_memory_management = isset($_POST['server_memory_management']) ? intval($_POST['server_memory_management']) : 0;
    $ocr_processing_speed = isset($_POST['ocr_processing_speed']) ? intval($_POST['ocr_processing_speed']) : 0;
    $navigation_ease = isset($_POST['navigation_ease']) ? intval($_POST['navigation_ease']) : 0;
    $user_friendly_interface = isset($_POST['user_friendly_interface']) ? intval($_POST['user_friendly_interface']) : 0;
    $responsive_design = isset($_POST['responsive_design']) ? intval($_POST['responsive_design']) : 0;
    $accessibility = isset($_POST['accessibility']) ? intval($_POST['accessibility']) : 0;

    $sql = "INSERT INTO user_feedback (user_id, line_user_id, display_name, design_appeal, ease_of_use, user_feedback_experience, notification_accuracy, feature_functionality, system_reliability, user_manual_completeness, page_load_speed, server_responsiveness, server_memory_management, ocr_processing_speed, navigation_ease, user_friendly_interface, responsive_design, accessibility, created_at, evaluated_date)
            VALUES (:user_id, :line_user_id, :display_name, :design_appeal, :ease_of_use, :user_feedback_experience, :notification_accuracy, :feature_functionality, :system_reliability, :user_manual_completeness, :page_load_speed, :server_responsiveness, :server_memory_management, :ocr_processing_speed, :navigation_ease, :user_friendly_interface, :responsive_design, :accessibility, current_timestamp(), CURDATE())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':line_user_id', $profile->userId, PDO::PARAM_STR);
    $stmt->bindParam(':display_name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':design_appeal', $design_appeal, PDO::PARAM_INT);
    $stmt->bindParam(':ease_of_use', $ease_of_use, PDO::PARAM_INT);
    $stmt->bindParam(':user_feedback_experience', $user_feedback_experience, PDO::PARAM_STR);
    $stmt->bindParam(':notification_accuracy', $notification_accuracy, PDO::PARAM_INT);
    $stmt->bindParam(':feature_functionality', $feature_functionality, PDO::PARAM_INT);
    $stmt->bindParam(':system_reliability', $system_reliability, PDO::PARAM_INT);
    $stmt->bindParam(':user_manual_completeness', $user_manual_completeness, PDO::PARAM_INT);
    $stmt->bindParam(':page_load_speed', $page_load_speed, PDO::PARAM_INT);
    $stmt->bindParam(':server_responsiveness', $server_responsiveness, PDO::PARAM_INT);
    $stmt->bindParam(':server_memory_management', $server_memory_management, PDO::PARAM_INT);
    $stmt->bindParam(':ocr_processing_speed', $ocr_processing_speed, PDO::PARAM_INT);
    $stmt->bindParam(':navigation_ease', $navigation_ease, PDO::PARAM_INT);
    $stmt->bindParam(':user_friendly_interface', $user_friendly_interface, PDO::PARAM_INT);
    $stmt->bindParam(':responsive_design', $responsive_design, PDO::PARAM_INT);
    $stmt->bindParam(':accessibility', $accessibility, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        text: 'ขอบคุณที่ให้ความคิดเห็น',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index';
                        }
                    });
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถบันทึกข้อมูลได้ โปรดลองอีกครั้ง',
                        confirmButtonText: 'OK'
                    });
                });
              </script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/loadweb.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
        }, 1500);
    });
    </script>

    <?php require_once("component/nav_feedback.php"); ?>

    <div class="container">
        <h2 class="mt-5">Feedback Form</h2>
        <h4 class="mt-1">แบบประเมินเว็ปไซต์ Medicine</h4>
        <h1>การประเมินจากประสบการ์ณผู้ใช้ (User Experience)</h1>
        <form method="post" id="feedback-form">
            <div class="mb-3">
            <label for="design_appeal" class="form-label">การออกแบบที่สวยงามและดึงดูด</label>
                <label for="design_appeal" class="form-label">Attractive and Appealing Design</label>
                <select class="form-select" id="design_appeal" name="design_appeal" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความสะดวกสะดวกสบายในการใช้งาน</label>
                <label for="ease_of_use" class="form-label">Ease of Use</label>
                <select class="form-select" id="ease_of_use" name="ease_of_use" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ฟีตแบ็กจากผู้ใช้งานเกี่ยวกับประสบการ์ณการใช้งาน</label>
                <label for="user_feedback_experience" class="form-label">User Feedback on Experience</label>
                <textarea class="form-control" id="user_feedback_experience" name="user_feedback_experience" rows="3" required></textarea>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความถูกต้องและความแม่นยำในการแจ้งเตือน</label>
                <label for="notification_accuracy" class="form-label">Accuracy and Precision of Notifications</label>
                <select class="form-select" id="notification_accuracy" name="notification_accuracy" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">การทำงานของฟีเจอร์ต่าง เช่น การแปลงข้อมูลรูปภาพซองยาเป็นข้อความ OCR PHOTO TO TEXT, การแจ้งเตือนผ่านไลน์</label>
                <label for="feature_functionality" class="form-label">Functionality of Features</label>
                <select class="form-select" id="feature_functionality" name="feature_functionality" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความน่าเชื่อถือของระบบในการแจ้งเตือน Line Official</label>
                <label for="system_reliability" class="form-label">Reliability of the Notification System</label>
                <select class="form-select" id="system_reliability" name="system_reliability" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความครบถ้วนและชัดเจนของคู่มือประกอบการสอนใช้งาน</label>
                <label for="user_manual_completeness" class="form-label">Completeness and Clarity of the User Manual</label>
                <select class="form-select" id="user_manual_completeness" name="user_manual_completeness" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <h1>การประเมินด้านประสิทธิภาพ (Performance)</h1>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความเร็วในการโหลดหน้าเว็ปไซต์</label>
                <label for="page_load_speed" class="form-label">Web Page Loading Speed</label>
                <select class="form-select" id="page_load_speed" name="page_load_speed" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">การตอบสนองของเซิร์ฟเวอร์</label>
                <label for="server_responsiveness" class="form-label">Server Responsiveness</label>
                <select class="form-select" id="server_responsiveness" name="server_responsiveness" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">การจัดการหน่อยความจำและทรัพยากรของเซิร์ฟเวอร์</label>
                <label for="server_memory_management" class="form-label">Memory and Resource Management of the Server</label>
                <select class="form-select" id="server_memory_management" name="server_memory_management" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความเร็วในการประมวลผลของ AI Tesseract OCR</label>
                <label for="ocr_processing_speed" class="form-label">Processing Speed of Tesseract OCR</label>
                <select class="form-select" id="ocr_processing_speed" name="ocr_processing_speed" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <h1>การประเมินด้าน การใช้งาน (Usability)</h1>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความง่ายในการนำทาง</label>
                <label for="navigation_ease" class="form-label">Ease of Navigation</label>
                <select class="form-select" id="navigation_ease" name="navigation_ease" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">อินเตอร์เฟชที่เป็นมิตรกับผู้ใช้</label>
                <label for="user_friendly_interface" class="form-label">User-friendly Interface</label>
                <select class="form-select" id="user_friendly_interface" name="user_friendly_interface" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">การออกแบบที่ตอบสนองกับผู้ใช้งานได้ง่าย</label>
                <label for="responsive_design" class="form-label">Responsive Design</label>
                <select class="form-select" id="responsive_design" name="responsive_design" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
            <label for="design_appeal" class="form-label">ความสามารถในการเข้าถึง</label>
                <label for="accessibility" class="form-label">Accessibility</label>
                <select class="form-select" id="accessibility" name="accessibility" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="submit-btn">ส่งแบบประเมิน</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
