<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Fetch filter parameters
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the SQL query with filters
$sql = "SELECT * FROM attendance WHERE 1=1";

if ($filter_start_date) {
    $sql .= " AND date >= '$filter_start_date'";
}

if ($filter_end_date) {
    $sql .= " AND date <= '$filter_end_date'";
}

if ($filter_status) {
    $sql .= " AND status = '$filter_status'";
}

$sql .= " ORDER BY date DESC";

// Fetch filtered records
$result = $conn->query($sql);

// Add attendance
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_name = $_POST['staff_name'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO attendance (staff_name, date, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $staff_name, $date, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_attendance.php");
    exit();
}

// Delete attendance
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM attendance WHERE id = $delete_id");
    header("Location: manage_attendance.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff Attendance</title>
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

        .form-control, .form-select {
            margin-bottom: 10px;
        }

        .section-title {
            color: #333;
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
    <a href="generate_report.php">📊 Generate Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div id="main" class="main">
    <h4 class="section-title mb-4">👥 Manage Staff Attendance</h4>

    <!-- Filter Form -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filter_start_date) ?>" placeholder="Start Date">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filter_end_date) ?>" placeholder="End Date">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Select Status</option>
                    <option value="Present" <?= $filter_status == 'Present' ? 'selected' : '' ?>>Present</option>
                    <option value="Absent" <?= $filter_status == 'Absent' ? 'selected' : '' ?>>Absent</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-info w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Add Attendance Form -->
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="staff_name" class="form-control" placeholder="Staff Name" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" required>
                    <option value="">Select Status</option>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    <!-- Attendance Table -->
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['staff_name']) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">Delete</a>
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
