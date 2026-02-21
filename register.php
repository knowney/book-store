<?php
include 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    $query = "INSERT INTO users (username, email, password, full_name) VALUES ('$username', '$email', '$password', '$full_name')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: login.php");
    } else {
        $error = "สมัครสมาชิกไม่สำเร็จ: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>สมัครสมาชิก - Book Store</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; display: flex; justify-content: center; padding-top: 50px; }
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 400px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #0e6fc9ff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0a4a8f; }
        .error { color: red; margin-bottom: 10px; }
        a { color: #0e6fc9ff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>สมัครสมาชิก</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="ชื่อ-นามสกุล" required>
            <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit">สมัครสมาชิก</button>
        </form>
        <p style="margin-top: 15px;">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
    </div>
</body>
</html>
