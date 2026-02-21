<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = intval($_SESSION['user_id']);

$success_msg = '';
$error_msg = '';

// Handle cancel request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = intval($_POST['cancel_order_id']);
    $cancel_reason = mysqli_real_escape_string($conn, $_POST['cancel_reason']);
    
    // Verify order belongs to user and is pending
    $check_q = mysqli_query($conn, "SELECT id FROM orders WHERE id='$cancel_id' AND user_id='$user_id' AND status='pending'");
    if (mysqli_num_rows($check_q) > 0) {
        if (mysqli_query($conn, "UPDATE orders SET status='cancel_requested', cancel_reason='$cancel_reason' WHERE id='$cancel_id'")) {
            $success_msg = "ส่งคำขอยกเลิกคำสั่งซื้อ #$cancel_id เรียบร้อยแล้ว";
        } else {
            $error_msg = "เกิดข้อผิดพลาดในการยกเลิกคำสั่งซื้อ";
        }
    } else {
        $error_msg = "ไม่สามารถยกเลิกคำสั่งซื้อนี้ได้";
    }
}


// Get current tab filter
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Build query based on tab
if ($current_tab === 'all') {
    $orders_q = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");
} else {
    $status_filter = mysqli_real_escape_string($conn, $current_tab);
    $orders_q = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' AND status='$status_filter' ORDER BY created_at DESC");
}

