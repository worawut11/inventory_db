<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบ id ที่ส่งมา
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id=$id");
if ($result->num_rows == 0) {
    echo "❌ ไม่พบข้อมูลสินค้า";
    exit;
}

$product = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image'];

    $sql = "UPDATE products SET 
            product_code='$product_code',
            name='$name', 
            quantity='$quantity',
            image='$image'
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขสินค้า</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        font-family: 'Prompt', sans-serif;
        background: #f4f7fc;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 20px;
    }
    h2 {
        text-align: center;
        color: #333;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    label {
        margin-top: 10px;
        font-weight: bold;
    }
    input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-top: 5px;
    }
    button {
        background: #4a90e2;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        margin-top: 20px;
        cursor: pointer;
    }
    button:hover {
        background: #357abd;
    }
    a {
        display: block;
        text-align: center;
        margin-top: 10px;
        color: #333;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="container">
    <h2>📝 แก้ไขสินค้า</h2>

    <?php if (isset($error)): ?>
        <p style="color:red; text-align:center;"><?= $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>รหัสสินค้า</label>
        <input type="text" name="product_code" value="<?= htmlspecialchars($product['product_code']); ?>" required>

        <label>ชื่อสินค้า</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label>จำนวน</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity']); ?>" required>

        <label>ลิงก์รูปภาพ</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']); ?>">

        <button type="submit" name="update">💾 บันทึกการแก้ไข</button>
    </form>

    <a href="index.php">⬅ กลับหน้าหลัก</a>
</div>
</body>
</html>
