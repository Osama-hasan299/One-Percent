<?php
global $conn;
include 'db_connect.php';

// Fetch current settings
$sql = "SELECT * FROM Settings ORDER BY updated_at DESC LIMIT 1";
$result = $conn->query($sql);
$currentSettings = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $platformName = $_POST['platformName'];
    $currency = $_POST['currency'];
    $auctionDuration = $_POST['auctionDuration'];
    $adminEmail = $_POST['adminEmail'];

    // Handle logo upload
    $logoPath = $currentSettings['logo_path'] ?? ''; // Default to current logo if no new upload
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "uploads/";
        $logoPath = $targetDir . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);
    }

    // Update settings
    $stmt = $conn->prepare("INSERT INTO Settings (platform_name, logo_path, currency, auction_duration, admin_email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $platformName, $logoPath, $currency, $auctionDuration, $adminEmail);

    if ($stmt->execute()) {
        header("Location: settings.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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

        .settings-form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .settings-form .form-group {
            margin-bottom: 15px;
        }

        .settings-form input, .settings-form select {
            width: 100%;
            border-radius: 5px;
            padding: 10px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
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
    <a href="Statistics.php">Statistics</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h1>Settings</h1>
    </div>

    <!-- Settings Form -->
    <div class="settings-form">
        <h3>Platform Configuration</h3>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Settings saved successfully!</div>
        <?php endif; ?>
        <form action="settings.php" method="POST" enctype="multipart/form-data">
            <!-- Platform Name -->
            <div class="form-group">
                <label for="platformName">Platform Name</label>
                <input type="text" id="platformName" name="platformName" class="form-control" value="<?php echo htmlspecialchars($currentSettings['platform_name'] ?? ''); ?>" required>
            </div>

            <!-- Platform Logo -->
            <div class="form-group">
                <label for="logo">Upload Logo</label>
                <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                <?php if (!empty($currentSettings['logo_path'])): ?>
                    <img src="<?php echo $currentSettings['logo_path']; ?>" alt="Logo" style="max-height: 50px; margin-top: 10px;">
                <?php endif; ?>
            </div>

            <!-- Currency Settings -->
            <div class="form-group">
                <label for="currency">Currency</label>
                <select id="currency" name="currency" class="form-control">
                    <option value="usd" <?php echo ($currentSettings['currency'] ?? '') === 'usd' ? 'selected' : ''; ?>>USD</option>
                    <option value="eur" <?php echo ($currentSettings['currency'] ?? '') === 'eur' ? 'selected' : ''; ?>>EUR</option>
                    <option value="gbp" <?php echo ($currentSettings['currency'] ?? '') === 'gbp' ? 'selected' : ''; ?>>GBP</option>
                </select>
            </div>

            <!-- Auction Duration -->
            <div class="form-group">
                <label for="auctionDuration">Default Auction Duration (in hours)</label>
                <input type="number" id="auctionDuration" name="auctionDuration" class="form-control" value="<?php echo htmlspecialchars($currentSettings['auction_duration'] ?? 24); ?>" required>
            </div>

            <!-- Admin Email -->
            <div class="form-group">
                <label for="adminEmail">Admin Email</label>
                <input type="email" id="adminEmail" name="adminEmail" class="form-control" value="<?php echo htmlspecialchars($currentSettings['admin_email'] ?? ''); ?>" required>
            </div>

            <!-- Save Button -->
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Auction Platform - Admin Panel</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
