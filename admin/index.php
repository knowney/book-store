<?php
include '../config.php';
include '../check_admin.php';

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='pending'"))['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --admin-accent:#7c3aed; --danger:#ef4444; --muted:#94a3b8; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-blue-800 { background-color: #061024 !important; }
        .bg-green-600 { background-color: var(--admin-accent) !important; color:#fff !important; }
        .text-gray-600 { color: var(--muted) !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <aside class="w-64 h-screen bg-blue-700 text-white p-6 fixed">
            <h2 class="text-xl font-semibold mb-6 flex items-center space-x-2">
                <!-- shield-check icon -->
                                                                                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 22C7.029 22 3 17.97 3 13V8l9-4 9 4v5c0 4.97-4.029 9-9 9z" />
                </svg>
                <span>Admin Panel</span>
            </h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <!-- chart-bar -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18" />
                    </svg><span>Dashboard</span>
                </a>
                <a href="products.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <!-- book-open -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z" />
                    </svg><span>จัดการหนังสือ</span>
                </a>
                <a href="users.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <!-- users -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-4M12 12a4 4 0 100-8 4 4 0 000 8z" />
                    </svg><span>จัดการผู้ใช้</span>
                </a>
                <a href="orders.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2">
                    <!-- clipboard-list -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v14l-2 2H7l-2-2V5a2 2 0 012-2z" />
                    </svg><span>จัดการคำสั่งซื้อ</span>
                </a>
                <a href="../logout.php" class="inline-block mt-6 bg-red-600 px-3 py-2 rounded flex items-center space-x-2">
                    <!-- logout -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg><span>ออกจากระบบ</span>
                </a>
            </nav>
        </aside>

        <main class="ml-64 w-full p-8">
            <header class="bg-white rounded shadow p-4 mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Dashboard</h1>
                <p class="text-sm text-gray-600">ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?></p>
            </header>

            <div class="grid gap-6 grid-cols-1 md:grid-cols-3">
                <div class="bg-white p-6 rounded shadow text-center">
                    <h3 class="text-lg text-gray-600">หนังสือทั้งหมด</h3>
                    <div class="text-3xl font-bold text-red-600 mt-2"><?php echo $total_products; ?></div>
                </div>
                <div class="bg-white p-6 rounded shadow text-center">
                    <h3 class="text-lg text-gray-600">ผู้ใช้ทั้งหมด</h3>
                    <div class="text-3xl font-bold text-red-600 mt-2"><?php echo $total_users; ?></div>
                </div>
                <div class="bg-white p-6 rounded shadow text-center">
                    <h3 class="text-lg text-gray-600">คำสั่งซื้อรอดำเนินการ</h3>
                    <div class="text-3xl font-bold text-red-600 mt-2"><?php echo $pending_orders; ?></div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
