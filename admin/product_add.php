<?php
include '../config.php';
include '../check_admin.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = '';

    // Handle image upload (filesystem path for moving, web-relative path for DB)
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file = $_FILES['image'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
            $upload_dir_fs = __DIR__ . '/../uploads/products/'; // filesystem folder
            $file_path_fs = $upload_dir_fs . $file_name;          // filesystem target
            $file_path_db = 'uploads/products/' . $file_name;     // web-relative path to store in DB

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
        $query = "INSERT INTO products (title, author, description, price, stock, image) VALUES ('$title', '$author', '$description', '$price', '$stock', '$image')";
        
        if (mysqli_query($conn, $query)) {
            $success = "เพิ่มหนังสือสำเร็จ!";
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
    <title>เพิ่มหนังสือ - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{ --admin-primary:#0b1220; --accent:#7c3aed; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: #f5f5f5; }
        .sidebar { background: #0e6fc9ff; color: white; width: 250px; position: fixed; height: 100vh; padding: 20px; }
        .content { margin-left: 250px; padding: 30px; }
        header { background: #0e6fc9ff; color: white; padding: 15px 30px; margin: -30px -30px 30px -30px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 500px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; font-family: sans-serif; }
        label { display: block; margin-top: 10px; font-weight: bold; color: #333; }
        button { width: 100%; padding: 10px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #229954; }
        .error { color: red; margin-bottom: 10px; padding: 10px; background: #ffebee; border-radius: 5px; }
        .success { color: green; margin-bottom: 10px; padding: 10px; background: #e8f5e9; border-radius: 5px; }
        a { color: #0e6fc9ff; text-decoration: none; }
        .bg-blue-700 { background-color: var(--admin-primary) !important; }
        .bg-blue-800 { background-color: #061024 !important; }
        .bg-green-600 { background-color: var(--accent) !important; color:#fff !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="fixed w-64 h-full bg-blue-700 text-white p-6">
        <h2 class="text-xl mb-6">Admin Panel</h2>
        <a href="index.php" class="block py-2">Dashboard</a>
        <a href="products.php" class="block py-2 bg-blue-800 rounded">จัดการหนังสือ</a>
        <a href="../logout.php" class="mt-6 inline-block bg-red-600 px-3 py-2 rounded">ออกจากระบบ</a>
    </div>
    <div class="ml-64 p-8">
        <header class="mb-6">
            <h1 class="text-2xl">เพิ่มหนังสือใหม่</h1>
        </header>
        <div class="bg-white p-6 rounded shadow max-w-lg">
            <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <label>ชื่อเรื่อง *</label>
                <input type="text" name="title" required>
                
                <label>ผู้เขียน</label>
                <input type="text" name="author">
                
                <label>รายละเอียด</label>
                <textarea name="description" rows="5"></textarea>
                
                <label>ราคา (บาท) *</label>
                <input type="number" name="price" step="0.01" required>
                
                <label>สต็อก *</label>
                <input type="number" name="stock" required>
                
                <label>รูปภาพ (jpg, png, gif)</label>
                <input type="file" name="image" accept="image/*">
                
                <button type="submit">บันทึก</button>
            </form>
            <p style="margin-top: 15px;"><a href="products.php">← กลับ</a></p>
        </div>
    </div>
</body>
</html>
