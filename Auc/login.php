<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password_hash']) {
            $_SESSION['user_id'] = $user['id'];
            
            // Check if this email belongs to ID 11111
            $admin_sql = "SELECT email FROM Users WHERE id = 11111";
            $admin_result = $conn->query($admin_sql);
            $admin_email = $admin_result->fetch_assoc()['email'];
            
            if ($email === $admin_email) {
                header("Location: admin_Dashboard.php");
            } else {
                header("Location: home.html");
            }
            exit();
        } else {
            echo "Invalid password.";
            header("Refresh: 2; URL=index.php");
        }
    } else {
        echo "Invalid email.";
        header("Refresh: 2; URL=index.php");
    }
}
?>
