<?php
session_start();
include '../database.php';

// Ensure user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

// SQL query to fetch impounded vehicle records
$sql = "
    SELECT 
        impound_records.record_id,
        impound_records.impound_date,
        vehicles.color, 
        vehicles.orcr_number, 
        vehicles.vehicle_type, 
        vehicles.chassis_number, 
        vehicles.engine_number, 
        vehicles.plate_number,
        
        registered_owners.owner_first_name, 
        registered_owners.owner_middle_name, 
        registered_owners.owner_last_name,
        registered_owners.owner_age, 
        registered_owners.owner_gender, 
        registered_owners.owner_address,
        
        riders.rider_first_name, 
        riders.rider_middle_name, 
        riders.rider_last_name, 
        riders.rider_age, 
        riders.rider_gender,
        riders.license_number,
        riders.rider_address,
        
        violations.violations_type, 
        officers.first_name AS officer_first_name, 
        officers.last_name AS officer_last_name

    FROM impound_records
    JOIN vehicles ON impound_records.vehicle_id = vehicles.vehicle_id
    JOIN registered_owners ON impound_records.owner_id = registered_owners.owner_id
    JOIN riders ON impound_records.rider_id = riders.rider_id
    JOIN violations ON impound_records.violation_id = violations.violation_id
    JOIN officers ON impound_records.officer_id = officers.officer_id
";

// Include date filtering if start_date and end_date are provided
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

$where_conditions = [];
if ($start_date) {
    $where_conditions[] = "impound_records.impound_date >= '" . mysqli_real_escape_string($conn, $start_date) . "'";
}
if ($end_date) {
    $where_conditions[] = "impound_records.impound_date <= '" . mysqli_real_escape_string($conn, $end_date) . "'";
}

// Append WHERE clause if conditions exist
if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(' AND ', $where_conditions);
}

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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>
<style>
    .table-responsive {
        overflow-x: auto;
        width: 100%;
        display: block;
    }
    #impoundedTable {
        width: 100%;
    }
    td, th {
        white-space: nowrap;
        padding: 10px;
    }
