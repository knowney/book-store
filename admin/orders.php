<?php
include '../config.php';
include '../check_admin.php';

// 1. รับค่าสำหรับการค้นหาและกรองข้อมูล
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// 2. ตั้งค่า Pagination
$limit = 10; // จำนวนรายการต่อหน้า
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 3. สร้างเงื่อนไข SQL (WHERE clause)
$where_clauses = [];
if (!empty($search)) {
    // ค้นหาจาก ID คำสั่งซื้อ หรือ ชื่อผู้ใช้
    $where_clauses[] = "(o.id LIKE '%$search%' OR u.full_name LIKE '%$search%')";
}
if (!empty($status_filter)) {
    $where_clauses[] = "o.status = '$status_filter'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// 4. หาจำนวนรายการทั้งหมดเพื่อไปทำปุ่มหน้า (Pagination)
$count_query = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.user_id = u.id $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// 5. ดึงข้อมูลหลัก
$query = "SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id $where_sql ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// สร้างตัวแปรเก็บ Query String สำหรับปุ่มเปลี่ยนหน้า
$query_string = "";
if (!empty($search)) $query_string .= "&search=" . urlencode($search);
if (!empty($status_filter)) $query_string .= "&status=" . urlencode($status_filter);
?>
<!DOCTYPE html>
<html>
<head>
    <title>จัดการคำสั่งซื้อ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --accent:#7c3aed; --danger:#ef4444; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-blue-800 { background-color: #061024 !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
        .status-pending { background: #f59e0b; color: #fff; }
        .status-confirmed { background: #10b981; color: #fff; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <aside class="w-64 h-screen bg-blue-700 text-white p-6 fixed">
            <h2 class="text-xl font-semibold mb-6 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 22C7.029 22 3 17.97 3 13V8l9-4 9 4v5c0 4.97-4.029 9-9 9z" />
                </svg>
                <span>Admin Panel</span>
            </h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                    </svg><span>Dashboard</span>
                </a>
                <a href="products.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z" />
                    </svg><span>จัดการหนังสือ</span>
                </a>
                <a href="users.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-4M12 12a4 4 0 100-8 4 4 0 000 8z" />
                    </svg><span>จัดการผู้ใช้</span>
                </a>
                <a href="orders.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2 bg-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14l-2 2H7l-2-2V5a2 2 0 012-2z" />
                    </svg><span>จัดการคำสั่งซื้อ</span>
                </a>
                <a href="../logout.php" class="inline-block mt-6 bg-red-600 px-3 py-2 rounded flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg><span>ออกจากระบบ</span>
                </a>
            </nav>
        </aside>

        <main class="ml-64 w-full p-8">
            <header class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">จัดการคำสั่งซื้อ</h1>
            </header>

            <div class="bg-white p-4 rounded shadow mb-6">
                <form method="GET" action="orders.php" class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหาจากรหัสคำสั่งซื้อ หรือ ชื่อผู้ใช้..." class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="status" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- ทุกสถานะ --</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>รอดำเนินการ</option>
                            <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>ชำระเงินแล้ว</option>
                            <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>จัดส่งแล้ว</option>
                            <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>ส่งมอบแล้ว</option>
                            <option value="cancel_requested" <?php echo $status_filter == 'cancel_requested' ? 'selected' : ''; ?>>รออนุมัติยกเลิก</option>
                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>ยกเลิกแล้ว</option>
                        </select>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors w-full md:w-auto">ค้นหา</button>
                        <?php if(!empty($search) || !empty($status_filter)): ?>
                            <a href="orders.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors text-center w-full md:w-auto">ล้างค่า</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">ชื่อผู้ใช้</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">ยอดรวม</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">สถานะ</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">วันที่</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">#<?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">฿<?php echo number_format($row['total_price'], 2); ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch($row['status']) {
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
                                                    $status_text = $row['status'];
                                            }
                                        ?>
                                        <span class="px-2 py-1 rounded text-xs font-medium <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        <?php if($row['status'] == 'cancel_requested'): ?>
                                            <div class="text-xs text-red-600 mt-1 font-medium">มีคำขอยกเลิก</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo substr($row['created_at'], 0, 10); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2 transition-colors">
                                            ดูรายละเอียด
                                        </a>
                                        <a href="order_delete.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded confirm-delete transition-colors" data-confirm="<?php echo 'ยืนยันการลบคำสั่งซื้อ #' . $row['id'] . ' ?'; ?>">
                                            ลบ
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        ไม่พบข้อมูลคำสั่งซื้อที่ค้นหา
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($total_pages > 1): ?>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        แสดงข้อมูลหน้า <span class="font-medium"><?php echo $page; ?></span> จากทั้งหมด <span class="font-medium"><?php echo $total_pages; ?></span> หน้า (รวม <?php echo $total_rows; ?> รายการ)
                    </div>
                    <div class="flex space-x-1">
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo ($page - 1) . $query_string; ?>" class="px-3 py-1 border border-gray-300 rounded bg-white text-gray-600 hover:bg-gray-50">ก่อนหน้า</a>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i . $query_string; ?>" class="px-3 py-1 border border-gray-300 rounded <?php echo $i == $page ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?php echo ($page + 1) . $query_string; ?>" class="px-3 py-1 border border-gray-300 rounded bg-white text-gray-600 hover:bg-gray-50">ถัดไป</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

<?php include '../includes/confirm_modal.php'; ?>