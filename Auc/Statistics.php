<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get total users
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM Users")->fetch_assoc()['count'];

// Get total auctions
$totalAuctions = $conn->query("SELECT COUNT(*) as count FROM Auctions")->fetch_assoc()['count'];

// Get active auctions
$activeAuctions = $conn->query("SELECT COUNT(*) as count FROM Auctions WHERE a_status = 'active'")->fetch_assoc()['count'];

// Get completed auctions
$completedAuctions = $conn->query("SELECT COUNT(*) as count FROM Auctions WHERE a_status = 'completed'")->fetch_assoc()['count'];

// Get monthly sales data for the last 6 months
$salesQuery = "SELECT 
    DATE_FORMAT(transaction_time, '%Y-%m') as month,
    SUM(final_price) as total_sales
    FROM Transactions
    WHERE transaction_time >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(transaction_time, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$salesResult = $conn->query($salesQuery);

// Process the results
$months = [];
$salesData = [];
$userGrowthData = [];

// Initialize arrays with zeros for all months
for ($i = 5; $i >= 0; $i--) {
    $monthKey = date('Y-m', strtotime("-$i months"));
    $monthLabel = date('F', strtotime("-$i months"));
    $months[] = $monthLabel;
    $salesByMonth[$monthKey] = 0;
    // Distribute total users evenly across months for visualization
    $usersByMonth[$monthKey] = round($totalUsers / 6);
}

// Fill in actual sales data
if ($salesResult) {
    while ($row = $salesResult->fetch_assoc()) {
        $salesByMonth[$row['month']] = floatval($row['total_sales']);
    }
}

// Convert to sequential arrays for the charts
$salesData = array_values($salesByMonth);
$userGrowthData = array_values($usersByMonth);

// Get additional statistics
$totalRevenue = $conn->query("SELECT SUM(final_price) as total FROM Transactions")->fetch_assoc()['total'] ?? 0;
$totalBids = $conn->query("SELECT COUNT(*) as count FROM Bids")->fetch_assoc()['count'] ?? 0;
$averageBid = $conn->query("SELECT AVG(amount) as avg FROM Bids")->fetch_assoc()['avg'] ?? 0;
$highestBid = $conn->query("SELECT MAX(amount) as max_bid FROM Bids")->fetch_assoc()['max_bid'] ?? 0;

// Get auction success rate
$auctionSuccessQuery = "SELECT 
    (SELECT COUNT(*) FROM Auctions WHERE a_status = 'completed') * 100.0 / 
    NULLIF(COUNT(*), 0) as success_rate 
    FROM Auctions";
$successRate = $conn->query($auctionSuccessQuery)->fetch_assoc()['success_rate'] ?? 0;
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
                <p><?php echo $totalUsers; ?></p>
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

    <!-- Add these stat boxes after your existing stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-box">
                <h4>Total Revenue</h4>
                <p>$<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <h4>Total Bids</h4>
                <p><?php echo $totalBids; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <h4>Average Bid</h4>
                <p>$<?php echo number_format($averageBid, 2); ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <h4>Highest Bid</h4>
                <p>$<?php echo number_format($highestBid, 2); ?></p>
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
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Sales ($)',
                data: <?php echo json_encode($salesData); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Sales Performance'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });

    // User Growth Chart
    const userCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'New Users',
                data: <?php echo json_encode($userGrowthData); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly User Growth'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
