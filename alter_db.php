<?php
include 'config.php';
$sql = "ALTER TABLE orders ADD COLUMN cancel_reason TEXT DEFAULT NULL";
if (mysqli_query($conn, $sql)) {
    echo "Success";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>