<?php
session_start();

// Get the admin username from the session
$admin_username = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Guest';

// Destroy the session
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout - Workshop System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .logout-box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .btn-back {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 30px;
            font-size: 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="logout-container">
    <div class="logout-box">
        <h2>Thank You, <?= htmlspecialchars($admin_username) ?>!</h2>
        <p>You have successfully logged out.</p>
        <a href="login.php" class="btn btn-back">Back to Login</a>
    </div>
</div>

</body>
</html>
