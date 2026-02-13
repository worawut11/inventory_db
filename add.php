<?php
include 'db.php';
session_start();

// ถ้ายังไม่ได้ล็อกอิน → กลับหน้า login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['add'])) {

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $quantity    = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];


    // ตัวแปรเก็บชื่อไฟล์รูป
    $image_name = null;

    // ถ้ามีการอัปโหลดรูป
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
            $error = "❌ อัปโหลดรูปภาพไม่สำเร็จ";
        }
    }

    // บันทึกข้อมูลลง DB
    if (!isset($error)) {

        $stmt = $conn->prepare("
            INSERT INTO products (name, description, quantity, price, image)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssids", $name, $description, $quantity, $price, $image_name);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error = "❌ เพิ่มสินค้าไม่สำเร็จ: " . $stmt->error;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

            <label>ราคา (บาท)</label>
            <input type="number" name="price" step="0.01" placeholder="เช่น 199.00" required>

            <label>อัปโหลดรูปภาพสินค้า</label>
            <input type="file" name="image" accept="image/*">


            <button type="submit" name="add">💾 บันทึกสินค้า</button>
        </form>

        <a href="index.php">⬅ กลับหน้าหลัก</a>
    </div>
</body>

</html>