    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <div class="sidebar">
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
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home nav-icon"></i><p>Dashboard</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="add_record.php" class="nav-link"><i class="fas fa-plus nav-icon"></i><p>Add Record</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="add_officer.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>Add Officer</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="officer_profile.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>View Officer</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="view_list.php" class="nav-link"><i class="fas fa-list nav-icon"></i><p>View List</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="impounded_vehicles.php" class="nav-link"><i class="fas fa-car nav-icon"></i><p>Impounded Vehicle</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="released_vehicles.php" class="nav-link"><i class="fas fa-check nav-icon"></i><p>Release Vehicle</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt nav-icon"></i><p>Logout</p></a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>