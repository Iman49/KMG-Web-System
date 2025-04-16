<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

$startDate = $_GET['start'] ?? date('Y-m-01');
$endDate = $_GET['end'] ?? date('Y-m-t');

// Fetch Payment Report
$payment_sql = "SELECT YEAR(payment_date) AS year, MONTH(payment_date) AS month, SUM(payment_amount) AS total_sales 
                FROM payments 
                WHERE payment_date BETWEEN '$startDate' AND '$endDate' 
                GROUP BY YEAR(payment_date), MONTH(payment_date)";
$payment_result = $conn->query($payment_sql);
$payment_data = [];
while ($row = $payment_result->fetch_assoc()) {
    $payment_data[] = [
        "month" => "{$row['year']}-" . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
        "total_sales" => $row['total_sales']
    ];
}

// Fetch Inventory Report
$inventory_sql = "SELECT YEAR(date_added) AS year, MONTH(date_added) AS month, SUM(quantity) AS stock_added 
                  FROM inventory 
                  WHERE date_added BETWEEN '$startDate' AND '$endDate' 
                  GROUP BY YEAR(date_added), MONTH(date_added)";
$inventory_result = $conn->query($inventory_sql);
$inventory_data = [];
while ($row = $inventory_result->fetch_assoc()) {
    $inventory_data[] = [
        "month" => "{$row['year']}-" . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
        "stock_added" => $row['stock_added']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report - KMG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .container { margin-top: 100px; }
        h2 { margin-bottom: 30px; }
        .card { border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .chart-container { padding: 20px; }
        .navbar { background-color: #212529; }
        .navbar-brand, .nav-link, .navbar-toggler-icon { color: #fff; }
        .nav-link:hover { color: #ccc; }
    </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand ms-3" href="#">KMG Motorcycle Garage</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon">☰</span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_inventory.php">🧾 Inventory</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_payments.php">💸 Payments</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_attendance.php">👥 Attendance</a></li>
                <li class="nav-item"><a class="nav-link active" href="generate_report.php">📊 Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <h2 class="text-center">📊 Monthly Report</h2>

    <form method="get" class="row mb-4 g-3 justify-content-center">
        <div class="col-md-3">
            <input type="date" name="start" value="<?= $startDate ?>" class="form-control" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="end" value="<?= $endDate ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Generate</button>
        </div>
    </form>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-md-6">
            <div class="card chart-container">
                <h5 class="text-center">💰 Monthly Sales (RM)</h5>
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>

        <!-- Inventory Chart -->
        <div class="col-md-6">
            <div class="card chart-container">
                <h5 class="text-center">📦 Stock Added Monthly</h5>
                <canvas id="stockChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-md-6">
            <h5>💸 Payment Table</h5>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr><th>Month</th><th>Total Sales (RM)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_data as $p): ?>
                        <tr>
                            <td><?= $p['month'] ?></td>
                            <td><?= number_format($p['total_sales'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h5>📦 Inventory Movement</h5>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr><th>Month</th><th>Stock Added</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory_data as $i): ?>
                        <tr>
                            <td><?= $i['month'] ?></td>
                            <td><?= $i['stock_added'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Setup -->
<script>
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const stockCtx = document.getElementById('stockChart').getContext('2d');

    const salesData = {
        labels: <?= json_encode(array_column($payment_data, 'month')) ?>,
        datasets: [{
            label: 'Total Sales (RM)',
            data: <?= json_encode(array_column($payment_data, 'total_sales')) ?>,
            backgroundColor: '#198754',
            borderColor: '#14532d',
            borderWidth: 1
        }]
    };

    const stockData = {
        labels: <?= json_encode(array_column($inventory_data, 'month')) ?>,
        datasets: [{
            label: 'Stock Added',
            data: <?= json_encode(array_column($inventory_data, 'stock_added')) ?>,
            backgroundColor: '#0d6efd',
            borderColor: '#003d99',
            borderWidth: 1
        }]
    };

    new Chart(salesCtx, {
        type: 'bar',
        data: salesData,
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(stockCtx, {
        type: 'line',
        data: stockData,
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
