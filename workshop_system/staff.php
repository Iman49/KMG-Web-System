<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Add Staff
if (isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $date_of_hire = $_POST['date_of_hire'];
    $status = $_POST['status'];

    $sql = "INSERT INTO staff (name, position, phone, email, date_of_hire, status) 
            VALUES ('$name', '$position', '$phone', '$email', '$date_of_hire', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "New staff added successfully!";
        header("Location: staff.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding staff: " . $conn->error;
    }
}

// Edit Staff
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM staff WHERE id=$id";
    $result = $conn->query($sql);
    $staff = $result->fetch_assoc();
}

if (isset($_POST['edit_staff'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $date_of_hire = $_POST['date_of_hire'];
    $status = $_POST['status'];

    $sql = "UPDATE staff SET name='$name', position='$position', phone='$phone', email='$email', date_of_hire='$date_of_hire', status='$status' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Staff details updated successfully!";
        header("Location: manage_staff.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating staff: " . $conn->error;
    }
}

// Delete Staff
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM staff WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Staff deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting staff: " . $conn->error;
    }
}

$sql = "SELECT * FROM staff";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff - KMG Motorcycle Garage</title>
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
    <a href="generate_report.php">📊 Generate Report</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div id="main" class="main">
    <h2>Manage Staff Information</h2>
    
    <!-- Displaying messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Add Staff Form -->
    <form action="" method="POST" class="mb-4">
        <h4>Add New Staff</h4>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <input type="text" name="position" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="date_of_hire" class="form-label">Date of Hire</label>
            <input type="date" name="date_of_hire" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="text" name="status" class="form-control" required>
        </div>
        <button type="submit" name="add_staff" class="btn btn-primary">Add Staff</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Date of Hire</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($staff = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $staff['name'] ?></td>
                    <td><?= $staff['position'] ?></td>
                    <td><?= $staff['phone'] ?></td>
                    <td><?= $staff['email'] ?></td>
                    <td><?= $staff['date_of_hire'] ?></td>
                    <td><?= $staff['status'] ?></td>
                    <td>
                        <a href="?edit=<?= $staff['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?= $staff['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit Staff Form -->
    <?php if (isset($staff)): ?>
        <h4>Edit Staff Information</h4>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $staff['id'] ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?= $staff['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" name="position" class="form-control" value="<?= $staff['position'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= $staff['phone'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= $staff['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_of_hire" class="form-label">Date of Hire</label>
                <input type="date" name="date_of_hire" class="form-control" value="<?= $staff['date_of_hire'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" class="form-control" value="<?= $staff['status'] ?>" required>
            </div>
            <button type="submit" name="edit_staff" class="btn btn-primary">Update Staff</button>
        </form>
    <?php endif; ?>
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
