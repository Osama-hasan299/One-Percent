<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {
    $auction_id = $_GET['id'];
    
    // First get the item_id from the auction
    $get_item_sql = "SELECT item_id FROM Auctions WHERE id = ?";
    $get_item_stmt = $conn->prepare($get_item_sql);
    $get_item_stmt->bind_param("i", $auction_id);
    $get_item_stmt->execute();
    $result = $get_item_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $item_id = $row['item_id'];
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Delete the auction first (due to foreign key constraint)
            $delete_auction_sql = "DELETE FROM Auctions WHERE id = ?";
            $delete_auction_stmt = $conn->prepare($delete_auction_sql);
            $delete_auction_stmt->bind_param("i", $auction_id);
            $delete_auction_stmt->execute();
            
            // Then delete the associated item
            $delete_item_sql = "DELETE FROM Items WHERE id = ?";
            $delete_item_stmt = $conn->prepare($delete_item_sql);
            $delete_item_stmt->bind_param("i", $item_id);
            $delete_item_stmt->execute();
            
            // If everything is successful, commit the transaction
            $conn->commit();
            
            // Redirect back with success message
            $_SESSION['message'] = "Auction deleted successfully";
            header("Location: Manage_Auctions.php");
            exit();
            
        } catch (Exception $e) {
            // If there's an error, rollback the changes
            $conn->rollback();
            $_SESSION['error'] = "Error deleting auction: " . $e->getMessage();
            header("Location: Manage_Auctions.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Auction not found";
        header("Location: Manage_Auctions.php");
        exit();
    }
} else {
    $_SESSION['error'] = "No auction ID provided";
    header("Location: Manage_Auctions.php");
    exit();
}
?> 