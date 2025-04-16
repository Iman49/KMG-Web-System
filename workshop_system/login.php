<?php
session_start();
$conn = new mysqli("localhost", "root", "", "workshop_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the admin table is empty
$check = $conn->query("SELECT COUNT(*) AS total FROM admin");
$data = $check->fetch_assoc();
$no_admin = $data['total'] == 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Workshop System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
        }
        .box {
            margin-top: 100px;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 box">
            <h3 class="text-center mb-4">Admin Login</h3>

            <?php
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger">'.htmlspecialchars($_GET['error']).'</div>';
            }
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success">'.htmlspecialchars($_GET['success']).'</div>';
            }
            ?>

            <!-- Login Form -->
            <form method="POST" action="login_process.php">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <?php if ($no_admin): ?>
            <div class="mt-3 text-center">
                <a href="create_admin.php">Create Admin User</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

