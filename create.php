
<?php
include 'db_conn.php';

if ($_POST) {
    $user = $_POST['username'];
    $email = $_POST['email'];

    $sql = "INSERT INTO users (username, email) VALUES ('$user', '$email')";
    
    if ($conn->query($sql)) {
        header("Location: read.php"); 
    }
}
?>

<h2>เพิ่มข้อมูลผู้ใช้</h2>
<form method="post">
    ชื่อ: <input type="text" name="username" required> <br><br>
    อีเมล: <input type="email" name="email" required> <br><br>
    <button type="submit">บันทึกข้อมูล</button>
    <a href="read.php">ยกเลิก</a>
</form>