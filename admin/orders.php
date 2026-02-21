<?php
include '../config.php';
include '../check_admin.php';

$query = "SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
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
                <h1 class="text-2xl font-semibold">จัดการคำสั่งซื้อ</h1>
            </header>

            <div class="bg-white rounded shadow overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-700 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">ชื่อผู้ใช้</th>
                            <th class="px-6 py-3 text-left">ยอดรวม</th>
                            <th class="px-6 py-3 text-left">สถานะ</th>
                            <th class="px-6 py-3 text-left">วันที่</th>
                            <th class="px-6 py-3 text-left">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-4"><?php echo $row['full_name']; ?></td>
                            <td class="px-6 py-4">฿<?php echo number_format($row['total_price'], 2); ?></td>
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
                                <span class="px-2 py-1 rounded text-sm <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                <?php if($row['status'] == 'cancel_requested'): ?>
                                    <div class="text-xs text-red-600 mt-1 font-medium">มีคำขอยกเลิก</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4"><?php echo substr($row['created_at'], 0, 10); ?></td>
                            <td class="px-6 py-4">
                                <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-blue-600 text-white px-3 py-1 rounded mr-2">
                                    <!-- eye -->
                                    ดูรายละเอียด
                                </a>

                                <a href="order_delete.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-red-600 text-white px-3 py-1 rounded confirm-delete" data-confirm="<?php echo 'ยืนยันการลบคำสั่งซื้อ #' . $row['id'] . ' ?'; ?>">
                                    <!-- trash -->
                                    ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

<!-- add confirm handler -->
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('a.confirm-delete').forEach(function(el){
    el.addEventListener('click', function(e){
      var msg = el.getAttribute('data-confirm') || 'ยืนยันการลบ?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
});
</script>
