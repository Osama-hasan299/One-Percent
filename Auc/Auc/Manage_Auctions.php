<?php
global $conn;
include 'db_connect.php';

$auctions = $conn->query("SELECT * FROM Auctions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Auctions</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Item ID</th>
        <th>Status</th>
        <th>Start Time</th>
        <th>End Time</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $auctions->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['item_id']; ?></td>
            <td><?php echo $row['a_status']; ?></td>
            <td><?php echo $row['start_time']; ?></td>
            <td><?php echo $row['end_time']; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
