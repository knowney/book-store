<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'admin')";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>สร้างบัญชีแอดมิน สำเร็จ! <a href='login.php?type=admin'>เข้าสู่ระบบ</a></p>";
    } else {
        echo "<p style='color: red;'>เกิดข้อผิดพลาด: " . mysqli_error($conn) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>สร้างบัญชีแอดมิน</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; display: flex; justify-content: center; padding-top: 50px; }
        .form-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 400px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #0e6fc9ff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0a4a8f; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>สร้างบัญชีแอดมิน</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="ชื่อแอดมิน" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit">สร้างบัญชี</button>
        </form>
        <p style="margin-top: 15px;"><a href="login.php?type=admin">กลับไปเข้าสู่ระบบ</a></p>
    </div>
</body>
</html>