// Get cart count for navbar
$cart_count = 0;
$cnt_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='$user_id'");
if ($cnt_res && $cnt_row = mysqli_fetch_assoc($cnt_res)) {
    $cart_count = intval($cnt_row['cnt']);
}
?>
<!DOCTYPE html>
<html>
<head>
     <style>
        /* small custom styles for carousel and cards */
        .hero-slide { height: 420px; }
        @media (max-width: 640px) { .hero-slide { height: 240px; } }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .carousel-dot { width: 10px; height: 10px; border-radius: 9999px; }
        :root{
            --primary:#0f172a;      /* new primary (dark slate) */
            --primary-700:#0b1220;
            --accent:#f59e0b;       /* accent (amber) */
            --accent-600:#f59e0b;
            --muted:#6b7280;
            --danger:#ef4444;
            --surface:#0b1220;
            --on-primary:#ffffff;
        }
        /* Tailwind class overrides */
        .bg-blue-700 { background-color: var(--primary) !important; }
        .bg-blue-800 { background-color: var(--primary-700) !important; }
        .bg-blue-600 { background-color: var(--primary) !important; }
        .text-white  { color: var(--on-primary) !important; }
        .bg-green-600 { background-color: var(--accent) !important; color:#000 !important; }
        .bg-yellow-400 { background-color: var(--accent) !important; color:#000 !important; }
        .text-blue-600 { color: var(--primary) !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
        /* product card tweaks */
        .product-card { background: linear-gradient(180deg, #ffffff 0%, #fbfbfd 100%); }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
        }
    </style>
    <title>ประวัติการสั่งซื้อ - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
     
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'navbar.php'; ?>
   
    <main class="container mx-auto p-4 md:p-8 max-w-5xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">ประวัติการสั่งซื้อของคุณ</h1>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
            <div class="flex border-b border-gray-100">
                <button onclick="showTab('all')" id="tab-all" class="tab-btn active px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    ทั้งหมด
                </button>
                <button onclick="showTab('pending')" id="tab-pending" class="tab-btn px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    รอดำเนินการ
                </button>
                <button onclick="showTab('confirmed')" id="tab-confirmed" class="tab-btn px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    ชำระเงินแล้ว
                </button>
                <button onclick="showTab('cancelled')" id="tab-cancelled" class="tab-btn px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    ยกเลิกแล้ว
                </button>
                <button onclick="showTab('shipped')" id="tab-shipped" class="tab-btn px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    จัดส่งแล้ว
                </button>
                <button onclick="showTab('delivered')" id="tab-delivered" class="tab-btn px-6 py-3 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors">
                    ส่งมอบแล้ว
                </button>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($orders_q) == 0): ?>
            <div class="bg-white p-10 rounded-xl shadow-sm text-center border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="text-xl font-medium text-gray-600 mb-2">ยังไม่มีคำสั่งซื้อ</h3>
                <p class="text-gray-400 mb-6">คุณยังไม่ได้ทำการสั่งซื้อสินค้าใดๆ</p>
                <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">เลือกซื้อสินค้า</a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php while ($order = mysqli_fetch_assoc($orders_q)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Order Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex flex-wrap gap-x-8 gap-y-2">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">หมายเลขคำสั่งซื้อ</p>
                                    <p class="font-semibold text-gray-800">#<?php echo $order['id']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">วันที่สั่งซื้อ</p>
                                    <p class="font-medium text-gray-800"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">ยอดรวมทั้งสิ้น</p>
                                    <p class="font-bold text-blue-600">฿<?php echo number_format($order['total_price'], 2); ?></p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch($order['status']) {
                                        case 'pending':
                                            $status_class = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $status_text = 'รอดำเนินการ';
                                            break;
                                        case 'confirmed':
                                            $status_class = 'bg-blue-100 text-blue-800 border-blue-200';
                                            $status_text = 'ชำระเงินแล้ว';
                                            break;
                                        case 'shipped':
                                            $status_class = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                            $status_text = 'จัดส่งแล้ว';
                                            break;
                                        case 'delivered':
                                            $status_class = 'bg-green-100 text-green-800 border-green-200';
                                            $status_text = 'ส่งมอบแล้ว';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'bg-gray-100 text-gray-800 border-gray-200';
                                            $status_text = 'ยกเลิกแล้ว';
                                            break;
                                        case 'cancel_requested':
                                            $status_class = 'bg-red-100 text-red-800 border-red-200';
                                            $status_text = 'รออนุมัติยกเลิก';
                                            break;
                                    }
                                ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium border <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                                
                                <?php if ($order['status'] == 'pending'): ?>
                                    <button onclick="openCancelModal(<?php echo $order['id']; ?>)" class="text-sm text-red-500 hover:text-red-700 underline mt-1">
                                        ขอยกเลิกคำสั่งซื้อ
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <?php
                        $items_q = mysqli_query($conn, "SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = '{$order['id']}'");
                        ?>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php while ($it = mysqli_fetch_assoc($items_q)): ?>
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-20 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                                            <?php if (!empty($it['image'])): ?>
                                                <img src="<?php echo $it['image']; ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800 line-clamp-1"><?php echo htmlspecialchars($it['title']); ?></h4>
                                            <p class="text-sm text-gray-500 mt-1">จำนวน: <?php echo intval($it['quantity']); ?> ชิ้น</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-800">฿<?php echo number_format($it['price'] * $it['quantity'], 2); ?></p>
                                            <?php if($it['quantity'] > 1): ?>
                                                <p class="text-xs text-gray-500">(฿<?php echo number_format($it['price'], 2); ?> / ชิ้น)</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Cancel Order Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">ขอยกเลิกคำสั่งซื้อ</h2>
                <button onclick="closeCancelModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="">
                <input type="hidden" id="cancel_order_id" name="cancel_order_id" value="">
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">คุณกำลังขอยกเลิกคำสั่งซื้อหมายเลข <span id="display_order_id" class="font-bold"></span></p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">เหตุผลที่ต้องการยกเลิก <span class="text-red-500">*</span></label>
                    <textarea name="cancel_reason" rows="3" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="โปรดระบุเหตุผล..."></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">ปิด</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">ยืนยันการยกเลิก</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Update URL without reloading page
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({tab: tabName}, '', url);
            
            // Update active tab styling
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'text-blue-600', 'bg-gray-50');
                btn.classList.add('text-gray-700');
            });
            
            const activeBtn = document.getElementById('tab-' + tabName);
            if (activeBtn) {
                activeBtn.classList.add('active', 'text-blue-600', 'bg-gray-50');
                activeBtn.classList.remove('text-gray-700');
            }
            
            // Reload page to fetch filtered orders
            location.reload();
        }

        function openCancelModal(orderId) {
            document.getElementById('cancel_order_id').value = orderId;
            document.getElementById('display_order_id').innerText = '#' + orderId;
            document.getElementById('cancelModal').style.display = 'block';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('cancelModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Set initial active tab based on URL parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'all';
            const activeBtn = document.getElementById('tab-' + tab);
            if (activeBtn) {
                activeBtn.classList.add('active', 'text-blue-600', 'bg-gray-50');
                activeBtn.classList.remove('text-gray-700');
            }
        });
    </script>
</body>
</html>