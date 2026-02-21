<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    // For AJAX, return JSON error; otherwise redirect to login
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ต้องเข้าสู่ระบบก่อน']);
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
if ($quantity < 1) $quantity = 1;

// Check if product exists and has stock
$product_check = mysqli_query($conn, "SELECT stock, title FROM products WHERE id='$product_id'");
if (!$product_check || mysqli_num_rows($product_check) == 0) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'สินค้าไม่พบ']);
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}
$product = mysqli_fetch_assoc($product_check);
if ($quantity > $product['stock']) $quantity = $product['stock'];

// Check if product already in cart
$check_query = "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Update quantity
    $update_query = "UPDATE cart SET quantity = quantity + '$quantity' WHERE user_id='$user_id' AND product_id='$product_id'";
    mysqli_query($conn, $update_query);
} else {
    // Insert new cart item
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
    mysqli_query($conn, $insert_query);
}

// Decide response type
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
    // Return updated cart summary (count)
    $count_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='$user_id'");
    $cnt = ($count_res && $row = mysqli_fetch_assoc($count_res)) ? intval($row['cnt']) : 0;
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'เพิ่มสินค้าเข้าตะกร้าแล้ว', 'cart_count' => $cnt, 'product_title' => $product['title']]);
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>
