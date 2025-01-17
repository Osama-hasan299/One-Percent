<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Update the query to include item name
$sql = "SELECT a.id, a.item_id, i.i_name, a.a_status, 
        DATE_FORMAT(a.start_time, '%d %M %Y %h:%i %p') as formatted_start_time,
        DATE_FORMAT(a.end_time, '%d %M %Y %h:%i %p') as formatted_end_time
        FROM Auctions a 
        LEFT JOIN Items i ON a.item_id = i.id 
        ORDER BY a.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Auctions - Admin Panel</title>
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

        .auction-table {
            margin-top: 20px;
        }

        .auction-table th, .auction-table td {
            text-align: center;
            padding: 12px;
        }

        .auction-table th {
            background-color: #343a40;
            color: white;
            font-weight: 500;
        }

        .auction-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .action-buttons button {
            margin: 0 5px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
        }

        .search-bar input {
            width: 250px;
            border-radius: 5px;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 500;
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
            <h1>Manage Auctions</h1>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="d-flex justify-content-between">
            <div class="search-bar">
                <input type="text" class="form-control" id="searchInput" placeholder="Search Auctions" onkeyup="searchAuction()">
            </div>
            <div>
                <a href="add_auction.php" class="btn btn-primary">Add New Auction</a>
            </div>
        </div>

        <!-- Auction Table -->
        <div class="auction-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Status</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="auctionList">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo 'Item #' . $row['item_id']; ?></td>
                                <td><?php echo $row['i_name']; ?></td>
                                <td>
                                    <span class="badge <?php echo $row['a_status'] === 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo ucfirst($row['a_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['formatted_start_time']; ?></td>
                                <td><?php echo $row['formatted_end_time']; ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-warning btn-sm">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteAuction(<?php echo $row['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No auctions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Auction Platform - Admin Panel</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function searchAuction() {
            var input = document.getElementById('searchInput');
            var filter = input.value.toUpperCase();
            var table = document.getElementById('auctionList');
            var tr = table.getElementsByTagName('tr');

            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName('td')[1]; // Search by Item ID
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function deleteAuction(auctionId) {
            if (confirm("Are you sure you want to delete this auction?")) {
                window.location.href = "delete_auction.php?id=" + auctionId;
            }
        }
    </script>
</body>
</html>
