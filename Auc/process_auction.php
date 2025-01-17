<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "";
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $starting_price = $_POST['starting_price'];
    $end_time = $_POST['end_time'];
    $user_id = $_SESSION['user_id'];
    
    // Handle image upload just for display
    if (isset($_FILES["item_image"]) && $_FILES["item_image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        
        $file_type = $_FILES["item_image"]["type"];
        $valid_mime_types = array("image/jpeg", "image/png", "image/gif");
        
        if (!in_array($file_extension, $allowed_types) || !in_array($file_type, $valid_mime_types)) {
            $error = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $error = "Please select a file to upload.";
    }

    if (empty($error)) {
        // Insert the item without image path
        $item_sql = "INSERT INTO Items (i_name, i_description, starting_price, user_id) VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);
        $item_stmt->bind_param("ssdi", $item_name, $description, $starting_price, $user_id);
        
        if ($item_stmt->execute()) {
            $item_id = $conn->insert_id;
            
            $auction_sql = "INSERT INTO Auctions (item_id, a_status, end_time) VALUES (?, 'active', ?)";
            $auction_stmt = $conn->prepare($auction_sql);
            $auction_stmt->bind_param("is", $item_id, $end_time);
            
            if ($auction_stmt->execute()) {
                header("Location: Manage_Auctions.php");
                exit();
            } else {
                $error = "Error creating auction: " . $conn->error;
            }
        } else {
            $error = "Error creating item: " . $conn->error;
        }
    }

    // If there was an error, redirect back with error message
    if (!empty($error)) {
        $_SESSION['auction_error'] = $error;
        header("Location: add_auction.php");
        exit();
    }
}
?> 