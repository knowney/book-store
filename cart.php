<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// compute cart count for navbar badge
$cart_count = 0;
$cnt_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='".intval($user_id)."'");
if ($cnt_res && $cnt_row = mysqli_fetch_assoc($cnt_res)) {
    $cart_count = intval($cnt_row['cnt']);
}

$query = "SELECT c.*, p.title, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id='$user_id'";
$result = mysqli_query($conn, $query);

$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $total += $row['line_total'];
    $items[] = $row;
}

// Checkout handling moved here: process modal form submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (count($items) == 0) {
        $error = "ตะกร้าว่างเปล่า";
    } else {
        // create order
        $insert_order = "INSERT INTO orders (user_id, total_price, status) VALUES ('$user_id', '$total', 'pending')";
        if (mysqli_query($conn, $insert_order)) {
            $order_id = mysqli_insert_id($conn);
            // insert items
            foreach ($items as $item) {
                $pid = intval($item['product_id']);
                $qty = intval($item['quantity']);
                $price = floatval($item['price']);
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$pid', '$qty', '$price')");
            }
            // handle slip upload
            if (isset($_FILES['slip_image']) && $_FILES['slip_image']['size'] > 0) {
                $file = $_FILES['slip_image'];
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif'];
                if (in_array($file_ext, $allowed)) {
                    $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
                    $upload_dir = __DIR__ . '/uploads/slips/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $file_path_fs = $upload_dir . $file_name;
                    $file_path_db = 'uploads/slips/' . $file_name;
                    if (move_uploaded_file($file['tmp_name'], $file_path_fs)) {
                        mysqli_query($conn, "UPDATE orders SET slip_image='$file_path_db' WHERE id='$order_id'");
                    }
                } else {
                    // ignore invalid file types (optional: set $error)
                }
            }
            // clear cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");
            // refresh local data
            $success = "คำสั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ: #$order_id";
            $items = [];
            $total = 0;
            $cart_count = 0;
        } else {
            $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ตะกร้า - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --primary:#0f172a; --primary-700:#0b1220; --accent:#f59e0b; --danger:#ef4444; --muted:#6b7280; }
        .bg-blue-700 { background-color: var(--primary) !important; }
        .bg-blue-800 { background-color: var(--primary-700) !important; }
        .bg-yellow-400, .bg-green-600 { background-color: var(--accent) !important; color: #000 !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
        .text-gray-600 { color: var(--muted) !important; }
        .container { max-width: 1100px; margin: 20px auto; padding: 0 20px; }
        .product-card { transition: transform .12s ease, box-shadow .12s ease; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'navbar.php'; ?>
  

    <main class="container">
        <h2 class="text-2xl font-semibold my-6">ตะกร้าของคุณ</h2>

        <?php if ($error): ?><div class="mb-4 p-3 bg-red-50 text-red-700 rounded"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="mb-4 p-3 bg-green-50 text-green-700 rounded"><?php echo $success; ?></div><?php endif; ?>

        <?php if (count($items) > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- left: items list -->
            <section class="lg:col-span-2 space-y-4">
                <?php foreach ($items as $row): ?>
                <div class="bg-white p-4 rounded-lg flex items-start space-x-4 product-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="" class="w-28 h-32 object-cover rounded">
                    <?php else: ?>
                        <div class="w-28 h-32 bg-gray-100 flex items-center justify-center rounded text-sm text-gray-500">No Image</div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="text-sm text-gray-600 mt-1">฿<?php echo number_format($row['price'],2); ?></p>
                            </div>
                            <div class="text-sm text-gray-700">฿<?php echo number_format($row['line_total'],2); ?></div>
                        </div>

                        <div class="mt-3 flex items-center space-x-3">
                            <form action="update_cart.php" method="POST" class="flex items-center border rounded w-fit">
                                <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                <button type="button" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 border-r" onclick="const input = this.form.querySelector('input[name=quantity]'); input.stepDown(); this.form.submit();">-</button>
                                <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" class="w-16 text-center p-1 border-0 focus:ring-0 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" onchange="this.form.submit()" />
                                <button type="button" class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 border-l" onclick="const input = this.form.querySelector('input[name=quantity]'); input.stepUp(); this.form.submit();">+</button>
                            </form>

                            <a href="remove_from_cart.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center text-sm text-white bg-red-600 px-3 py-1 rounded confirm-delete" data-confirm="ลบรายการนี้ออกจากตะกร้า?">
                                ลบ
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>

            <!-- right: summary -->
            <aside class="bg-white p-6 rounded-lg h-fit">
                <h3 class="text-lg font-semibold">สรุปคำสั่งซื้อ</h3>
                <div class="mt-4 space-y-3">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>รวมสินค้า</span>
                        <span><?php echo count($items); ?> รายการ</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>ยอดรวม</span>
                        <span>฿<?php echo number_format($total,2); ?></span>
                    </div>
                    <div class="border-t pt-4">
                        <!-- open checkout modal -->
                        <button id="open-checkout" class="block w-full text-center bg-yellow-400 hover:bg-yellow-300 px-4 py-3 rounded font-semibold">ไปชำระเงิน</button>
                        <a href="index.php" class="mt-3 block text-center text-sm text-gray-700">กลับไปเลือกสินค้าต่อ</a>
                    </div>
                </div>
            </aside>
        </div>

        <?php else: ?>
        <div class="bg-white p-8 rounded-lg text-center">
            <h3 class="text-xl font-semibold mb-2">ตะกร้าของคุณว่างเปล่า</h3>
            <p class="text-gray-600 mb-4">ไปเลือกซื้อหนังสือสักเล่มนะ!</p>
            <a href="index.php" class="inline-block bg-blue-700 text-white px-4 py-2 rounded">กลับไปช้อปปิ้ง</a>
        </div>
        <?php endif; ?>
    </main>

    <!-- Checkout Modal (moved from checkout.php) -->
    <div id="checkout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">ยืนยันการสั่งซื้อ</h3>
                <button id="close-checkout" class="text-gray-600 hover:text-gray-800">ปิด ✕</button>
            </div>

            <form method="POST" enctype="multipart/form-data" class="p-4">
                <input type="hidden" name="confirm_order" value="1">

                <div class="mb-4">
                    <h4 class="font-medium">รายการสินค้า</h4>
                    <div class="mt-2 space-y-2">
                        <?php foreach ($items as $it): ?>
                        <div class="flex justify-between text-sm">
                            <div><?php echo htmlspecialchars($it['title']); ?> x <?php echo intval($it['quantity']); ?></div>
                            <div>฿<?php echo number_format($it['line_total'],2); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between font-semibold">
                        <div>ยอดรวมทั้งสิ้น</div>
                        <div>฿<?php echo number_format($total,2); ?></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block font-medium mb-2">อัปโหลดสลิปการโอนเงิน (ภาพ)</label>
                    <input type="file" name="slip_image" accept="image/*" required>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-checkout" class="px-4 py-2 rounded bg-gray-200">ยกเลิก</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-700 text-white">ยืนยันการสั่งซื้อ</button>
                </div>
            </form>
        </div>
    </div>

<!-- confirm handler & modal JS -->
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('a.confirm-delete').forEach(function(el){
    el.addEventListener('click', function(e){
      var msg = el.getAttribute('data-confirm') || 'ยืนยันการลบ?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  const modal = document.getElementById('checkout-modal');
  const openBtn = document.getElementById('open-checkout');
  const closeBtn = document.getElementById('close-checkout');
  const cancelBtn = document.getElementById('cancel-checkout');

  function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); window.scrollTo(0,0); }
  function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

  if (openBtn) openBtn.addEventListener('click', openModal);
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

  // close on ESC
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
  });
});
</script>
</body>
</html>
