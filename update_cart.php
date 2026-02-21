<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_id = $_POST['cart_id'];
$quantity = $_POST['quantity'];

if ($quantity > 0) {
    $query = "UPDATE cart SET quantity='$quantity' WHERE id='$cart_id'";
    mysqli_query($conn, $query);
}

header("Location: cart.php");
?>
