<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบ id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "❌ ไม่พบข้อมูลสินค้า";
    exit;
}

$product = $result->fetch_assoc();

// เมื่อกดอัปเดต
if (isset($_POST['update'])) {

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $quantity    = (int)$_POST['quantity'];
    $price       = (float)$_POST['price'];

    $image_name = $product['image'];

    // ถ้ามีการอัปโหลดรูปใหม่
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $upload_dir = 'uploads/';
        $tmp_name   = $_FILES['image']['tmp_name'];
        $original   = basename($_FILES['image']['name']);

        $ext        = pathinfo($original, PATHINFO_EXTENSION);
        $safe_name  = pathinfo($original, PATHINFO_FILENAME);
        $safe_name  = preg_replace('/[^A-Za-z0-9_-]/', '_', $safe_name);

        $image_name = $safe_name . '_' . time() . '.' . $ext;
        $target     = $upload_dir . $image_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!move_uploaded_file($tmp_name, $target)) {
            $error = "❌ อัปโหลดรูปไม่สำเร็จ";
        }
    }

    // อัปเดตข้อมูล
    if (!isset($error)) {

        $stmt = $conn->prepare("
            UPDATE products 
            SET name = ?, description = ?, quantity = ?, price = ?, image = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ssidsi", $name, $description, $quantity, $price, $image_name, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error = "❌ อัปเดตไม่สำเร็จ: " . $stmt->error;
        }
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            <input type="text" name="product_code"
                value="<?= htmlspecialchars($product['product_code']); ?>" required>

            <label>ชื่อสินค้า</label>
            <input type="text" name="name"
                value="<?= htmlspecialchars($product['name']); ?>" required>

            <label>จำนวน</label>
            <input type="number" name="quantity"
                value="<?= htmlspecialchars($product['quantity']); ?>" required>

            <label>ลิงก์รูปภาพ</label>

            <?php if (!empty($product['image'])): ?>
                <!-- แสดงรูปตัวอย่าง -->
                <div style="margin:10px 0; text-align:center;">
                    <img src="<?= htmlspecialchars($product['image']); ?>"
                        alt="รูปสินค้า"
                        style="max-width:150px; max-height:150px; border-radius:8px; border:1px solid #ccc;">
                    <br>
                    <a href="<?= htmlspecialchars($product['image']); ?>"
                        target="_blank"
                        style="display:inline-block; margin-top:5px; font-size:13px;">
                        🔍 เปิดดูรูปเต็ม
                    </a>
                </div>
            <?php endif; ?>
            <label>อัปโหลดรูปภาพสินค้า</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" name="update">💾 บันทึกการแก้ไข</button>
        </form>

        <a href="index.php">⬅ กลับหน้าหลัก</a>
    </div>
</body>

</html>