</style>
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
                        <h5 class="card-title">All Records</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
                                </div>
                                <div class="form-group col-md-2 align-self-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="impoundedTable" class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Record ID</th>
                                        <th>Impound Date</th>
                                        <th>License Number</th>
                                        <th>Make/Type</th>
                                        <th>Chassis Number</th>
                                        <th>Engine Number</th>
                                        <th>Plate Number</th>
                                        <th>Color</th>
                                        <th>ORCR Number</th>
                                        <th>Owner Name</th>
                                        <th>Owner Age</th>
                                        <th>Owner Gender</th>
                                        <th>Owner Address</th>
                                        <th>Rider</th>
                                        <th>Rider Age</th>
                                        <th>Rider Gender</th>
                                        <th>Rider Address</th>
                                        <th>Violations</th>
                                        <th>Officer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: justify;">
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                                            <td><?php echo htmlspecialchars(date('F j, Y', strtotime($row['impound_date']))); ?></td>
                                            <td><?php echo htmlspecialchars($row['license_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['chassis_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['engine_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['color']); ?></td>
                                            <td><?php echo htmlspecialchars($row['orcr_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['owner_first_name'] . ' ' . $row['owner_middle_name'] . ' ' . $row['owner_last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['owner_age']); ?></td>
                                            <td><?php echo htmlspecialchars($row['owner_gender']); ?></td>
                                            <td><?php echo htmlspecialchars($row['owner_address']); ?></td>
                                            <td><?php echo htmlspecialchars($row['rider_first_name'] . ' ' . $row['rider_middle_name'] . ' ' . $row['rider_last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['rider_age']); ?></td>
                                            <td><?php echo htmlspecialchars($row['rider_gender']); ?></td>
                                            <td><?php echo htmlspecialchars($row['rider_address']); ?></td>
                                            <td><?php echo htmlspecialchars($row['violations_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['officer_first_name'] . ' ' . $row['officer_last_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                                                <form action="print.php" method="POST" target="_blank" style="display: inline;">
                                                    <input type="hidden" name="record_id" value="<?php echo $row['record_id']; ?>">
                                                    <input type="hidden" name="vehicle_type" value="<?php echo htmlspecialchars($row['vehicle_type']); ?>">
                                                    <input type="hidden" name="chassis_number" value="<?php echo htmlspecialchars($row['chassis_number']); ?>">
                                                    <input type="hidden" name="engine_number" value="<?php echo htmlspecialchars($row['engine_number']); ?>">
                                                    <input type="hidden" name="plate_number" value="<?php echo htmlspecialchars($row['plate_number']); ?>">
                                                    <input type="hidden" name="color" value="<?php echo htmlspecialchars($row['color']); ?>">
                                                    <input type="hidden" name="orcr_number" value="<?php echo htmlspecialchars($row['orcr_number']); ?>">
                                                    <input type="hidden" name="owner_last_name" value="<?php echo htmlspecialchars($row['owner_last_name']); ?>">
                                                    <input type="hidden" name="owner_first_name" value="<?php echo htmlspecialchars($row['owner_first_name']); ?>">
                                                    <input type="hidden" name="owner_middle_name" value="<?php echo htmlspecialchars($row['owner_middle_name']); ?>">
                                                    <input type="hidden" name="owner_age" value="<?php echo htmlspecialchars($row['owner_age']); ?>">
                                                    <input type="hidden" name="owner_gender" value="<?php echo htmlspecialchars($row['owner_gender']); ?>">
                                                    <input type="hidden" name="owner_address" value="<?php echo htmlspecialchars($row['owner_address']); ?>">
                                                    <input type="hidden" name="rider_last_name" value="<?php echo htmlspecialchars($row['rider_last_name']); ?>">
                                                    <input type="hidden" name="rider_first_name" value="<?php echo htmlspecialchars($row['rider_first_name']); ?>">
                                                    <input type="hidden" name="rider_middle_name" value="<?php echo htmlspecialchars($row['rider_middle_name']); ?>">
                                                    <input type="hidden" name="rider_age" value="<?php echo htmlspecialchars($row['rider_age']); ?>">
                                                    <input type="hidden" name="rider_gender" value="<?php echo htmlspecialchars($row['rider_gender']); ?>">
                                                    <input type="hidden" name="rider_address" value="<?php echo htmlspecialchars($row['rider_address']); ?>">
                                                    <input type="hidden" name="violation" value="<?php echo htmlspecialchars($row['violations_type']); ?>">
                                                    <input type="hidden" name="officer_name" value="<?php echo htmlspecialchars($row['officer_first_name'] . ' ' . $row['officer_last_name']); ?>">
                                                    <button type="submit" class="btn btn-secondary btn-sm">Print</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="record_id" id="record_id">
                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" class="form-control" id="color" name="color">
                    </div>
                    <div class="form-group">
                        <label for="orcr_number">ORCR Number</label>
                        <input type="text" class="form-control" id="orcr_number" name="orcr_number">
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type">
                    </div>
                    <div class="form-group">
                        <label for="chassis_number">Chassis Number</label>
                        <input type="text" class="form-control" id="chassis_number" name="chassis_number">
                    </div>
                    <div class="form-group">
                        <label for="engine_number">Engine Number</label>
                        <input type="text" class="form-control" id="engine_number" name="engine_number">
                    </div>
                    <div class="form-group">
                        <label for="plate_number">Plate Number</label>
                        <input type="text" class="form-control" id="plate_number" name="plate_number">
                    </div>
                    <div class="form-group">
                        <label for="license_number">License Number</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>

<script>
    $(function () {
        $("#impoundedTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "lengthMenu": "Show _MENU_ Records per page",
                "zeroRecords": "No matching records found",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total records)"
            }
        });
    });
    
    // Display current date and time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDateTime').innerText = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    function openEditModal(record) {
        document.getElementById('record_id').value = record.record_id;
        document.getElementById('color').value = record.color;
        document.getElementById('orcr_number').value = record.orcr_number;
        document.getElementById('vehicle_type').value = record.vehicle_type;
        document.getElementById('chassis_number').value = record.chassis_number;
        document.getElementById('engine_number').value = record.engine_number;
        document.getElementById('plate_number').value = record.plate_number;
        document.getElementById('license_number').value = record.license_number;
        $('#editModal').modal('show');
    }

    document.getElementById('editForm').onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('edit_record.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editModal').modal('hide');
                location.reload();
            } else {
                alert('Failed to update record');
            }
        })
        .catch(error => console.error('Error:', error));
    };
</script>

</body>
</html>
