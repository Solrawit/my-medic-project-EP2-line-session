<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Title</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    /* CSS Style to add shadow to Navbar */
    .navbar {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow with specific values */
      transition: box-shadow 0.3s ease; /* Smooth transition for box-shadow */
    }

    .navbar:hover {
      box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Larger shadow on hover */
    }

    .navbar-brand img {
      transition: transform 0.3s ease;
    }

    .navbar-brand img:hover {
      transform: scale(1.1); /* Scale up on hover */
    }

    /* Sticky Navbar Animation */
    .sticky-top {
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: top 0.3s ease-in-out; /* Smooth transition for top position */
    }

    .sticky-top.navbar-scrolled {
      top: -60px; /* Adjust as per your Navbar height */
    }
  </style>
</head>
<body>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- Navbar brand with image -->
    <a class="navbar-brand" href="index.php">
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
          <a class="nav-link" href="index.php"><i class="fa fa-home fa-lg me-1"></i>หน้าหลัก</a>
        </li>
        <!-- Add more menu items here -->
      </ul>
      <div class="col-md-3 text-end">
        <?php 
        if (!isset($_SESSION['profile'])) {
            // เรียกใช้งานคลาส LineLogin หรือเมทอดที่ให้คุณสร้างลิงก์การเข้าสู่ระบบ Line แล้วกำหนดให้ตัวแปร $link
            $line = new LineLogin();
            $link = $line->getLink();
        ?>
        <a href="<?php echo $link; ?>" class="btn btn-success me-2">
          <img src="assets/images/line.png" alt="LINE Logo" width="20" height="20" class="me-1">
          LOGIN WITH LINE
        </a>
        <?php } else { ?>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php } ?>
      </div>
    </div>
  </div>
</nav>

<!-- Bootstrap JavaScript Bundle with Popper. -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // jQuery to collapse the Navbar on scroll
  $(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
      $(".navbar").addClass("navbar-scrolled");
    } else {
      $(".navbar").removeClass("navbar-scrolled");
    }
  });
</script>
</body>
</html>
