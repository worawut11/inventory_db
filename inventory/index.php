<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการสินค้า</title>
    <style>
        /* Reset เบื้องต้น */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* หน้า body */
        body {
            background: linear-gradient(120deg, #f6d365, #fda085);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }

        /* หัวข้อ */
        h2 {
            margin-bottom: 20px;
            color: #fff;
            font-size: 2.2rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }

        /* ปุ่มทั่วไป */
        .btn, .btn-danger {
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 8px;
            margin: 5px;
            display: inline-block;
            font-weight: bold;
            transition: all 0.3s ease;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }

        /* ปุ่มเพิ่ม/แก้ไข */
        .btn {
            background-color: #4CAF50;
        }

        .btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        /* ปุ่มลบ */
        .btn-danger {
            background-color: #f44336;
        }

        .btn-danger:hover {
            background-color: #da190b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        /* ตารางสินค้า */
        table {
            width: 95%;
            max-width: 1200px;
            border-collapse: collapse;
            background-color: rgba(255,255,255,0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            margin-top: 20px;
        }

        /* หัวตาราง */
        table th {
            background-color: #ff7e5f;
            color: #fff;
            font-weight: bold;
            padding: 14px 10px;
            text-align: center;
        }

        /* แถวข้อมูล */
        table td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        /* สลับสีแถว */
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* รูปสินค้า */
        table img {
            border-radius: 8px;
            transition: transform 0.3s;
        }

        table img:hover {
            transform: scale(1.1);
        }

        /* Responsive สำหรับมือถือ */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            table tr {
                margin-bottom: 15px;
            }
            table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: calc(50% - 30px);
                font-weight: bold;
                text-align: left;
            }
            table th {
                display: none;
            }
        }
    </style>
</head>
<body>
    <h2>📦 ระบบจัดการสินค้า</h2>
    <a href="add.php" class="btn">➕ เพิ่มสินค้า</a>
    <table>
        <tr>
            <th>ID</th>
            <th>รหัสสินค้า</th>
            <th>ชื่อสินค้า</th>
            <th>จำนวน</th>
            <th>รูปสินค้า</th>
            <th>การจัดการ</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
        if($result && $result->num_rows > 0){
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td data-label='ID'>{$row['id']}</td>
                    <td data-label='รหัสสินค้า'>{$row['product_code']}</td>
                    <td data-label='ชื่อสินค้า'>{$row['name']}</td>
                    <td data-label='จำนวน'>{$row['quantity']}</td>
                    <td data-label='รูปสินค้า'><img src='{$row['image']}' width='80'></td>
                    <td data-label='การจัดการ'>
                        <a href='edit.php?id={$row['id']}' class='btn'>✏️ แก้ไข</a>
                        <a href='delete.php?id={$row['id']}' class='btn-danger' onclick='return confirm(\"ยืนยันการลบ?\")'>🗑️ ลบ</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>ไม่มีสินค้าในระบบ</td></tr>";
        }
        ?>
    </table>
</body>
</html>
