<?php
include '../config.php';
include '../check_admin.php';

$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>จัดการหนังสือ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --admin-accent:#7c3aed; --danger:#ef4444; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-blue-800 { background-color: #061024 !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
        .bg-green-600 { background-color: var(--admin-accent) !important; color:#fff !important; }
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
                <a href="orders.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
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
            <header class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold">จัดการหนังสือ</h1>
                <a href="product_add.php" class="bg-green-600 text-white px-4 py-2 rounded">เพิ่มหนังสือใหม่</a>
            </header>

            <div class="bg-white rounded shadow overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-700 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">ชื่อเรื่อง</th>
                            <th class="px-6 py-3 text-left">ผู้เขียน</th>
                            <th class="px-6 py-3 text-left">ราคา</th>
                            <th class="px-6 py-3 text-left">สต็อก</th>
                            <th class="px-6 py-3 text-left">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-4"><?php echo $row['title']; ?></td>
                            <td class="px-6 py-4"><?php echo $row['author']; ?></td>
                            <td class="px-6 py-4">฿<?php echo number_format($row['price'], 2); ?></td>
                            <td class="px-6 py-4"><?php echo $row['stock']; ?></td>
                            <td class="px-6 py-4">
                                <a href="product_edit.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-blue-600 text-white px-3 py-1 rounded mr-2">
                                    <!-- pencil -->
                                    แก้ไข
                                </a>
                                <a href="product_delete.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center bg-red-600 text-white px-3 py-1 rounded confirm-delete" data-confirm="<?php echo 'ยืนยันการลบหนังสือ #' . $row['id'] . ' ?'; ?>">
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

    <!-- Tailwind confirm modal -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4">
            <div class="p-4 border-b">
                <h3 id="confirm-title" class="text-lg font-semibold">ยืนยันการลบ</h3>
            </div>
            <div class="p-4">
                <p id="confirm-message" class="text-sm text-gray-700"></p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-600 mb-2">เหตุผล (ถ้ามี)</label>
                    <input id="confirm-reason" type="text" class="w-full px-3 py-2 border rounded" placeholder="ใส่เหตุผลการลบ (ไม่บังคับ)">
                </div>
            </div>
            <div class="flex justify-end p-4 border-t space-x-2">
                <button id="confirm-cancel" class="px-4 py-2 rounded bg-gray-200">ยกเลิก</button>
                <button id="confirm-ok" class="px-4 py-2 rounded bg-red-600 text-white">ลบ</button>
            </div>
        </div>
    </div>

</body>
</html>

<!-- Include Tailwind Confirm Modal -->
<?php include '../includes/confirm_modal.php'; ?>
