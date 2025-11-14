<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ระบบจัดการสินค้า</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        font-family: 'Prompt', sans-serif;
        background: #f4f7fc;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 95%;
        width: 900px;
        margin: 30px auto;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border-bottom: 1px solid #ddd;
        text-align: center;
        padding: 10px;
    }
    th {
        background: #4a90e2;
        color: white;
    }
    tr:hover {
        background: #f1f1f1;
    }
    a.btn, button.btn {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        font-size: 14px;
    }
    .btn-edit {
        background: #3498db;
    }
    .btn-delete {
        background: #e74c3c;
    }
    .btn-add {
        display: block;
        width: 100%;
        margin-top: 20px;
        background: #2ecc71;
        text-align: center;
        padding: 12px;
        color: white;
        border-radius: 8px;
        font-size: 16px;
        text-decoration: none;
    }
    img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 600px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        th { display: none; }
        td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }
        td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            width: 45%;
            font-weight: bold;
            text-align: left;
        }
    }
</style>
</head>
<body>
    
<div class="container">
    <h2>📦 ระบบจัดการสินค้า</h2>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
  <h2></h2>
  <div>
<a href="add.php" class="btn btn-success" style="color: black;">➕ เพิ่มสินค้าใหม่</a>
<a href="dashboard.php" class="btn btn-primary" style="color: black;">📊 ดูรายงานสต็อก</a>

  </div>
</div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>ภาพ</th>
                <th>วันที่เพิ่ม</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $row['id']; ?></td>
                <td data-label="รหัสสินค้า"><?= htmlspecialchars($row['product_code']); ?></td>
                <td data-label="ชื่อสินค้า"><?= htmlspecialchars($row['name']); ?></td>
                <td data-label="จำนวน"><?= $row['quantity']; ?></td>
                <td data-label="ภาพ">
                    <?php if ($row['image']): ?>
                        <img src="<?= htmlspecialchars($row['image']); ?>" alt="สินค้า">
                    <?php else: ?>
                        ไม่มีรูป
                    <?php endif; ?>
                </td>
                <td data-label="วันที่"><?= $row['created_at']; ?></td>
                <td data-label="จัดการ">
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-edit">แก้ไข</a>
                    <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-delete" onclick="return confirm('ต้องการลบสินค้านี้หรือไม่?')">ลบ</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="add.php" class="btn-add">➕ เพิ่มสินค้าใหม่</a>
</div>
</body>
</html>
