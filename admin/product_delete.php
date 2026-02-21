<?php
include '../config.php';
include '../check_admin.php';

$id = $_GET['id'] ?? 0;
$query = "DELETE FROM products WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    header("Location: products.php");
} else {
    echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
}
?>
