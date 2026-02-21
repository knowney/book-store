<?php
include 'config.php';
$sql = "ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'cancel_requested') DEFAULT 'pending'";
if (mysqli_query($conn, $sql)) {
    echo "Success";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>