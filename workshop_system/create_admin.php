<?php
session_start();
$conn = new mysqli("localhost", "root", "", "workshop_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert the first admin user into the database
    $query = "INSERT INTO admin (username, password) VALUES ('$username', '$password')";
    if ($conn->query($query)) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");  // Redirect to dashboard after successful creation
        exit();
    } else {
        $error = "Failed to create admin user. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin User - Workshop System</title>
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
            <h3 class="text-center mb-4">Create Admin User</h3>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Create Admin</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
