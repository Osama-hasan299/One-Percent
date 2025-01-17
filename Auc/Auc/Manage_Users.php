<?php
global $conn;
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch users
$sql = "SELECT id, username, email, role, status FROM Users"; // Adjust columns as per your database
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .user-table {
            margin-top: 20px;
        }

        .user-table th, .user-table td {
            text-align: center;
            padding: 12px;
        }

        .user-table th {
            background-color: #343a40;
            color: white;
        }

        .user-table tr:nth-child(even) {
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
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <a href="admin_Dashboard.php">Dashboard</a>
    <a href="Manage_Auctions.php">Manage Auctions</a>
    <a href="Manage_Users.php">Manage Users</a>
    <a href="Settings.php">Settings</a>
    <a href="Statistics.php">Statistics</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h1>Manage Users</h1>
    </div>

    <!-- Search and Filter -->
    <div class="d-flex justify-content-between">
        <div class="search-bar">
            <input type="text" class="form-control" id="searchInput" placeholder="Search Users" onkeyup="searchUser()">
        </div>
        <div>
            <button class="btn btn-primary">Add New User</button>
        </div>
    </div>

    <!-- User Table -->
    <div class="user-table">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="userList">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role'] ?? 'User'; ?></td>
                        <td><?php echo $row['status'] ?? 'Active'; ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Auction Platform - Admin Panel</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Search function for Users
    function searchUser() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        table = document.getElementById('userList');
        tr = table.getElementsByTagName('tr');

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName('td')[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    // Function to delete user
    function deleteUser(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = "delete_user.php?id=" + userId;
        }
    }
</script>
</body>
</html>
