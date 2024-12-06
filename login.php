<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = $conn->prepare("SELECT * FROM officers WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);

            $_SESSION['officer_id'] = $row['officer_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header('Location: admin/admin_dashboard.php');
            } else {
                header('Location: officer/officer_dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="dist/img/SL.png" type="icon">
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <style>
        body.wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('dist/img/PH2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.8);
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .login-logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .login-logo-container img {
            width: 75px;
            height: auto;
        }

        .login-logo-container h4 {
            margin: 0;
        }

        label {
            color: #333;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body class="wrapper">
    <div class="login-card">
        <div class="login-logo-container">
            <img src="dist/img/PNP.png" alt="PNP Logo" class="login-logo">
            <h4>Login</h4>
            <img src="dist/img/SL.png" alt="Login Logo" class="login-logo">
        </div>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required placeholder="Username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($error)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?php echo addslashes($error); ?>'
                });
            <?php endif; ?>
        });
    </script>

    <script src="dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
