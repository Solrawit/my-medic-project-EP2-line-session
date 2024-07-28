<?php
require_once('../LineLogin.php');
require_once('../db_connection.php');

// ดึงข้อมูลตั้งค่าเว็บไซต์
$siteSettings = getSiteSettings($db);
$siteName = isset($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Default Site Name';
$contactEmail = isset($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'default@example.com';
$siteNav = isset($siteSettings['site_nav']) ? $siteSettings['site_nav'] : 'Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0D309D;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding-top: 20px;
            z-index: 1000;
            transition: width 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar .nav-link {
            color: #ffffff;
            padding: 15px;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: #0044cc;
            padding-left: 25px;
            color: #ffffff;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            transition: margin-left 0.3s;
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.1);
        }

        .dropdown {
            width: 100%;
            padding: 10px;
        }

        .dropdown .dropdown-toggle {
            width: 100%;
            transition: background-color 0.3s;
        }

        .dropdown .dropdown-menu {
            width: 100%;
            left: 0 !important;
            right: 0 !important;
        }

        .dropdown .dropdown-toggle:hover {
            background-color: #f8f9fa;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar .nav-link {
            animation: slideIn 0.5s ease;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div>
        <center><a class="navbar-brand text-white" href="admin">
            <img src="../uploads/<?php echo htmlspecialchars($siteSettings['image_path']); ?>" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            <b>MEDICINE ADMIN</b>
        </a></center>

        <hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin"><i class="fa fa-home fa-lg"></i> หน้าหลัก</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="website_settings"><i class="fa fa-cogs fa-lg"></i> ตั้งค่าเว็ปไซต์</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_feedback"><i class="fa fa-comments"></i> ข้อมูลการประเมิน</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="data_med"><i class="fa-solid fa-suitcase-medical"></i> ฐานข้อมูลยา</a>
            </li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="met_userdata"><i class="fa-solid fa-hand-holding-medical"></i> ข้อมูลการแจ้งเตือนยาผู้ใช้</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users"><i class="fa fa-users"></i> จัดการผู้ใช้</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users"><i class="fa fa-users"></i> จัดการผู้ใช้ทั่วไป (เก่า)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lineuser"><i class="fa fa-users"></i> จัดการผู้ใช้ไลน์</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="../welcome"><i class="fa fa-sign-out"></i> ออกจากหลังบ้าน</a>
            </li>
        </ul>
    </div>
    <hr class="featurette-divider" style="background-color: white; height: 1px; border: none;">
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (!empty($profile->pictureUrl)): ?>
                <img src="<?php echo htmlspecialchars($profile->pictureUrl); ?>" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px;">
            <?php endif; ?>
            <b>Mr. : <?php echo htmlspecialchars($profile->displayName); ?></b>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <!--<li><a class="dropdown-item" href="../profile.php"><i class="fa fa-user" aria-hidden="true"></i> โปรไฟล์ของฉัน</a></li>-->
            <li><a class="dropdown-item" href="../logout"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>
</div>

<div class="content">
    <!-- เนื้อหาหลักของหน้า -->
</div>

<!-- Bootstrap JavaScript Bundle with Popper. -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
