<?php
global $conn;
include 'db_connect.php';

// Fetch total active auctions
$activeAuctionsQuery = "SELECT COUNT(*) AS active_count FROM Auctions WHERE status = 'active'";
$activeAuctionsResult = $conn->query($activeAuctionsQuery);
$activeAuctions = $activeAuctionsResult->fetch_assoc()['active_count'];

// Fetch total registered users
$registeredUsersQuery = "SELECT COUNT(*) AS user_count FROM Users";
$registeredUsersResult = $conn->query($registeredUsersQuery);
$registeredUsers = $registeredUsersResult->fetch_assoc()['user_count'];

// Fetch total completed auctions
$completedAuctionsQuery = "SELECT COUNT(*) AS completed_count FROM Auctions WHERE status = 'completed'";
$completedAuctionsResult = $conn->query($completedAuctionsQuery);
$completedAuctions = $completedAuctionsResult->fetch_assoc()['completed_count'];

// Mock monthly sales data (replace with actual query if needed)
$salesData = [5000, 8000, 6000, 9000, 12000, 15000];

// Mock monthly user growth data (replace with actual query if needed)
$userGrowthData = [50, 80, 70, 90, 110, 130];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            display: block;
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .stat-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .stat-box h4 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-box p {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
        }

        canvas {
            width: 100%;
            height: 300px;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <a href="admin_Dashboard.php">Dashboard</a>
    <a href="Manage_Auctions.php">Manage Auctions</a>
    <a href="Manage_Users.php">Manage Users</a>
    <a href="settings.php">Settings</a>
    <a href="statistics.php">Statistics</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h1>Statistics</h1>
    </div>

    <!-- Stats Overview -->
    <div class="row">
        <!-- Active Auctions -->
        <div class="col-md-4">
            <div class="stat-box">
                <h4>Active Auctions</h4>
                <p><?php echo $activeAuctions; ?></p>
            </div>
        </div>

        <!-- Registered Users -->
        <div class="col-md-4">
            <div class="stat-box">
                <h4>Registered Users</h4>
                <p><?php echo $registeredUsers; ?></p>
            </div>
        </div>

        <!-- Completed Auctions -->
        <div class="col-md-4">
            <div class="stat-box">
                <h4>Completed Auctions</h4>
                <p><?php echo $completedAuctions; ?></p>
            </div>
        </div>
    </div>

    <!-- Sales and Performance Charts -->
    <div class="row">
        <!-- Sales Chart -->
        <div class="col-md-6">
            <div class="stat-box">
                <h4>Sales Over the Last 6 Months</h4>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="col-md-6">
            <div class="stat-box">
                <h4>User Growth Over the Last 6 Months</h4>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Auction Platform - Admin Panel</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js Script -->
<script>
    // Sales Chart Data
    const salesData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Sales ($)',
            data: <?php echo json_encode($salesData); ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
        }]
    };

    const salesConfig = {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // User Growth Chart Data
    const userGrowthData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'New Users',
            data: <?php echo json_encode($userGrowthData); ?>,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 2,
        }]
    };

    const userGrowthConfig = {
        type: 'line',
        data: userGrowthData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Render Charts
    const salesChart = new Chart(document.getElementById('salesChart'), salesConfig);
    const userGrowthChart = new Chart(document.getElementById('userGrowthChart'), userGrowthConfig);
</script>
</body>
</html>
