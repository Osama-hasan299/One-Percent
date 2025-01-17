<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $balance = $_POST['balance'] ?? 0;

    // Check if username or email already exists
    $check_sql = "SELECT * FROM Users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user_error'] = "Username or email already exists.";
        header("Location: add_user.php");
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO Users (username, email, password_hash, balance) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssd", $username, $email, $password, $balance);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User created successfully";
        header("Location: Manage_Users.php");
        exit();
    } else {
        $_SESSION['user_error'] = "Error creating user: " . $stmt->error;
        header("Location: add_user.php");
        exit();
    }
}
?> 