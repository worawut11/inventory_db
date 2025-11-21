<?php
include 'db.php';
session_start();

// ถ้ายังไม่ได้ล็อกอิน → กลับหน้า login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['add'])) {
    $product_code = $_POST['product_code'];
    $name         = $_POST['name'];
    $quantity     = $_POST['quantity'];

    // เตรียมตัวแปรเก็บชื่อไฟล์ภาพ (ที่จะบันทึกลงฐานข้อมูล)
    $image_name = null;

    // ถ้ามีการอัปโหลดไฟล์รูปมา
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        // โฟลเดอร์สำหรับเก็บรูป (สร้างโฟลเดอร์นี้ไว้ในโปรเจกต์ก่อน เช่น htdocs/inventory/uploads)
        $upload_dir = 'uploads/';

        // ดึงข้อมูลไฟล์
        $tmp_name   = $_FILES['image']['tmp_name'];
        $original   = basename($_FILES['image']['name']);

        // ต่อเวลา (นาที+วินาที) กันชื่อซ้ำก็ได้
        $ext        = pathinfo($original, PATHINFO_EXTENSION);
        $safe_name  = pathinfo($original, PATHINFO_FILENAME);
        $safe_name  = preg_replace('/[^A-Za-z0-9_-]/', '_', $safe_name); // เคลียร์ตัวอักษรแปลก ๆ
        $image_name = $safe_name . '_' . time() . '.' . $ext;

        $target_path = $upload_dir . $image_name;

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // ย้ายไฟล์จาก temp ไปยังโฟลเดอร์จริง
        if (!move_uploaded_file($tmp_name, $target_path)) {
            $error = "❌ อัปโหลดรูปภาพไม่สำเร็จ";
        }
    }

    // ถ้าไม่มี error เรื่องอัปโหลดรูป → ค่อย insert
    if (!isset($error)) {
        // ถ้าไม่อัปโหลดรูป image_name จะเป็น null ก็เก็บค่าว่างไปได้
        $sql = "INSERT INTO products (product_code, name, quantity, image)
                VALUES ('$product_code', '$name', '$quantity', '$image_name')";

        if ($conn->query($sql)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "❌ เกิดข้อผิดพลาดในการเพิ่มสินค้า";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เพิ่มสินค้าใหม่</title>
<style>
    body {
        font-family: 'Prompt', sans-serif;
        background: #f2f6fc;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 500px;
        margin: 50px auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
    }
    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-top: 5px;
    }
    button {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        width: 100%;
        margin-top: 20px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background: #218838;
    }
    a {
        display: block;
        text-align: center;
        margin-top: 15px;
        text-decoration: none;
        color: #333;
    }
    a:hover {
        color: #007bff;
    }
    .error {
        color: red;
        text-align: center;
    }
</style>
</head>
<body>
<div class="container">
    <h2>➕ เพิ่มสินค้าใหม่</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <!-- สำคัญ: enctype="multipart/form-data" เพื่อให้อัปโหลดไฟล์ได้ -->
    <form method="post" enctype="multipart/form-data">
        <label>รหัสสินค้า</label>
        <input type="text" name="product_code" placeholder="เช่น P001" required>

        <label>ชื่อสินค้า</label>
        <input type="text" name="name" placeholder="ชื่อสินค้า..." required>

        <label>จำนวน</label>
        <input type="number" name="quantity" placeholder="จำนวน..." required>

        <label>อัปโหลดรูปภาพสินค้า</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="add">💾 บันทึกสินค้า</button>
    </form>

    <a href="index.php">⬅ กลับหน้าหลัก</a>
</div>
</body>
</html>
