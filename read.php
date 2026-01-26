<?php
include 'db_conn.php';

// ดึงข้อมูลทั้งหมดจากตาราง users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// ส่วนของการลบข้อมูล (Delete)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: read.php"); // ลบเสร็จให้ Refresh หน้าตัวเอง
}
?>

<h2>รายชื่อผู้ใช้งาน</h2>
<a href="create.php"> ++ เพิ่มข้อมูลใหม่</a>
<table border="1" cellpadding="10" style="margin-top: 10px;">
    <tr>
        <th>ID</th>
        <th>ชื่อผู้ใช้</th>
        <th>อีเมล</th>
        <th>จัดการ</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td>
            <a href="update.php?id=<?php echo $row['id']; ?>">แก้ไข</a> | 
            <a href="read.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
        </td>
    </tr>
    <?php } ?>
</table>