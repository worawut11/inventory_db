<?php 
include 'db.php'; 
session_start();

// เช็คว่า id ถูกส่งมาหรือไม่
if (!isset($_GET['id'])) {
    die("❌ ไม่พบสินค้าที่ต้องการแก้ไข");
}

$id = (int)$_GET['id']; // แปลงเป็น integer เพื่อความปลอดภัย

// ดึงข้อมูลสินค้า
$result = $conn->query("SELECT * FROM products WHERE id=$id");
if (!$result || $result->num_rows == 0) {
    die("❌ ไม่พบสินค้าที่ต้องการแก้ไข");
}
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <style>
        /* จัดหน้าให้อยู่กึ่งกลาง */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #555;
        }
        input[type=text], input[type=number], input[type=file] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #45a049;
        }
        .success { color: green; text-align:center; margin-top: 15px; }
        .error { color: red; text-align:center; margin-top: 15px; }
        .btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            text-align: center;
            padding: 10px 15px;
            background: #2196F3;
            color: #fff;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ แก้ไขสินค้า</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>รหัสสินค้า</label>
            <input type="text" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>" required>

            <label>ชื่อสินค้า</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label>จำนวน</label>
            <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required>

            <label>อัปโหลดรูป (เว้นว่างถ้าไม่เปลี่ยน)</label>
            <input type="file" name="image">

            <button type="submit" name="submit">อัปเดต</button>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $code = $_POST['product_code'];
            $name = $_POST['name'];
            $qty  = $_POST['quantity'];

            $img = $product['image'];
            if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $img = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $img);
            }

            $sql = "UPDATE products SET product_code='$code', name='$name', quantity='$qty', image='$img' WHERE id=$id";
            if ($conn->query($sql)) {
                echo "<p class='success'>✅ แก้ไขสำเร็จ</p>";
            } else {
                echo "<p class='error'>❌ เกิดข้อผิดพลาด: ".$conn->error."</p>";
            }
        }
        ?>
        <a href="index.php" class="btn">⬅️ กลับหน้าแรก</a>
    </div>
</body>
</html>
