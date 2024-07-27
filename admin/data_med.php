<?php
require_once('../db_connection.php');
include '../timeout.php';

session_start();

if (!isset($_SESSION['profile']) || $_SESSION['role'] != 'admin') {
    header('Location: ./welcome');
    exit;
}

// เชื่อมต่อกับฐานข้อมูล MySQL
$host = 'localhost';
$dbname = 'mdpj_user';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage());
}

// รับค่า action จากการกดปุ่มแก้ไขหรือลบ หรือเพิ่ม
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM medicine WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลเรียบร้อย']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
            exit;
        }
    } elseif ($_POST['action'] == 'edit' && isset($_POST['id']) && isset($_POST['newName'])) {
        $id = $_POST['id'];
        $newName = $_POST['newName'];
        $stmt = $pdo->prepare("UPDATE medicine SET text_column = :newName WHERE id = :id");
        $stmt->bindParam(':newName', $newName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'แก้ไขข้อมูลเรียบร้อย']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
            exit;
        }
    } elseif ($_POST['action'] == 'add' && isset($_POST['medicineName'])) {
        $medicineName = $_POST['medicineName'];
        $stmt = $pdo->prepare("INSERT INTO medicine (text_column) VALUES (:medicineName)");
        $stmt->bindParam(':medicineName', $medicineName, PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'เพิ่มข้อมูลเรียบร้อย']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล']);
            exit;
        }
    }
}

// ทำการส่งคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง medicine
$stmt = $pdo->query("SELECT * FROM medicine");
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> <!-- ใช้dropdownไม่ได้เพราะ2scriptนี้ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            position: relative;
            font-family: 'Sarabun', sans-serif;
            padding: 0px 0px;
        }
        .blurry-img {
            filter: blur(10px); /* Adjust as needed */
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
            background-image: url('../assets/images/7788.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        h1 {
            color: white;
        }
        
    </style>
</head>
<body>
<?php require_once("../component/nav_admin.php"); ?>

<div class="container mt-5">
    <h1>MANAGE DATA MEDICINES</h1>
    <p style="color: white; font-size: 1.5rem;">ข้อมูลยาทั้งหมด</p>
    <br>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
        เพิ่มข้อมูลยา
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addMedicineModalLabel">เพิ่มข้อมูลยา</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addMedicineForm">
            <div class="modal-body">
              <div class="mb-3">
                <label for="medicineName" class="form-label">ชื่อยา</label>
                <input type="text" class="form-control" id="medicineName" name="medicineName" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
              <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th>ID</th>
                <th>Medicine Name</th>
                <th>EDIT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicines as $medicine): ?>
                <tr>
                    <td><?php echo htmlspecialchars($medicine['id']); ?></td>
                    <td><?php echo htmlspecialchars($medicine['text_column']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $medicine['id']; ?>">แก้ไข</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $medicine['id']; ?>">ลบ</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../component/footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ฟังก์ชั่นสำหรับลบข้อมูล
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบข้อมูลนี้หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ฉันต้องการลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'data_med.php',
                    data: { action: 'delete', id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'เกิดข้อผิดพลาดในการลบข้อมูล',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // ฟังก์ชั่นสำหรับแก้ไขข้อมูล
    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'แก้ไขชื่อยา',
            html: '<input id="edit-input" class="swal2-input" value="' + id + '">',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                var newValue = $('#edit-input').val();
                $.ajax({
                    type: 'POST',
                    url: 'data_med.php',
                    data: { action: 'edit', id: id, newName: newValue },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'บันทึกสำเร็จ!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // ฟังก์ชั่นสำหรับเพิ่มข้อมูล
    $('#addMedicineForm').submit(function(event) {
        event.preventDefault();
        var medicineName = $('#medicineName').val();
        $.ajax({
            type: 'POST',
            url: 'data_med.php',
            data: { action: 'add', medicineName: medicineName },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'เพิ่มข้อมูลสำเร็จ!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล',
                    icon: 'error'
                });
            }
        });
    });
</script>
</body>
</html>
