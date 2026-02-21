<?php
include 'config.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// cart count for navbar badge
$cart_count = 0;
$cnt_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='".intval($user_id)."'");
if ($cnt_res && $cnt_row = mysqli_fetch_assoc($cnt_res)) {
    $cart_count = intval($cnt_row['cnt']);
}

// Get cart items
$query = "SELECT c.*, p.title, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id='$user_id'";
$result = mysqli_query($conn, $query);

$total = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $total += $row['line_total'];
    $cart_items[] = $row;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create order
    $insert_order = "INSERT INTO orders (user_id, total_price, status) VALUES ('$user_id', '$total', 'pending')";
    
    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($cart_items as $item) {
            $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '{$item['product_id']}', '{$item['quantity']}', '{$item['price']}')";
            mysqli_query($conn, $insert_item);
        }
        
        // Handle slip upload
        if (isset($_FILES['slip_image']) && $_FILES['slip_image']['size'] > 0) {
            $file = $_FILES['slip_image'];
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/','_',basename($file['name']));
            $upload_dir = 'uploads/slips/';
            $file_path = $upload_dir . $file_name;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $update_slip = "UPDATE orders SET slip_image='$file_path' WHERE id='$order_id'";
                mysqli_query($conn, $update_slip);
            }
        }
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE user_id='$user_id'";
        mysqli_query($conn, $clear_cart);
        
        $success = "คำสั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ: #$order_id";
        // refresh local data
        $cart_items = [];
        $total = 0;
        $cart_count = 0;
        header("Refresh: 3; url=index.php");
    } else {
        $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ชำระเงิน - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    :root{
        --primary:#0f172a;
        --primary-700:#0b1220;
        --accent:#f59e0b;
        --danger:#ef4444;
        --muted:#6b7280;
    }
    .bg-blue-700 { background-color: var(--primary) !important; }
    .bg-blue-800 { background-color: var(--primary-700) !important; }
    .bg-yellow-400, .bg-green-600 { background-color: var(--accent) !important; color:#000 !important; }
    .bg-red-600 { background-color: var(--danger) !important; }
    .text-gray-600 { color: var(--muted) !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navbar (use same markup as index.php) -->
    <header class="bg-blue-700 text-white p-5 flex justify-between items-center">
        <h1 class="text-xl font-semibold flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 20l9-5-9-5-9 5 9 5z" />
            </svg>
            <span>Book Store</span>
        </h1>

        <div class="flex items-center space-x-3">
            <a id="cart-link" href="cart.php" class="relative flex items-center space-x-2 bg-blue-800 px-3 py-1 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5" />
                </svg>
                <span>ตะกร้า</span>
                <?php if ($cart_count > 0): ?>
                    <span id="cart-count" class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="orders_history.php" class="flex items-center space-x-2 bg-blue-800 px-3 py-1 rounded">รายการสั่งซื้อของฉัน</a>
                <a href="profile.php" class="flex items-center space-x-2 bg-blue-800 px-3 py-1 rounded">โปรไฟล์</a>
                <a href="logout.php" class="flex items-center space-x-2 bg-blue-800 px-3 py-1 rounded">ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php" class="flex items-center space-x-2 bg-blue-800 px-3 py-1 rounded">เข้าสู่ระบบ / สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">ยืนยันการชำระเงิน</h2>

        <?php if ($error): ?><div class="mb-4 p-3 bg-red-50 text-red-700 rounded"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="mb-4 p-3 bg-green-50 text-green-700 rounded"><?php echo $success; ?></div><?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Items list -->
            <section class="lg:col-span-2 space-y-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="font-semibold mb-4">สินค้าที่สั่ง</h3>
                    <?php if (count($cart_items) == 0): ?>
                        <p class="text-gray-600">ไม่มีสินค้าในตะกร้า</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="flex items-center space-x-4">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-24 h-28 object-cover rounded" alt="">
                                    <?php else: ?>
                                        <div class="w-24 h-28 bg-gray-100 rounded flex items-center justify-center text-sm text-gray-500">No Image</div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-medium"><?php echo htmlspecialchars($item['title']); ?></div>
                                                <div class="text-sm text-gray-600">฿<?php echo number_format($item['price'],2); ?> × <?php echo intval($item['quantity']); ?></div>
                                            </div>
                                            <div class="font-semibold">฿<?php echo number_format($item['line_total'],2); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Summary & slip upload -->
            <aside class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">สรุปคำสั่งซื้อ</h3>
                <div class="mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>ยอดรวม</span>
                        <span class="font-semibold">฿<?php echo number_format($total,2); ?></span>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">อัปโหลดสลิปการโอนเงิน</label>
                        <input type="file" name="slip_image" accept="image/*" required class="block w-full text-sm text-gray-700">
                    </div>

                    <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-semibold px-4 py-2 rounded">ยืนยันการสั่งซื้อ</button>
                    <a href="cart.php" class="block mt-3 text-center text-sm text-gray-600">กลับไปตะกร้า</a>
                </form>
            </aside>
        </div>
    </main>
</body>
</html>
