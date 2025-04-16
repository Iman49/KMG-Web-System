<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Fetch all payment records from the database
$sql = "SELECT * FROM payments";
$result = $conn->query($sql);

// Add new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_payment'])) {
        $customer_name = $_POST['customer_name'];
        $payment_amount = $_POST['payment_amount'];
        $payment_date = $_POST['payment_date'];

        $insert_sql = "INSERT INTO payments (customer_name, payment_amount, payment_date) 
                       VALUES ('$customer_name', '$payment_amount', '$payment_date')";
        if ($conn->query($insert_sql) === TRUE) {
            $success_message = "Payment added successfully!";
        } else {
            $error_message = "Error adding payment: " . $conn->error;
        }
    }

    // Delete payment
    if (isset($_POST['delete_payment'])) {
        $payment_id = $_POST['payment_id'];
        $delete_sql = "DELETE FROM payments WHERE id = '$payment_id'";
        if ($conn->query($delete_sql) === TRUE) {
            $success_message = "Payment deleted successfully!";
        } else {
            $error_message = "Error deleting payment: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments | KMG Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2d0e6cfd6.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #212529;
            padding-top: 60px;
            transition: 0.3s;
        }

        .sidebar a {
            color: #ffffff;
            padding: 15px;
            display: block;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background-color: #343a40;
        }

        .topbar {
            position: fixed;
            width: 100%;
            height: 60px;
            background-color: #343a40;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }

        .content {
            margin-left: 250px;
            padding: 80px 30px;
        }

        .btn-add {
            background-color: #198754;
            color: white;
        }

        .btn-add:hover {
            background-color: #157347;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .btn-danger {
            font-size: 0.85rem;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<div class="topbar">
    <div><i class="fas fa-money-check-alt me-2"></i>Manage Payments</div>
    <div>
        <a href="dashboard.php" class="text-white me-3">Dashboard</a>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <a href="manage_inventory.php"><i class="fas fa-boxes me-2"></i>Manage Inventory</a>
    <a href="manage_payments.php"><i class="fas fa-money-check-alt me-2"></i>Manage Payments</a>
    <a href="manage_staff.php"><i class="fas fa-users me-2"></i>Manage Staff Attendance</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Add Payment Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Add New Payment</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="payment_amount" class="form-label">Payment Amount (RM)</label>
                            <input type="number" name="payment_amount" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add_payment" class="btn btn-add w-100">
                                <i class="fas fa-plus-circle me-1"></i>Add Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment List Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Payment Records</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Payment Amount (RM)</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['customer_name'] ?></td>
                                        <td><?= $row['payment_amount'] ?></td>
                                        <td><?= $row['payment_date'] ?></td>
                                        <td>
                                            <form method="POST" onsubmit="return confirm('Delete this payment?');">
                                                <input type="hidden" name="payment_id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="delete_payment" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No payment records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
