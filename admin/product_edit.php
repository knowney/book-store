<?php
include '../config.php';
include '../check_admin.php';

$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM products WHERE id='$id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $product['image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file = $_FILES['image'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_ext)) {
            // Delete old image (use filesystem path)
            if ($product['image']) {
                $old_fs = __DIR__ . '/../' . $product['image'];
                if (file_exists($old_fs)) {
                    @unlink($old_fs);
                }
            }
            
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
            $upload_dir_fs = __DIR__ . '/../uploads/products/';
            $file_path_fs = $upload_dir_fs . $file_name;
            $file_path_db = 'uploads/products/' . $file_name;

            if (!is_dir($upload_dir_fs)) {
                mkdir($upload_dir_fs, 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $file_path_fs)) {
                $image = $file_path_db;
            } else {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            }
        } else {
            $error = "อนุญาตเฉพาะไฟล์ jpg, jpeg, png, gif เท่านั้น";
        }
    }

    if (!$error) {
        $update_query = "UPDATE products SET title='$title', author='$author', description='$description', price='$price', stock='$stock', image='$image' WHERE id='$id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success = "อัปเดตสำเร็จ!";
            header("Refresh: 2; url=products.php");
        } else {
            $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>แก้ไขหนังสือ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --accent:#7c3aed; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-blue-800 { background-color: #061024 !important; }
        .bg-blue-600 { background-color: var(--admin-primary) !important; color:#fff !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <div class="sidebar">
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
                    <a href="products.php" class="block px-3 py-2 rounded hover:bg-blue-800 flex items-center space-x-2 bg-blue-800">
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
        </div>

        <main class="ml-64 w-full p-8">
            <header class="mb-6">
                <h1 class="text-2xl font-semibold">แก้ไขหนังสือ</h1>
            </header>

            <div class="bg-white p-6 rounded shadow max-w-lg">
                <?php if ($error): ?><p class="text-red-600 mb-3"><?php echo $error; ?></p><?php endif; ?>
                <?php if ($success): ?><p class="text-green-600 mb-3"><?php echo $success; ?></p><?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <label class="block text-sm font-medium">ชื่อเรื่อง *</label>
                    <input type="text" name="title" value="<?php echo $product['title']; ?>" required class="w-full border rounded px-3 py-2">
                    
                    <label class="block text-sm font-medium">ผู้เขียน</label>
                    <input type="text" name="author" value="<?php echo $product['author']; ?>" class="w-full border rounded px-3 py-2">
                    
                    <label class="block text-sm font-medium">รายละเอียด</label>
                    <textarea name="description" rows="5" class="w-full border rounded px-3 py-2"><?php echo $product['description']; ?></textarea>
                    
                    <label class="block text-sm font-medium">ราคา (บาท) *</label>
                    <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required class="w-full border rounded px-3 py-2">
                    
                    <label class="block text-sm font-medium">สต็อก *</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required class="w-full border rounded px-3 py-2">
                    
                    <label class="block text-sm font-medium">รูปภาพปัจจุบัน</label>
                    <?php if ($product['image']): ?>
                        <img src="<?php echo $product['image']; ?>" class="w-48 h-32 object-cover rounded mb-3" alt="<?php echo $product['title']; ?>">
                    <?php else: ?>
                        <p class="text-gray-500">ไม่มีรูปภาพ</p>
                    <?php endif; ?>
                    
                    <label class="block text-sm font-medium">เปลี่ยนรูปภาพ (jpg, png, gif)</label>
                    <input type="file" name="image" accept="image/*" class="block">
                    
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded">บันทึกการเปลี่ยนแปลง</button>
                </form>
                <p class="mt-4"><a href="products.php" class="text-blue-600">← กลับ</a></p>
            </div>
        </main>
    </div>
</body>
</html>
