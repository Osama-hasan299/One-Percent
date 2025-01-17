<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Auction - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Copy the same styles from Manage_Auctions.php */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .main-content {
            
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1>Add New Auction</h1>
        </div>
        
        <?php if (isset($_SESSION['auction_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['auction_error'];
                    unset($_SESSION['auction_error']); // Clear the error after displaying
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="process_auction.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="item_name" class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="item_image" class="form-label">Item Image</label>
                    <input type="file" class="form-control" id="item_image" name="item_image" accept="image/*" required>
                    <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 5MB</div>
                </div>
                
                <div class="mb-3">
                    <label for="starting_price" class="form-label">Starting Price ($)</label>
                    <input type="number" class="form-control" id="starting_price" name="starting_price" min="0" step="0.01" required>
                </div>
                
                <div class="mb-3">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                </div>
                
                <div class="mb-3">
                    <img id="imagePreview" src="#" alt="Image preview" style="max-width: 200px; display: none;">
                </div>
                
                <button type="submit" class="btn btn-primary">Create Auction</button>
                <a href="Manage_Auctions.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('item_image').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('imagePreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
    </script>
</body>
</html> 