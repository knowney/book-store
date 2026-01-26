<?php
include 'db_conn.php';

// 1. ดึงข้อมูลเก่ามาแสดงในฟอร์ม
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$row = $result->fetch_assoc();

// 2. ส่วนของการอัปเดตข้อมูลเมื่อกดปุ่ม
if ($_POST) {
    $user = $_POST['username'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET username='$user', email='$email' WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: read.php"); // อัปเดตเสร็จ กลับไปหน้าตาราง
    }
}
?>

<h2>แก้ไขข้อมูลผู้ใช้</h2>
<form method="post">
    ชื่อ: <input type="text" name="username" value="<?php echo $row['username']; ?>"> <br><br>
    อีเมล: <input type="email" name="email" value="<?php echo $row['email']; ?>"> <br><br>
    <button type="submit">อัปเดตข้อมูล</button>
    <a href="read.php">ยกเลิก</a>
</form>