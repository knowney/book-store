<?php
include '../config.php';
include '../check_admin.php';

$id = $_GET['id'] ?? 0;
$query = "DELETE FROM users WHERE id='$id' AND role='user'";

if (mysqli_query($conn, $query)) {
    header("Location: users.php");
} else {
    echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
}
?>
