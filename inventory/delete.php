<?php
include 'db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: index.php");
?>
<?php
// delete.php - ลบสินค้าจากฐานข้อมูล