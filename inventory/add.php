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
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image'];

    $sql = "INSERT INTO products (product_code, name, quantity, image)
            VALUES ('$product_code', '$name', '$quantity', '$image')";
    
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ เกิดข้อผิดพลาดในการเพิ่มสินค้า";
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

    <form method="post">
        <label>รหัสสินค้า</label>
        <input type="text" name="product_code" placeholder="เช่น P001" required>

        <label>ชื่อสินค้า</label>
        <input type="text" name="name" placeholder="ชื่อสินค้า..." required>

        <label>จำนวน</label>
        <input type="number" name="quantity" placeholder="จำนวน..." required>

        <label>ลิงก์รูปภาพ (URL)</label>
        <input type="text" name="image" placeholder="https://example.com/image.jpg">

        <button type="submit" name="add">💾 บันทึกสินค้า</button>
    </form>

    <a href="index.php">⬅ กลับหน้าหลัก</a>
</div>
</body>
</html>
