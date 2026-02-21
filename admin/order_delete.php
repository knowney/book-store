<?php
include '../config.php';
include '../check_admin.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: orders.php");
    exit();
}

// get slip image path to remove file
$res = mysqli_query($conn, "SELECT slip_image FROM orders WHERE id = '$id' LIMIT 1");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $slip = $row['slip_image'];
    if (!empty($slip)) {
        // filesystem path (relative stored as uploads/...)
        $fs = __DIR__ . '/../' . ltrim($slip, '/');
        if (file_exists($fs)) @unlink($fs);
    }
}

// delete order items first
mysqli_query($conn, "DELETE FROM order_items WHERE order_id = '$id'");

// delete order
mysqli_query($conn, "DELETE FROM orders WHERE id = '$id'");

// redirect back to orders list
header("Location: orders.php");
exit();
?>
