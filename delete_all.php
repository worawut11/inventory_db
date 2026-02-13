<?php
include 'db.php';
session_start();

// 🔥 ให้ admin เท่านั้น
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// ===== ลบข้อมูลใน DB =====
$conn->query("TRUNCATE TABLE products");

// ===== ลบรูปใน uploads =====
$folder = "uploads/";

$files = glob($folder . "*");

foreach ($files as $file) {

    // กันลบ default.png
    if (is_file($file) && basename($file) !== "default.png") {
        unlink($file);
    }
}

header("Location: index.php");
exit;
