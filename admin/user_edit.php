<?php
include '../config.php';
include '../check_admin.php';

// Get user ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<script>alert('ไม่พบผู้ใช้'); window.location.href = 'users.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Check if email already exists (excluding current user)
    $check_email = "SELECT id FROM users WHERE email = '$email' AND id != $id";
    $email_result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($email_result) > 0) {
        echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว');</script>";
    } else {
        $update_query = "UPDATE users SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            address = '$address'
            WHERE id = $id";
        
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href = 'users.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>แก้ไขข้อมูลผู้ใช้ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --accent:#7c3aed; }
        .bg-blue-700{ background-color: var(--admin-primary) !important; }
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
            <header class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">แก้ไขข้อมูลผู้ใช้</h1>
                <a href="users.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    กลับ
                </a>
            </header>

            <div class="bg-white rounded shadow p-6 max-w-2xl">
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">ชื่อผู้ใช้</label>
                        <input type="text" value="<?php echo $user['username']; ?>" class="w-full px-4 py-2 border rounded bg-gray-100" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">ชื่อ-นามสกุล</label>
                        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">อีเมล</label>
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">เบอร์โทร</label>
                        <input type="text" name="phone" value="<?php echo $user['phone']; ?>" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">ที่อยู่</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $user['address']; ?></textarea>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800">
                            บันทึก
                        </button>
                        <a href="users.php" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                            ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
