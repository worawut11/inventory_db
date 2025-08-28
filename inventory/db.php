<?php
$servername = "localhost";
$username = "root";       // ปรับตาม XAMPP/MySQL ของคุณ
$password = "";           // ปรับตาม XAMPP/MySQL ของคุณ
$dbname = "inventory_db"; // ปรับตามฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// db.php - การเชื่อมต่อฐานข้อมูล MySQL