<?php
session_start();
include '../database.php';

// Ensure user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch all officers
$sql = "SELECT officer_id, first_name, last_name, username FROM officers";
$result = mysqli_query($conn, $sql);
$officers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $officers[] = $row;
}

// Handle password or username updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $officer_id = $_POST['officer_id'];
    $new_password = $_POST['new_password'] ?? null;
    $new_username = $_POST['new_username'] ?? null;

    if ($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE officers SET password = '$hashed_password' WHERE officer_id = '$officer_id'";
    } elseif ($new_username) {
        $update_sql = "UPDATE officers SET username = '$new_username' WHERE officer_id = '$officer_id'";
    }

    if (mysqli_query($conn, $update_sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Impoundment Record Tracking System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="icon" href="../dist/img/SL.png" type="icon">
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
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
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-dark">
                        <div class="card-header">
                            <h3 class="card-title">Officer Profiles</h3>
                        </div>
                        <div class="card-body">
                            <table id="officerTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($officers as $officer): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($officer['officer_id']); ?></td>
                                            <td><?php echo htmlspecialchars($officer['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($officer['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($officer['username']); ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="editUsername('<?php echo $officer['officer_id']; ?>', '<?php echo $officer['username']; ?>')">Change Username</button>
                                                <button class="btn btn-secondary btn-sm" onclick="editPassword('<?php echo $officer['officer_id']; ?>')">Change Password</button>
                                            </td>
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

<!-- Modals -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="officer_id" id="officer_id">
                    <div class="form-group" id="usernameGroup">
                        <label for="new_username">New Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username">
                    </div>
                    <div class="form-group" id="passwordGroup" style="display: none;">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
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
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        $("#officerTable").DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });

    function editUsername(officerId, currentUsername) {
        $('#modalTitle').text('Change Username');
        $('#officer_id').val(officerId);
        $('#new_username').val(currentUsername);
        $('#usernameGroup').show();
        $('#passwordGroup').hide();
        $('#editModal').modal('show');
    }

    function editPassword(officerId) {
        $('#modalTitle').text('Change Password');
        $('#officer_id').val(officerId);
        $('#new_password').val('');
        $('#usernameGroup').hide();
        $('#passwordGroup').show();
        $('#editModal').modal('show');
    }

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('officer_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });
    // JavaScript to Update Date and Time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDateTime').innerText = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
</body>
</html>
