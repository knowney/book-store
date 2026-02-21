<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_id = $_GET['id'];
$query = "DELETE FROM cart WHERE id='$cart_id'";
mysqli_query($conn, $query);

header("Location: cart.php");
?>
