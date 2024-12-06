<?php
session_start();

// Ensure user is logged in and is an officer
if (!isset($_SESSION['officer_id']) || $_SESSION['role'] != 'officer') {
    header('Location: ../login.php');
    exit();
}

include '../database.php';

// Handle password change request
if (isset($_POST['change_password'])) {
    $officer_id = $_SESSION['officer_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM officers WHERE officer_id = ?");
    $stmt->bind_param("i", $officer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $current_password = $user_data['password'];

    // Check if old password matches the current password
    if (password_verify($old_password, $current_password)) {
        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Hash the new password and update in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE officers SET password = ? WHERE officer_id = ?");
            $update_stmt->bind_param("si", $hashed_password, $officer_id);
            $update_stmt->execute();

            $message = "<style>color: green;</style>Password successfully changed!";
        } else {
            $message = "New password and confirm password do not match!";
        }
    } else {
        $message = "Old password is incorrect!";
    }
}

// Fetch officer-specific stats for today
$officer_id = $_SESSION['officer_id'];
$sql_officer_stats = $conn->prepare("SELECT COUNT(*) AS record_count FROM impound_records WHERE officer_id = ? AND DATE(impound_date) = CURDATE()");
$sql_officer_stats->bind_param("i", $officer_id);
$sql_officer_stats->execute();
$result_stats = $sql_officer_stats->get_result();
$record_count = $result_stats->fetch_assoc()['record_count'];

// Fetch recent records for the officer
$sql_recent_records = $conn->prepare("SELECT ir.impound_date, v.vehicle_type, o.officer_id FROM impound_records ir
JOIN vehicles v ON ir.vehicle_id = v.vehicle_id
JOIN officers o ON ir.officer_id = o.officer_id
WHERE ir.officer_id = ? ORDER BY ir.impound_date DESC LIMIT 5");
$sql_recent_records->bind_param("i", $officer_id);
$sql_recent_records->execute();
$result_recent = $sql_recent_records->get_result();
$recent_records = $result_recent->fetch_all(MYSQLI_ASSOC);

// Fetch monthly record counts for the current year
$monthly_data = [];
$current_year = date('Y');
$sql_monthly_counts = "SELECT MONTH(impound_date) AS month, COUNT(*) AS count
                       FROM impound_records
                       WHERE YEAR(impound_date) = ?
                       GROUP BY month
                       ORDER BY month";

$stmt = $conn->prepare($sql_monthly_counts);
$stmt->bind_param("i", $current_year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $monthly_data[(int)$row['month'] - 1] = (int)$row['count'];
}

// Fill months with zero records to maintain 12 months in the dataset
for ($i = 0; $i < 12; $i++) {
    if (!isset($monthly_data[$i])) {
        $monthly_data[$i] = 0;
    }
}
ksort($monthly_data); // Ensure data is in order from January to December

// Convert data to JSON for use in JavaScript
$monthly_data_json = json_encode(array_values($monthly_data));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Impoundment Record Tracking System</title>
    <link rel="icon" href="../dist/img/SL.png" type="icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Date and Time Display -->
                <li class="nav-item">
                    <span id="currentDateTime" class="nav-link"></span>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../dist/img/logo.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">
                            <?php 
                                // Display Admin or Officer depending on session role
                                echo $_SESSION['role'] === 'admin' ? 'Administrator' : 'Officer'; 
                            ?>
                        </a>
                    </div>
                </div>
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item menu-open">
                            <a href="officer_dashboard.php" class="nav-link active"><i class="nav-icon fas fa-home"></i><p>Dashboard</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="officer_record.php" class="nav-link"><i class="fas fa-plus nav-icon"></i><p>Add Record</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt nav-icon"></i><p>Logout</p></a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <h3>Officer Dashboard</h3>

                    <div class="row">
                        <!-- Records Entered Today -->
                        <div class="card card-dark col-md-3">
                            <div class="card-header">
                                <h5>Records Entered Today:</h5>
                            </div>
                            <div class="card-body">
                                <p id="recordCounter" style="font-size: 4em; font-weight: bold; text-align: center;"><?php echo $record_count; ?></p>
                            </div>
                        </div>

                        <!-- Profile Section -->
                        <div class="card card-dark col-md-3">
                            <div class="card-header">
                                <h5>Profile</h5>
                            </div>
                            <div class="card-body" style="text-align: center;">
                                <p>Officer ID: <?php echo $_SESSION['officer_id']; ?></p>
                                <p>Username: <?php echo $_SESSION['username']; ?></p>
                                <p>Role: <?php echo $_SESSION['role']; ?></p>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card card-dark col-md-6">
                            <div class="card-header">
                                <h5>Recent Activity</h5>
                            </div>
                            <div class="card-body" style="text-align: center;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Vehicle Type</th>
                                            <th>Officer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_records as $record): ?>
                                            <tr>
                                                <td><?php echo $record['impound_date']; ?></td>
                                                <td><?php echo $record['vehicle_type']; ?></td>
                                                <td><?php echo $record['officer_id']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Form -->
                    <div class="row">
                        <div class="card card-dark col-md-12">
                            <div class="card-header">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($message)) { echo "<p style='color:red;'>$message</p>"; } ?>
                                <form action="" method="POST">
                                    <div class="form-group">
                                        <label for="old_password">Old Password</label>
                                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- Counter Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set the target count (use PHP to pass the value into JavaScript)
        const targetCount = <?php echo $record_count; ?>;
        
        // Initialize variables for the counter
        let count = 0;
        const interval = Math.ceil(2000 / targetCount); // Duration of 2 seconds in ms divided by count
        
        // Counter animation function
        const counter = setInterval(function() {
            if (count < targetCount) {
                count++;
                document.getElementById('recordCounter').innerText = count;
            } else {
                clearInterval(counter); // Stop the counter when target is reached
            }
        }, interval);
    });
</script>
</body>
</html>
