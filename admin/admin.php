<?php
require_once('../LineLogin.php');
##session_start(); // Ensure session is started
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* CSS Style to add shadow to Navbar */
        .navbar {
            box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.1); /* Add shadow with specific values */
        }
    </style>
</head>
<body>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Navbar brand with image -->
        <a class="navbar-brand" href="../welcome.php">
            <img src="assets/images/dog.jpg" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
            MEDIC TEST 1.0 (LINE)
        </a>
        <!-- Navbar toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="btn btn-primary nav-link active" aria-current="page" href="index.php">หน้าแรก</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary nav-link" href="admin.php">ระบบหลังบ้าน</a>
                </li>
            </ul>
            <div class="col-md-3 text-end">
                <a href="./logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h1 class="text-center mb-4">Admin Dashboard</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome, <?php echo $_SESSION['profile']->displayName; ?></h5>
                    <p class="card-text">This is the admin page.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript Bundle with Popper. -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
