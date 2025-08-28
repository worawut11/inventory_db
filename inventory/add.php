<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้า</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ตั้งค่าพื้นฐานให้หน้า */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* กล่องฟอร์ม */
        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin: 12px 0 5px;
            font-weight: 600;
            text-align: left;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus {
            border-color: #007bff;
        }

        button {
            margin-top: 20px;
            padding: 10px 18px;
            border: none;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            padding: 8px 15px;
            background: #6c757d;
            color: #fff;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #5a6268;
        }

        .success {
            margin-top: 15px;
            color: #28a745;
        }

        .error {
            margin-top: 15px;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>➕ เพิ่มสินค้าใหม่</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>รหัสสินค้า</label>
            <input type="text" name="product_code" required>

            <label>ชื่อสินค้า</label>
            <input type="text" name="name" required>

            <label>จำนวน</label>
            <input type="number" name="quantity" required>

            <label>อัปโหลดรูป</label>
            <input type="file" name="image">

            <button type="submit" name="submit">บันทึก</button>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $code = $_POST['product_code'];
            $name = $_POST['name'];
            $qty  = $_POST['quantity'];

            $img = "";
            if ($_FILES['image']['name'] != "") {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir);
                $img = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $img);
            }

            $sql = "INSERT INTO products (product_code, name, quantity, image) VALUES ('$code','$name','$qty','$img')";
            if ($conn->query($sql)) {
                echo "<p class='success'>✅ เพิ่มสินค้าสำเร็จ</p>";
            } else {
                echo "<p class='error'>❌ เกิดข้อผิดพลาด</p>";
            }
        }
        ?>
        <a href="index.php" class="btn">⬅️ กลับหน้าแรก</a>
    </div>
</body>
</html>
