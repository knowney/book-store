<?php
include 'config.php';

$error = '';
$success = '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'login';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($tab == 'register') {
        // Register
        $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';

        // Check if username already exists
        $check_query = "SELECT id FROM users WHERE username='$username'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "ชื่อผู้ใช้นี้มีอยู่แล้ว";
        } else if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $query = "INSERT INTO users (username, email, password, full_name, role) VALUES ('$username', '$email', '$password_hash', '$full_name', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $success = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
                $tab = 'login';
            } else {
                $error = "สมัครสมาชิกไม่สำเร็จ: " . mysqli_error($conn);
            }
        }
    } else {
        // Login
        $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                if ($row['role'] == 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
            } else {
                $error = "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $error = "ไม่พบผู้ใช้";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>เข้าสู่ระบบ - Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --primary:#0f172a; --accent:#f59e0b; --danger:#ef4444; --on-primary:#fff; }
        .bg-blue-700 { background-color: var(--primary) !important; }
        .bg-blue-600 { background-color: var(--primary) !important; }
        .border-blue-600 { border-color: var(--primary) !important; }
        .bg-blue-800 { background-color: #071022 !important; }
        .bg-green-600, .bg-yellow-400 { background-color: var(--accent) !important; color:#000 !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-start justify-center py-12">
  

    <div class="w-full max-w-md">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="flex">
                <a href="?tab=login" class="w-1/2 text-center py-4 <?php echo ($tab=='login') ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-600'; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h12" />
                    </svg>
                    เข้าสู่ระบบ
                </a>
                <a href="?tab=register" class="w-1/2 text-center py-4 <?php echo ($tab=='register') ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-600'; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v6M21 12h-6M15 7a4 4 0 11-8 0 4 4 0 018 0zM3 21a6 6 0 0112 0" />
                    </svg>
                    สมัครสมาชิก
                </a>
            </div>

            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4"><?php echo ($tab == 'login') ? 'เข้าสู่ระบบ' : 'สมัครสมาชิก'; ?></h2>

                <?php if ($error): ?><div class="mb-3 p-3 bg-red-50 text-red-700 rounded"><?php echo $error; ?></div><?php endif; ?>
                <?php if ($success): ?><div class="mb-3 p-3 bg-green-50 text-green-700 rounded"><?php echo $success; ?></div><?php endif; ?>

                <form method="POST" class="space-y-4">
                    <?php if ($tab == 'register'): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ชื่อ-นามสกุล</label>
                            <input name="full_name" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้</label>
                            <input name="username" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">อีเมล</label>
                            <input type="email" name="email" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                            <input type="password" name="password" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>สมัครสมาชิก</span>
                        </button>
                    <?php else: ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้</label>
                            <input name="username" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                            <input type="password" name="password" required class="mt-1 block w-full px-3 py-2 border rounded" />
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h12" /></svg>
                            <span>เข้าสู่ระบบ</span>
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 text-sm text-gray-600">
            <a href="index.php" class="text-blue-600">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</body>
</html>
