<?php
session_start();
$conn = new mysqli("localhost", "root", "", "workshop_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

$query = "SELECT * FROM admin WHERE username='$username'";
$result = $conn->query($query);

if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $row['username'];
        header("Location: dashboard.php");  // Redirect to the dashboard if successful
        exit();
    } else {
        header("Location: login.php?error=Invalid password");
        exit();
    }
} else {
    header("Location: login.php?error=User not found");
    exit();
}
?>
