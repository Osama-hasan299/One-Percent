<?php
$servername = "localhost";
$username = "root";
$password = "Root1root"; // Replace with your MySQL password
$dbname = "Auction"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
