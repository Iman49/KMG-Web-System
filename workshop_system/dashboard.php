<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Fetch Inventory
$inventory_sql = "SELECT * FROM inventory ORDER BY id DESC LIMIT 5";
$inventory_result = $conn->query($inventory_sql);

// Fetch Payments
$payment_sql = "SELECT * FROM payments ORDER BY payment_id DESC LIMIT 5";
$payment_result = $conn->query($payment_sql);

// Attendance Rate
$attendance_sql = "SELECT COUNT(*) AS total, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present FROM attendance";
$attendance_result = $conn->query($attendance_sql);
$attendance_data = $attendance_result->fetch_assoc();
$attendance_rate = ($attendance_data['total'] > 0) ? round(($attendance_data['present'] / $attendance_data['total']) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMG Motorcycle Garage Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background-color: #212529;
        }

        .navbar-brand {
            color: #fff;
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
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
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
            transition: margin-left 0.3s;
        }

        .main.collapsed {
            margin-left: 0;
        }

        .carousel-inner {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .carousel-item {
            padding: 20px;
            background: white;
        }

        .toggle-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: #343a40;
            color: white;
            border: none;
            padding: 5px 10px;
        }

        h5.section-title {
            margin-bottom: 20px;
            color: #333;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .goto-btn {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <a class="navbar-brand ms-5" href="#">KMG Motorcycle Garage Management</a>
    </div>
</nav>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_inventory.php">🧾 Manage Inventory</a>
    <a href="manage_payments.php">💸 Manage Payments</a>
    <a href="manage_attendance.php">👥 Manage Staff Attendance</a>
    <a href="staff.php">👨‍💼 Manage Staff</a>
    <a href="generate_report.php">📊 Generate Report</a>  <!-- New Report Link -->
    <a href="logout.php">🚪 Logout</a>
</div>


<!-- Main -->
<div id="main" class="main">
    <div id="previewCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-inner">

            <!-- Inventory Preview -->
            <div class="carousel-item active">
                <h5 class="section-title">🧾 Latest Inventory</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Part Name</th>
                            <th>Quantity</th>
                            <th>Price (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($inv = $inventory_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $inv['part_name'] ?></td>
                                <td><?= $inv['quantity'] ?></td>
                                <td><?= $inv['unit_price'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="manage_inventory.php" class="btn btn-success goto-btn">Manage Inventory</a>
            </div>

            <!-- Payment Preview -->
            <div class="carousel-item">
                <h5 class="section-title">💸 Latest Payments</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($pay = $payment_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $pay['customer_name'] ?></td>
                                <td>RM <?= $pay['payment_amount'] ?></td>
                                <td><?= $pay['payment_date'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="manage_payments.php" class="btn btn-primary goto-btn">Manage Payments</a>
            </div>

            <!-- Attendance Preview -->
            <div class="carousel-item">
                <h5 class="section-title">👥 Attendance Rate</h5>
                <div class="text-center">
                    <h1><?= $attendance_rate ?>%</h1>
                    <p class="text-muted">of staff marked as Present</p>
                    <a href="manage_attendance.php" class="btn btn-info goto-btn">Manage Attendance</a>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle"></span>
        </button>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("collapsed");
        document.getElementById("main").classList.toggle("collapsed");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
