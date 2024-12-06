<?php
session_start();
include '../database.php';

// Ensure user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// Handle update request
if (isset($_GET['release_id'])) {
    $release_id = $_GET['release_id'];
    $release_date = date('Y-m-d H:i:s'); // Get the current date and time
    $update_sql = "UPDATE impound_records SET status = 'Pending', release_date = ? WHERE record_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $release_date, $release_id); // Bind the date and ID parameters
    
    if ($stmt->execute()) {
        // Redirect to the same page after update
        header("Location: impounded_vehicles.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Fetch impounded vehicles with joined data
$sql = "
    SELECT impound_records.record_id, impound_records.impound_date, vehicles.orcr_number, 
           vehicles.chassis_number, vehicles.engine_number, vehicles.plate_number,
           registered_owners.owner_first_name, registered_owners.owner_last_name,
           riders.rider_first_name, riders.rider_last_name, violations.violations_type, 
           officers.first_name AS officer_first_name, officers.last_name AS officer_last_name,
           impound_records.status, impound_records.release_date
    FROM impound_records
    JOIN vehicles ON impound_records.vehicle_id = vehicles.vehicle_id
    JOIN registered_owners ON impound_records.owner_id = registered_owners.owner_id
    JOIN riders ON impound_records.rider_id = riders.rider_id
    JOIN violations ON impound_records.violation_id = violations.violation_id
    JOIN officers ON impound_records.officer_id = officers.officer_id
    WHERE impound_records.status = 'Impounded'
";
$result = mysqli_query($conn, $sql);

// Store all rows in an array
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
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
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        td, th {
            white-space: nowrap;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php 
        include 'navbar.php';
        include 'sidebar.php'; 
    ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-dark">
                    <div class="card-header">
                        <h5 class="card-title">Impounded Vehicles</h5>
                    </div>
                    <div class="card-body">
                        <table id="impoundedVehiclesTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Record ID</th>
                                    <th>Impound Date</th>
                                    <th>Owner Name</th>
                                    <th>ORCR Number</th>
                                    <th>Chassis Number</th>
                                    <th>Engine Number</th>
                                    <th>Plate Number</th>
                                    <th>Rider Name</th>
                                    <th>Violations</th>
                                    <th>Officer</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: justify;">
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                                        <td><?php echo htmlspecialchars(date('F j, Y', strtotime($row['impound_date']))); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_first_name'] . ' ' . $row['owner_last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['orcr_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['chassis_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['engine_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['rider_first_name'] . ' ' . $row['rider_last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['violations_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['officer_first_name'] . ' ' . $row['officer_last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td> <!-- Display Status -->
                                        <td>
                                            <a href="?release_id=<?php echo $row['record_id']; ?>" class="btn btn-success btn-sm">Release</a>
                                        </td> <!-- Release Button -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
<script src="../dist/js/adminlte.js"></script>
<!-- DataTables & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
    $(function () {
        $("#impoundedVehiclesTable").DataTable({
            "responsive": true, 
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ records",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries available",
                "emptyTable": "No released data available in table",
                "zeroRecords": "No matching records found",
            }
        }).buttons().container().appendTo('#impoundedVehiclesTable_wrapper .col-md-6:eq(0)');

        // Display current date and time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('currentDateTime').innerText = now.toLocaleString();
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    });
</script>
</body>
</html>
