<?php
include '../config.php';
include '../check_admin.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: orders.php");
    exit();
}

$order_q = mysqli_query($conn, "SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = '$id'");
$order = mysqli_fetch_assoc($order_q);
if (!$order) {
    header("Location: orders.php");
    exit();
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = mysqli_real_escape_string($conn, $_POST['status'] ?? $order['status']);
    $update = mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = '$id'");
    if ($update) {
        $success = "อัปเดตสถานะเป็น '$new_status' สำเร็จ";
        // refresh order data
        $order_q = mysqli_query($conn, "SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = '$id'");
        $order = mysqli_fetch_assoc($order_q);
    } else {
        $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}

$items_q = mysqli_query($conn, "SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = '$id'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>คำสั่งซื้อ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --accent:#7c3aed; --danger:#ef4444; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-green-500 { background-color: #10b981 !important; }
        .bg-yellow-400 { background-color: #f59e0b !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <aside class="w-64 h-screen bg-blue-700 text-white p-6 fixed">
            <h2 class="text-xl font-semibold mb-6">Admin Panel</h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-3 py-2 rounded hover:bg-blue-800">Dashboard</a>
                <a href="products.php" class="block px-3 py-2 rounded hover:bg-blue-800">จัดการหนังสือ</a>
                <a href="users.php" class="block px-3 py-2 rounded hover:bg-blue-800">จัดการผู้ใช้</a>
                <a href="orders.php" class="block px-3 py-2 rounded bg-blue-800">จัดการคำสั่งซื้อ</a>
                <a href="../logout.php" class="inline-block mt-6 bg-red-600 px-3 py-2 rounded">ออกจากระบบ</a>
            </nav>
        </aside>

        <main class="ml-64 w-full p-8">
            <header class="mb-6">
                <h1 class="text-2xl font-semibold">คำสั่งซื้อ #<?php echo $order['id']; ?></h1>
            </header>

            <?php if ($success): ?><div class="mb-4 text-green-600"><?php echo $success; ?></div><?php endif; ?>
            <?php if ($error): ?><div class="mb-4 text-red-600"><?php echo $error; ?></div><?php endif; ?>

            <div class="bg-white p-6 rounded shadow mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>ผู้สั่ง:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <p><strong>ยอดรวม:</strong> ฿<?php echo number_format($order['total_price'], 2); ?></p>
                        <p class="mb-2"><strong>สถานะปัจจุบัน:</strong>
                            <?php
                                $status_class = '';
                                $status_text = '';
                                switch($order['status']) {
                                    case 'pending':
                                        $status_class = 'bg-yellow-400 text-white';
                                        $status_text = 'รอดำเนินการ';
                                        break;
                                    case 'confirmed':
                                        $status_class = 'bg-blue-500 text-white';
                                        $status_text = 'ชำระเงินแล้ว';
                                        break;
                                    case 'shipped':
                                        $status_class = 'bg-indigo-500 text-white';
                                        $status_text = 'จัดส่งแล้ว';
                                        break;
                                    case 'delivered':
                                        $status_class = 'bg-green-500 text-white';
                                        $status_text = 'ส่งมอบแล้ว';
                                        break;
                                    case 'cancelled':
                                        $status_class = 'bg-gray-500 text-white';
                                        $status_text = 'ยกเลิกแล้ว';
                                        break;
                                    case 'cancel_requested':
                                        $status_class = 'bg-red-500 text-white';
                                        $status_text = 'รออนุมัติยกเลิก';
                                        break;
                                    default:
                                        $status_class = 'bg-gray-200 text-gray-800';
                                        $status_text = $order['status'];
                                }
                            ?>
                            <span class="px-2 py-1 rounded text-sm <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </p>
                        <p><strong>วันที่:</strong> <?php echo $order['created_at']; ?></p>
                        
                        <?php if (!empty($order['cancel_reason'])): ?>
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm">
                            <strong class="block mb-1">เหตุผลที่ขอยกเลิก:</strong>
                            <?php echo nl2br(htmlspecialchars($order['cancel_reason'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (!empty($order['slip_image'])): ?>
                            <p class="font-medium">สลิปการชำระเงิน:</p>
                            <img src="../<?php echo ltrim($order['slip_image'], '/'); ?>" alt="slip" class="mt-2 max-h-64 object-contain border rounded" />
                        <?php else: ?>
                            <p class="text-gray-500">ไม่มีสลิป</p>
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST" class="mt-4">
                    <label class="block text-sm font-medium mb-2">เปลี่ยนสถานะ</label>
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="pending" <?php echo $order['status']=='pending' ? 'selected' : ''; ?>>รอดำเนินการ</option>
                        <option value="confirmed" <?php echo $order['status']=='confirmed' ? 'selected' : ''; ?>>ชำระเงินแล้ว</option>
                        <option value="shipped" <?php echo $order['status']=='shipped' ? 'selected' : ''; ?>>จัดส่งแล้ว</option>
                        <option value="delivered" <?php echo $order['status']=='delivered' ? 'selected' : ''; ?>>ส่งมอบแล้ว</option>
                        <option value="cancel_requested" <?php echo $order['status']=='cancel_requested' ? 'selected' : ''; ?>>รออนุมัติยกเลิก</option>
                        <option value="cancelled" <?php echo $order['status']=='cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                    </select>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">บันทึกสถานะ</button>
                        <a href="orders.php" class="ml-3 text-gray-600">กลับ</a>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold mb-4">รายการสินค้าในคำสั่งซื้อ</h2>
                <div class="space-y-4">
                    <?php while ($it = mysqli_fetch_assoc($items_q)): ?>
                        <div class="flex items-center space-x-4 border-b pb-4">
                            <?php if (!empty($it['image'])): ?>
                                <img src="../<?php echo ltrim($it['image'], '/'); ?>" alt="" class="w-24 h-16 object-cover rounded">
                            <?php endif; ?>
                            <div class="flex-1">
                                <div class="font-medium"><?php echo htmlspecialchars($it['title']); ?></div>
                                <div class="text-sm text-gray-600">จำนวน: <?php echo intval($it['quantity']); ?> × ฿<?php echo number_format($it['price'], 2); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
