<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Add Inventory
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $part_name = $_POST['part_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $date_added = $_POST['date_added'];

    $sql = "INSERT INTO inventory (part_name, quantity, unit_price, date_added) 
            VALUES ('$part_name', '$quantity', '$unit_price', '$date_added')";
    $conn->query($sql);
    header("Location: manage_inventory.php");
    exit();
}

// Delete Inventory
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM inventory WHERE id = $id");
    header("Location: manage_inventory.php");
    exit();
}

// Get Inventory List
$result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Inventory</title>
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

        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <a class="navbar-brand ms-5" href="#">KMG Motorcycle Garage</a>
    </div>
</nav>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_inventory.php">🧾 Manage Inventory</a>
    <a href="manage_payments.php">💸 Manage Payments</a>
    <a href="manage_attendance.php">👥 Manage Staff Attendance</a>
    <a href="staff.php">👨‍💼 Manage Staff</a>
    <a href="generate_report.php">📊 Generate Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div id="main" class="main">
    <h3 class="mb-4">🧾 Manage Inventory</h3>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="part_name" class="form-control" placeholder="Part Name" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="Unit Price" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="date_added" class="form-control" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add" class="btn btn-success w-100">Add</button>
        </div>
    </form>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Part Name</th>
                <th>Quantity</th>
                <th>Unit Price (RM)</th>
                <th>Date Added</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['part_name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['unit_price'] ?></td>
                    <td><?= $row['date_added'] ? $row['date_added'] : '-' ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this part?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
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

