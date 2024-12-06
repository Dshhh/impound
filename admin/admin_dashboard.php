<?php
session_start();

// Ensure user is logged in and is an admin
if (!isset($_SESSION['officer_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../database.php';

// Initialize monthly counts for the current year
$impounded_count_per_month = array_fill(1, 12, 0);
$released_count_per_month = array_fill(1, 12, 0);

// Query to get the monthly counts for the current year
$impounded_query = "SELECT MONTH(impound_date) AS month, COUNT(*) AS total_impounded 
                    FROM impound_records 
                    WHERE YEAR(impound_date) = YEAR(CURRENT_DATE) 
                    GROUP BY month";
$impounded_result = mysqli_query($conn, $impounded_query);
while ($row = mysqli_fetch_assoc($impounded_result)) {
    $impounded_count_per_month[$row['month']] = (int)$row['total_impounded'];
}

// Repeat for released vehicles
$released_query = "SELECT MONTH(release_date) AS month, COUNT(*) AS total_released 
                   FROM impound_records 
                   WHERE YEAR(release_date) = YEAR(CURRENT_DATE) 
                   GROUP BY month";
$released_result = mysqli_query($conn, $released_query);
while ($row = mysqli_fetch_assoc($released_result)) {
    $released_count_per_month[$row['month']] = (int)$row['total_released'];
}

// Calculate total impounded and released vehicles for the current year
$total_impounded = array_sum($impounded_count_per_month);
$total_released = array_sum($released_count_per_month);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Impoundment Record Tracking System</title>
    <link rel="icon" href="../dist/img/SL.png" type="icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- ChartJS -->
    <script src="../plugins/chart.js/Chart.js"></script>
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
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3>Overview</h3>
                        </div>
                        <div class="card-body">
                            <!-- Use Bootstrap grid for side-by-side cards -->
                            <div class="row">
                                <!-- Total Record Card -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-green">
                                            <h5>Total Record</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="statusChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Yearly Report Card -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-green">
                                            <h5>Yearly Report</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="vehiclesYearlyChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

<script>
    // Donut Chart for Total Impounded vs. Released Vehicles
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const totalImpounded = <?php echo $total_impounded; ?>;
    const totalReleased = <?php echo $total_released; ?>;

    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Impounded Vehicles <?php echo $total_impounded; ?>', 'Released Vehicles <?php echo $total_released; ?>'],
            datasets: [{
                data: [totalImpounded, totalReleased],
                backgroundColor: ['#f56954', '#00a65a']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Bar Chart for Monthly Impounded and Released Vehicles for the Year
    const yearlyCtx = document.getElementById('vehiclesYearlyChart').getContext('2d');
    const impoundedMonthlyData = <?php echo json_encode(array_values($impounded_count_per_month)); ?>;
    const releasedMonthlyData = <?php echo json_encode(array_values($released_count_per_month)); ?>;

    const vehiclesYearlyChart = new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [
                {
                    label: 'Impounded Vehicles <?php echo $total_impounded; ?>',
                    data: impoundedMonthlyData,
                    backgroundColor: 'rgba(245, 105, 84, 1)',
                },
                {
                    label: 'Released Vehicles <?php echo $total_released; ?>',
                    data: releasedMonthlyData,
                    backgroundColor: 'rgba(0, 166, 90, 1)',
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                },
            },
        }
    });

    // JavaScript to Update Date and Time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDateTime').innerText = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>
