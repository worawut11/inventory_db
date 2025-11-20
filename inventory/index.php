<?php
include 'db.php';
session_start();

// ถ้าไม่ได้ล็อกอิน → กลับไปหน้า login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// id ผู้ใช้ที่ล็อกอินปัจจุบัน
$user_id = (int)$_SESSION['user'];

// ======================
// ดึงข้อมูลผู้ใช้
// ======================
$userSql   = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$userQuery = $conn->query($userSql);

if (!$userQuery) {
    die("SQL Error (users): " . $conn->error);
}

$user = $userQuery->fetch_assoc();

// กันกรณีหา user ไม่เจอ
if (!$user) {
    // ถ้าไม่พบ user ให้บังคับออกจากระบบ
    session_destroy();
    header("Location: login.php");
    exit;
}

// รูปผู้ใช้ ถ้าไม่มีใช้ default.png
$user_photo = (!empty($user['photo'])) ? $user['photo'] : 'default.png';

// ======================
// ดึงข้อมูลสินค้า
// ======================
$sql    = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("SQL Error (products): " . $conn->error);
}
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

    /* กล่องโปรไฟล์ผู้ใช้ */
    .user-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #eef2ff;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .user-info-wrap {
        display: flex;
        align-items: center;
    }
    .user-box img {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        margin-right: 12px;
        object-fit: cover;
        border: 2px solid #555;
    }
    .user-info .name {
        font-size: 16px;
        font-weight: 600;
        color: #111;
    }
    .user-info .small {
        font-size: 13px;
        color: #555;
    }
    .btn-logout {
        padding: 8px 14px;
        border-radius: 6px;
        background: #ff6666;
        color: #fff;
        border: none;
        text-decoration: none;
        font-size: 14px;
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
    .btn-edit { background: #3498db; }
    .btn-delete { background: #e74c3c; }
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
    img.product-img {
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
        .user-box {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>
</head>
<body>
    
<div class="container">
    <h2>📦 ระบบจัดการสินค้า</h2>

    <!-- กล่องแสดงข้อมูลผู้ใช้ที่ล็อกอิน -->
    <div class="user-box">
        <div class="user-info-wrap">
            <img src="<?= htmlspecialchars($user_photo); ?>" alt="User Photo">
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($user['username']); ?></div>
                <div class="small">
                    ID ผู้ใช้งาน : <?= $user['id']; ?> 
                    | สิทธิ์ : <?= htmlspecialchars($user['role']); ?>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">ออกจากระบบ</a>
    </div>

    <!-- ปุ่มเพิ่มสินค้า + Dashboard -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div></div>
        <div>
            <a href="add.php" class="btn" style="background:#aee8ff; color:black;">➕ เพิ่มสินค้าใหม่</a>
            <a href="dashboard.php" class="btn" style="background:#ffe4a8; color:black;">📊 ดูรายงานสต็อก</a>
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
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?= htmlspecialchars($row['image']); ?>" alt="สินค้า" class="product-img">
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
