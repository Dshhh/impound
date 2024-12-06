<?php
session_start();
include '../database.php';

// Ensure the user is an admin
if (!isset($_SESSION['officer_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Prepare the SQL statement to insert a new officer
    $sql = "INSERT INTO officers (first_name, last_name, username, password, role) 
            VALUES (?, ?, ?, ?, 'officer')";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "Error: " . $conn->error;
        exit();
    }

    // Bind the parameters
    $stmt->bind_param("ssss", $first_name, $last_name, $username, $password);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>
                alert('Record added successfully!');
                window.location.href = 'admin_dashboard.php';
            </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Impoundment Record Tracking System</title>
  <link rel="icon" href="../dist/img/SL.png" type="icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
</head>
<style>
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
</style>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
    <?php 
        include 'navbar.php';
        include 'sidebar.php'; 
    ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
            <div class="container-fluid">
                <div class="form-container">
                <h2>Create Officer Account</h2>
                <form method="POST">
                    <div class="form-section">
                    <h5>Officer Details</h5>
                    <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name: </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="Enter First Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name: </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="Enter Last Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">Username: </label>
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Enter Username">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password">Password: </label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter Password">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Record</button>
                    <a href="admin_dashboard.php" class="btn btn-danger">Cancel</a>
                </form>
                </div>
            </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- JavaScript to Update Date and Time -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script>
    // Display current date and time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('currentDateTime').innerText = now.toLocaleString();
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
</script>
</body>
</html>
