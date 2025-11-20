<?php
include 'db.php';
session_start();

// ถ้าไม่ได้ล็อกอิน → กลับไป login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// id ผู้ใช้ที่ล็อกอินปัจจุบัน
$user_id = (int)$_SESSION['user'];

// ดึงข้อมูลผู้ใช้
$userSql   = "SELECT id, username, photo, role FROM users WHERE id = $user_id LIMIT 1";
$userQuery = $conn->query($userSql);

if (!$userQuery || $userQuery->num_rows == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user = $userQuery->fetch_assoc(); // fetch_assoc() ครั้งเดียว
$user_photo = (!empty($user['photo'])) ? $user['photo'] : 'default.png';
$user_role  = (!empty($user['role'])) ? $user['role'] : '-';

// ดึงข้อมูลสินค้า
$sql    = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("SQL Error (products): " . $conn->error);
}

// ======================
// ดึงข้อมูลสรุป (Summary)
// ======================

// 1) จำนวนสินค้ารวมทั้งหมด (sum ของ quantity)
$totalQty = 0;
$resTotal = $conn->query("SELECT SUM(quantity) AS total_qty FROM products");
if ($resTotal && $row = $resTotal->fetch_assoc()) {
    $totalQty = (int)$row['total_qty'];
}

// 2) จำนวน SKU (รหัสสินค้าไม่ซ้ำ)
$totalSKU = 0;
$resSKU = $conn->query("SELECT COUNT(DISTINCT product_code) AS total_sku FROM products");
if ($resSKU && $row = $resSKU->fetch_assoc()) {
    $totalSKU = (int)$row['total_sku'];
}

// 3) จำนวนสินค้าใกล้หมดสต็อก (quantity <= min_stock และ min_stock > 0)
$lowStock = 0;
$resLow = $conn->query("SELECT COUNT(*) AS low_stock FROM products WHERE min_stock > 0 AND quantity <= min_stock");
if ($resLow && $row = $resLow->fetch_assoc()) {
    $lowStock = (int)$row['low_stock'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Smart Warehouse Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* --- CSS เหมือนเดิม --- */
body {
    font-family: 'Prompt', sans-serif;
    background: #f4f7fc;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 95%;
    width: 1100px;
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

/* SUMMARY CARDS */
.summary-box {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}
.card {
    border-radius: 12px;
    padding: 15px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    background: #f9fbff;
}
.card-title {
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
}
.card-value {
    font-size: 22px;
    font-weight: 700;
}
.card-note {
    font-size: 11px;
    color: #777;
    margin-top: 4px;
}
.card-blue { border-left: 4px solid #4a90e2; }
.card-green { border-left: 4px solid #2ecc71; }
.card-orange{ border-left: 4px solid #f1c40f; }

/* ปุ่มด้านบน */
.top-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 15px;
}
a.btn, button.btn {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 14px;
}
.btn-add-top { background:#3498db; color:#fff; }
.btn-dashboard { background:#9b59b6; color:#fff; }

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

@media (max-width: 768px) {
    .summary-box {
        grid-template-columns: 1fr;
    }
}

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
    <h2>📦 Smart Warehouse Dashboard</h2>

    <!-- กล่องข้อมูลผู้ใช้ -->
    <div class="user-box">
        <div class="user-info-wrap">
            <img src="<?= htmlspecialchars($user_photo); ?>" alt="User Photo">
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($user['username']); ?></div>
                <div class="small">
                    ID ผู้ใช้งาน : <?= $user['id']; ?> | สิทธิ์ : <?= htmlspecialchars($user_role); ?>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">ออกจากระบบ</a>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-box">
        <div class="card card-blue">
            <div class="card-title">สินค้ารวมทั้งหมด (ชิ้น)</div>
            <div class="card-value"><?= number_format($totalQty); ?></div>
            <div class="card-note">รวมทุก SKU ในคลัง</div>
        </div>

        <div class="card card-green">
            <div class="card-title">จำนวน SKU (รหัสสินค้าไม่ซ้ำ)</div>
            <div class="card-value"><?= number_format($totalSKU); ?></div>
            <div class="card-note">จำนวนรายการสินค้าที่แตกต่างกัน</div>
        </div>

        <div class="card card-orange">
            <div class="card-title">สินค้าใกล้หมดสต็อก</div>
            <div class="card-value"><?= number_format($lowStock); ?></div>
            <div class="card-note">quantity ≤ min_stock</div>
        </div>
    </div>

    <!-- ปุ่มด้านบน -->
    <div class="top-actions">
        <a href="add.php" class="btn btn-add-top">➕ เพิ่มสินค้าใหม่</a>
        <a href="dashboard.php" class="btn btn-dashboard">📊 รายงาน/กราฟ (แยกหน้า)</a>
    </div>

    <!-- ตารางสินค้าล่าสุด -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>จุดสั่งซื้อ (min)</th>
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
                <td data-label="min_stock"><?= $row['min_stock']; ?></td>
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
                    <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-delete"
                       onclick="return confirm('ต้องการลบสินค้านี้หรือไม่?')">ลบ</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="add.php" class="btn-add">➕ เพิ่มสินค้าใหม่</a>
</div>
</body>
</html>
