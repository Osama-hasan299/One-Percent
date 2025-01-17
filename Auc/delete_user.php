<?php
global $conn;
include 'db_connect.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $sql = "DELETE FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header("Location: Manage_Users.php");
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
