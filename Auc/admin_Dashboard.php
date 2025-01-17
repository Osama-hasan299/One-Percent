<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch stats with error checking
$activeAuctions = 0;
$totalUsers = 0;
$pendingAuctions = 0;

// Get active auctions
$activeQuery = $conn->query("SELECT COUNT(*) as count FROM Auctions WHERE a_status='active'");
if ($activeQuery) {
    $activeAuctions = $activeQuery->fetch_assoc()['count'];
}

// Get total users
$usersQuery = $conn->query("SELECT COUNT(*) as count FROM Users");
if ($usersQuery) {
    $totalUsers = $usersQuery->fetch_assoc()['count'];
}

// Get pending auctions
$pendingQuery = $conn->query("SELECT COUNT(*) as count FROM Auctions WHERE a_status='pending'");
if ($pendingQuery) {
    $pendingAuctions = $pendingQuery->fetch_assoc()['count'];
}

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - One Percent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
            font-weight: 400;
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

        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .card {
            width: 30%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .card h3 {
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 1.2rem;
        }

        .card .icon {
            font-size: 3rem;
            color: #007bff;
        }

        .card .count {
            font-size: 2rem;
            font-weight: 600;
        }

        .recent-activities {
            margin-top: 40px;
        }

        .recent-activities table {
            width: 100%;
            margin-top: 20px;
        }

        .recent-activities th, .recent-activities td {
            text-align: left;
            padding: 12px;
        }

        .recent-activities th {
            background-color: #343a40;
            color: white;
        }

        .recent-activities tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
        }

        .count {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="admin_Dashboard.php">Dashboard</a>
        <a href="Manage_Auctions.php">Manage Auctions</a>
        <a href="Manage_Users.php">Manage Users</a>
        <a href="Settings.php">Settings</a>
        <a href="Statistics.php">Statistics</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                
                <h3>Active Auctions</h3>
                <p class="count"><?php echo $activeAuctions; ?></p>
            </div>

            
            <div class="card">
                
                <h3>Total Users</h3>
                <p class="count"><?php echo $totalUsers; ?></p>
            </div>

            <div class="card">
                
                <h3>Pending Auctions</h3>
                <p class="count"><?php echo $pendingAuctions; ?></p>
            </div>
        </div>

        <div class="recent-activities">
            <h2>Recent Activities</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Time</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Auction Created</td>
                        <td>2 hours ago</td>
                        <td>A new auction for a vintage car was created.</td>
                    </tr>
                    <tr>
                        <td>User Registered</td>
                        <td>1 hour ago</td>
                        <td>A new user, John Doe, registered on the platform.</td>
                    </tr>
                    <tr>
                        <td>Auction Ended</td>
                        <td>30 minutes ago</td>
                        <td>An auction for a painting ended with a winning bid.</td>
                    </tr>
                    <tr>
                        <td>Auction Created</td>
                        <td>15 minutes ago</td>
                        <td>A new auction for a classic watch was created.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Auction Platform - Admin Panel</p>
    </div>

    <!-- Bootstrap JS and Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
