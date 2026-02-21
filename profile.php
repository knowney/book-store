<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Check if email already exists for another user
    $check_email = "SELECT id FROM users WHERE email='$email' AND id != '$user_id'";
    $check_result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_msg = "อีเมลนี้ถูกใช้งานแล้ว";
    } else {
        $update_query = "UPDATE users SET full_name='$full_name', email='$email', phone='$phone', address='$address' WHERE id='$user_id'";
        if (mysqli_query($conn, $update_query)) {
            $success_msg = "อัปเดตข้อมูลสำเร็จ";
        } else {
            $error_msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
}

$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Fetch orders
$orders_query = "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);

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
    <title>โปรไฟล์ - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto p-6 max-w-5xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-gray-800"><i class="fas fa-user-circle mr-2"></i>โปรไฟล์ของคุณ</h2>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Form -->
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-700">ข้อมูลส่วนตัว</h3>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            ชื่อผู้ใช้ (ไม่สามารถเปลี่ยนได้)
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">
                            ชื่อ-นามสกุล
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="full_name" name="full_name" type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            อีเมล
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                            เบอร์โทรศัพท์
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="phone" name="phone" type="text" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                            ที่อยู่จัดส่ง
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 h-24" id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out" type="submit">
                            <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar / Quick Links -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="text-center mb-4">
                        <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto flex items-center justify-center text-gray-500 text-4xl mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['role'] == 'admin' ? 'ผู้ดูแลระบบ' : 'สมาชิกทั่วไป'); ?></p>
                    </div>
                    <hr class="my-4">
                    <div class="space-y-3">
                        <a href="orders_history.php" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-shopping-bag w-6 text-center"></i> ประวัติการสั่งซื้อ
                        </a>
                        <a href="cart.php" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-shopping-cart w-6 text-center"></i> ตะกร้าสินค้า
                        </a>
                        <?php if($user['role'] == 'admin'): ?>
                        <a href="admin/index.php" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-cog w-6 text-center"></i> จัดการระบบ
                        </a>
                        <?php endif; ?>
                        <a href="logout.php" class="flex items-center text-red-600 hover:text-red-800 transition mt-4">
                            <i class="fas fa-sign-out-alt w-6 text-center"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>