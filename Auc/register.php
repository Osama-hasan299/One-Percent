<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if username or email already exists
    $check_sql = "SELECT * FROM Users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('Username or email already exists.');
            window.location.href = 'register.html';
        </script>";
    } else {
        // Insert new user
        $sql = "INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            // Start session and set user_id
            session_start();
            $_SESSION['user_id'] = $conn->insert_id;
            
            // Redirect to home page
            echo "<script>
                window.location.href = 'home.html';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Registration failed: " . $stmt->error . "');
                window.location.href = 'register.html';
            </script>";
        }
    }
}
?>
