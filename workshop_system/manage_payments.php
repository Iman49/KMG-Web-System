<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Add payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $customer_name = $_POST['customer_name'];
    $payment_amount = $_POST['payment_amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $description = $_POST['description'];

    $conn->query("INSERT INTO payments (customer_name, payment_amount, payment_date, payment_method, description) 
                  VALUES ('$customer_name', '$payment_amount', '$payment_date', '$payment_method', '$description')");
    header("Location: manage_payments.php");
    exit();
}

// Delete payment
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM payments WHERE payment_id = $id");
    header("Location: manage_payments.php");
    exit();
}

$payments = $conn->query("SELECT * FROM payments ORDER BY payment_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #212529;
        }
        .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .sidebar {
            width: 200px;
            background-color: #343a40;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 56px;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: #ccc;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }
        .main {
            margin-left: 200px;
            padding: 30px;
            padding-top: 80px;
        }
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body>

<!-- Top Nav -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand ms-3" href="#">KMG Motorcycle Garage Management</a>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_inventory.php">🧾 Manage Inventory</a>
    <a href="manage_payments.php">💸 Manage Payments</a>
    <a href="manage_attendance.php">👥 Manage Staff Attendance</a>
     <a href="staff.php">👨‍💼 Manage Staff</a>
    <a href="generate_report.php">📊 Generate Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main -->
<div class="main">
    <div class="form-section">
        <h2>💸 Manage Payments</h2>

        <!-- Add Payment Form -->
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Amount (RM)</label>
                <input type="number" step="0.01" name="payment_amount" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="payment_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Method</label>
                <select name="payment_method" class="form-select" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Online Transfer">Online Transfer</option>
                </select>
            </div>
            <div class="col-md-10">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control">
            </div>
            <div class="col-md-2 d-grid">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" name="add" class="btn btn-success">Add Payment</button>
            </div>
        </form>

        <!-- Payments Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Customer</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['customer_name'] ?></td>
                        <td><?= number_format($row['payment_amount'], 2) ?></td>
                        <td><?= $row['payment_date'] ?></td>
                        <td><?= $row['payment_method'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><a href="?delete=<?= $row['payment_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
