<?php
include 'config.php';

$new_password = password_hash('1111', PASSWORD_DEFAULT);
$query = "UPDATE users SET password = '$new_password' WHERE username = 'admin'";

if (mysqli_query($conn, $query)) {
    echo "<p style='color: green; font-size: 18px;'>✓ อัปเดตรหัสผ่านแอดมิน สำเร็จ!</p>";
    echo "<p>ชื่อ: admin | รหัสผ่าน: 1111</p>";
    echo "<p><a href='login.php'>กลับไปเข้าสู่ระบบ</a></p>";
} else {
    echo "<p style='color: red;'>เกิดข้อผิดพลาด: " . mysqli_error($conn) . "</p>";
}
?>
