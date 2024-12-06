<?php
session_start();
include '../database.php';

// Check if the user is logged in
if (!isset($_SESSION['officer_id']) || $_SESSION['role'] != 'officer') {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve input
    $color = htmlspecialchars(trim($_POST['color']));
    $orcr_number = htmlspecialchars(trim($_POST['orcr_number']));
    $vehicle_type = htmlspecialchars(trim($_POST['vehicle_type']));
    $chassis_number = htmlspecialchars(trim($_POST['chassis_number']));
    $engine_number = htmlspecialchars(trim($_POST['engine_number']));
    $plate_number = htmlspecialchars(trim($_POST['plate_number']));

    $rider_first_name = htmlspecialchars(trim($_POST['rider_first_name']));
    $rider_middle_name = htmlspecialchars(trim($_POST['rider_middle_name']));
    $rider_last_name = htmlspecialchars(trim($_POST['rider_last_name']));
    $rider_age = intval($_POST['rider_age']);
    $rider_gender = htmlspecialchars(trim($_POST['rider_gender']));
    $rider_address = htmlspecialchars(trim($_POST['rider_address']));

    $owner_first_name = htmlspecialchars(trim($_POST['owner_first_name']));
    $owner_middle_name = htmlspecialchars(trim($_POST['owner_middle_name']));
    $owner_last_name = htmlspecialchars(trim($_POST['owner_last_name']));
    $owner_age = intval($_POST['owner_age']);
    $owner_gender = htmlspecialchars(trim($_POST['owner_gender']));
    $owner_address = htmlspecialchars(trim($_POST['owner_address']));

    $violations = isset($_POST['violations_type']) ? htmlspecialchars(trim($_POST['violations_type'])) : null;
    $officer_id = $_SESSION['officer_id'];

    // Database transaction for consistency
    $conn->begin_transaction();

    try {
        // Insert into `vehicles`
        $stmt = $conn->prepare("INSERT INTO vehicles (color, orcr_number, vehicle_type, chassis_number, engine_number, plate_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $color, $orcr_number, $vehicle_type, $chassis_number, $engine_number, $plate_number);
        $stmt->execute();
        $vehicle_id = $stmt->insert_id;

        // Insert into `riders`
        $stmt = $conn->prepare("INSERT INTO riders (rider_first_name, rider_middle_name, rider_last_name, rider_age, rider_gender, rider_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $rider_first_name, $rider_middle_name, $rider_last_name, $rider_age, $rider_gender, $rider_address);
        $stmt->execute();
        $rider_id = $stmt->insert_id;

        // Insert into `registered_owners`
        $stmt = $conn->prepare("INSERT INTO registered_owners (owner_first_name, owner_middle_name, owner_last_name, owner_age, owner_gender, owner_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $owner_first_name, $owner_middle_name, $owner_last_name, $owner_age, $owner_gender, $owner_address);
        $stmt->execute();
        $owner_id = $stmt->insert_id;

        // Handle violations
        $violation_id = null;
        if ($violations) {
            $stmt = $conn->prepare("INSERT INTO violations (violations_type) VALUES (?)");
            $stmt->bind_param("s", $violations);
            $stmt->execute();
            $violation_id = $stmt->insert_id;
        }

        // Insert into `impound_records`
        if ($violation_id) {
            $stmt = $conn->prepare("INSERT INTO impound_records (vehicle_id, rider_id, owner_id, violation_id, officer_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiii", $vehicle_id, $rider_id, $owner_id, $violation_id, $officer_id);
            $stmt->execute();
            $conn->commit();

            echo "<script>
                    alert('Record added successfully!');
                    window.location.href = 'officer_dashboard.php';
                  </script>";
        } else {
            throw new Exception('No violations recorded.');
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Error: " . $e->getMessage() . "');
                window.location.href = 'officer_record.php';
              </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Impoundment Record Tracking System</title>
  <link rel="icon" href="../dist/img/SL.png" type="icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
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
                    <img src="../dist/img/user.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">
                        <?php echo $_SESSION['role'] === 'admin' ? 'Administrator' : 'Officer'; ?>
                    </a>
                </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="officer_dashboard.php" class="nav-link"><i class="nav-icon fas fa-address-card"></i><p>Profile</p></a>
                </li>
                <li class="nav-item">
                    <a href="officer_record.php" class="nav-link active"><i class="fas fa-plus nav-icon"></i><p>Add Record</p></a>
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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
            <div class="container-fluid">
                <div class="form-container">
                <h2>Impoundment Receipt</h2>
                <form method="POST">
                    <!-- Violations -->
                    <div class="form-section">
                        <h5>Violations</h5>
                        <div>
                            <label><input type="checkbox" class="violation-checkbox" value="No Helmet"> No Helmet</label>
                            <label><input type="checkbox" class="violation-checkbox" value="No Drivers License"> No Drivers License</label>
                            <label><input type="checkbox" class="violation-checkbox" value="Failed to show ORCR"> Failed to show ORCR</label><br>
                            <label><input type="checkbox" class="violation-checkbox" value="No Plate Number"> No Plate Number</label>
                            <label><input type="checkbox" class="violation-checkbox" value="Mudiffied Muffler"> Mudiffied Muffler</label>
                            <label>
                                <input type="checkbox" id="othersCheckbox" class="violation-checkbox" value="Others"> Others
                            </label>
                            <input type="text" id="othersInput" class="form-control mt-2" placeholder="Specify Other Violation" style="display: none;">
                        </div>
                        <div class="mt-3">
                            <textarea id="violationsTextarea" class="form-control" name="violations_type" rows="3" readonly></textarea>
                        </div>
                    </div>
                    <div class="form-section">
                    <h5>Vehicle Details</h5>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="color">Color</label>
                            <input type="text" class="form-control" id="color" name="color"  maxlength="20" required placeholder="Enter Color">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="orcr_number">ORCR Number</label>
                            <input type="text" class="form-control" id="orcr_number" name="orcr_number" maxlength="20" placeholder="Enter ORCR No.">
                        </div>
                        <div class="form-group col-md-4">
                                <label for="vehicle_type">Make/Type</label>
                                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" maxlength="20" required placeholder="Enter Make/Type">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="chassis_number">Chassis Number</label>
                            <input type="text" class="form-control" id="chassis_number" name="chassis_number" maxlength="20" required placeholder="Enter Chassis No.">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="engine_number">Engine Number</label>
                            <input type="text" class="form-control" id="engine_number" name="engine_number" maxlength="10" required placeholder="Enter Engine No.">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="plate_number">Plate Number</label>
                            <input type="text" class="form-control" id="plate_number" name="plate_number" maxlength="8" placeholder="Enter Plate No.">
                        </div>
                    </div>
                    </div>
                    <!-- Rider Details Section -->
                    <div class="form-section">
                        <h5>Rider Details</h5>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="rider_first_name">First Name</label>
                                <input type="text" class="form-control" id="rider_first_name" name="rider_first_name" required placeholder="Enter First Name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="rider_middle_name">Middle Name</label>
                                <input type="text" class="form-control" id="rider_middle_name" name="rider_middle_name" placeholder="Enter Middle Name (Optional)">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="rider_last_name">Last Name</label>
                                <input type="text" class="form-control" id="rider_last_name" name="rider_last_name" required placeholder="Enter Last Name">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="rider_age">Age</label>
                                <input type="number" class="form-control" id="rider_age" name="rider_age" required placeholder="Enter Age">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="rider_gender">Gender</label>
                                <select class="form-control" id="rider_gender" name="rider_gender" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="license_number">License No.</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" maxlength="20" required placeholder="Enter License No.">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="rider_address">Address</label>
                                <input type="text" class="form-control" id="rider_address" name="rider_address" required placeholder="Enter Address">
                            </div>
                        </div>
                    </div>

                    <!-- Copy Button for Rider Details -->
                    <div class="text-left">
                        <button type="button" class="btn btn-secondary" onclick="copyRiderDetails()">Would you like to input the same details?</button>
                    </div>

                    <!-- Registered Owner Details Section -->
                    <div class="form-section">
                        <h5>Registered Owner Details</h5>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="owner_first_name">First Name</label>
                                <input type="text" class="form-control" id="owner_first_name" name="owner_first_name" required placeholder="Enter First Name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="owner_middle_name">Middle Name</label>
                                <input type="text" class="form-control" id="owner_middle_name" name="owner_middle_name" placeholder="Enter Middle Name (Optional)">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="owner_last_name">Last Name</label>
                                <input type="text" class="form-control" id="owner_last_name" name="owner_last_name" required placeholder="Enter Last Name">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="owner_age">Age</label>
                                <input type="number" class="form-control" id="owner_age" name="owner_age" required placeholder="Enter Age">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="owner_gender">Gender</label>
                                <select class="form-control" id="owner_gender" name="owner_gender" required placeholder="Select Gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="owner_address">Address</label>
                                <input type="text" class="form-control" id="owner_address" name="owner_address" required placeholder="Enter Address">
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
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
<script>
    function copyRiderDetails() {
        // Get rider details
        const riderFirstName = document.getElementById('rider_first_name').value;
        const riderMiddleName = document.getElementById('rider_middle_name').value;
        const riderLastName = document.getElementById('rider_last_name').value;
        const riderAge = document.getElementById('rider_age').value;
        const riderGender = document.getElementById('rider_gender').value;
        const riderAddress = document.getElementById('rider_address').value;

        // Set owner details
        document.getElementById('owner_first_name').value = riderFirstName;
        document.getElementById('owner_middle_name').value = riderMiddleName;
        document.getElementById('owner_last_name').value = riderLastName;
        document.getElementById('owner_age').value = riderAge;
        document.getElementById('owner_gender').value = riderGender;
        document.getElementById('owner_address').value = riderAddress;
    }
    // Display current date and time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDateTime').innerText = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

        document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll(".violation-checkbox");
        const othersCheckbox = document.getElementById("othersCheckbox");
        const othersInput = document.getElementById("othersInput");
        const violationsTextarea = document.getElementById("violationsTextarea");

        // Toggle visibility of the "Others" input field
        othersCheckbox.addEventListener("change", function () {
            othersInput.style.display = this.checked ? "block" : "none";
            if (!this.checked) othersInput.value = ""; // Clear the input if unchecked
            updateTextarea();
        });

        // Update the textarea whenever a checkbox or the "Others" input changes
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", updateTextarea);
        });

        othersInput.addEventListener("input", updateTextarea);

        function updateTextarea() {
            const selectedViolations = Array.from(checkboxes)
                .filter((checkbox) => checkbox.checked && checkbox.value !== "Others")
                .map((checkbox) => checkbox.value);

            // Include the "Others" input value if the checkbox is checked
            if (othersCheckbox.checked && othersInput.value.trim() !== "") {
                selectedViolations.push(othersInput.value.trim());
            }

            // Update the textarea
            violationsTextarea.value = selectedViolations.join(", ");
        }
    });
    document.addEventListener("DOMContentLoaded", function () {
        const orcrCheckbox = document.querySelector('input[value="Failed to show ORCR"]');
        const plateCheckbox = document.querySelector('input[value="No Plate Number"]');
        const licenseCheckbox = document.querySelector('input[value="No Drivers License"]');
        
        const orcrInput = document.getElementById("orcr_number");
        const plateInput = document.getElementById("plate_number");
        const licenseInput = document.getElementById("license_number");

        // Function to toggle input fields based on checkbox states
        function toggleInputFields() {
            orcrInput.disabled = orcrCheckbox.checked;
            plateInput.disabled = plateCheckbox.checked;
            licenseInput.disabled = licenseCheckbox.checked;
        }

        // Add event listeners to the checkboxes
        orcrCheckbox.addEventListener("change", toggleInputFields);
        plateCheckbox.addEventListener("change", toggleInputFields);
        licenseCheckbox.addEventListener("change", toggleInputFields);

        // Initialize the fields' states on page load
        toggleInputFields();
    });
</script>
</body>
</html>
