<?php
include 'db.php';
session_start();

header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user'];

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

$stmt = $conn->prepare("
SELECT id, username, photo, role, password
FROM users
WHERE id = ?
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user = $res->fetch_assoc();
$user_role = $user['role'];

//////////////////////////////////////////////////
// FIX PATH ให้ตรงกันทั้งหมด
//////////////////////////////////////////////////
$user_photo = !empty($user['photo'])
    ? 'upload/' . htmlspecialchars($user['photo'])
    : 'upload/66209010011.png';

//////////////////////////////////////////////////
// DELETE ALL
//////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {

    if ($user_role !== 'admin') {
        die("Access denied");
    }

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        die("Invalid CSRF Token");
    }

    if (!password_verify($_POST['confirm_password'] ?? '', $user['password'])) {
        die("รหัสผ่านไม่ถูกต้อง");
    }

    if(!$conn->query("
        UPDATE products 
        SET deleted_at = NOW()
        WHERE deleted_at IS NULL
    ")){
        die("Database error: ".$conn->error);
    }

    $ip = getUserIP();
    $action = "DELETE ALL PRODUCTS";

    $stmt = $conn->prepare("
        INSERT INTO logs(user_id, action, ip)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iss", $user_id, $action, $ip);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

//////////////////////////////////////////////////
// LOAD PRODUCTS
//////////////////////////////////////////////////
$result = $conn->query("
SELECT * FROM products 
WHERE deleted_at IS NULL
ORDER BY id DESC
");

//////////////////////////////////////////////////
// SUMMARY (แก้ให้ใช้ตัวแปรตรงกับ HTML)
//////////////////////////////////////////////////
$summary = $conn->query("
SELECT 
IFNULL(SUM(quantity),0) AS qty,
COUNT(*) AS sku,
IFNULL(SUM(quantity*price),0) AS value
FROM products
WHERE deleted_at IS NULL
")->fetch_assoc();

$totalQty   = $summary['qty'];
$totalSKU   = $summary['sku'];
$totalValue = $summary['value'];
?>



<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Smart Warehouse GOD MODE</title>

<style>
body{font-family:Prompt,sans-serif;background:#f4f7fc;}
.container{max-width:1200px;margin:30px auto;background:#fff;padding:25px;border-radius:12px;box-shadow:0 5px 20px rgba(0,0,0,.08);text-align:center;}
.user-box{display:flex;justify-content:space-between;align-items:center;background:#eef2ff;padding:15px;border-radius:10px;margin-bottom:25px;}
.user-box img{width:60px;height:60px;border-radius:50%;}
.summary{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:25px;}
.card{padding:20px;border-radius:12px;background:#f9fbff;font-weight:600;}
.blue{border-left:6px solid #3498db;}
.green{border-left:6px solid #2ecc71;}
.orange{border-left:6px solid #f39c12;}
table{width:100%;border-collapse:collapse;margin-bottom:20px;}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:center;}
th{background:#3498db;color:white;}
.product-img{width:70px;border-radius:8px;}
.btn-edit{background:#3498db;padding:6px 12px;color:white;border-radius:6px;text-decoration:none;}
.btn-delete{background:#e74c3c;padding:6px 12px;color:white;border-radius:6px;text-decoration:none;}
.btn-add{display:block;margin-top:15px;background:#2ecc71;text-align:center;padding:12px;color:white;border-radius:8px;text-decoration:none;}
.bottom-actions{display:flex;gap:10px;margin-top:15px;align-items:center;}
.btn-dashboard{
background:linear-gradient(135deg,#667eea,#764ba2);
padding:10px 18px;
border-radius:8px;
color:white;
text-decoration:none;
font-weight:600;
}
.btn-dashboard:hover{transform:translateY(-2px);}
.btn-danger{
background:#c0392b;
padding:10px 14px;
border:none;
border-radius:8px;
color:white;
cursor:pointer;
}
.btn-danger:hover{background:#a93226;}
.password-input{
padding:8px;
border-radius:6px;
border:1px solid #ccc;
}
.btn-logout{background:#ff6666;padding:8px 14px;border-radius:6px;color:white;text-decoration:none;}
</style>
</head>

<body>

<div class="container">

<h2>📦 Smart Warehouse Dashboard</h2>

<div class="user-box">
<div style="display:flex;align-items:center;gap:10px;">
<img src="<?= $user_photo ?>">
<div>
<strong><?= htmlspecialchars($user['username']); ?></strong><br>
สิทธิ์: <?= htmlspecialchars($user_role); ?>
</div>
</div>
<a href="logout.php" class="btn-logout">ออกจากระบบ</a>
</div>

<div class="summary">
<div class="card blue">จำนวนสินค้า<br><?= number_format($totalQty) ?></div>
<div class="card green">จำนวน SKU<br><?= number_format($totalSKU) ?></div>
<div class="card orange">มูลค่าคลัง<br><?= number_format($totalValue,2) ?> บาท</div>
</div>

<table>
<thead>
<tr>
<th>ID</th>
<th>ชื่อ</th>
<th>รายละเอียด</th>
<th>จำนวน</th>
<th>ราคา</th>
<th>ภาพ</th>
<th>วันที่</th>
<th>จัดการ</th>
</tr>
</thead>

<tbody>
<?php if($result && $result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['description']) ?></td>
<td><?= $row['quantity'] ?></td>
<td><?= number_format($row['price'],2) ?></td>
<td>
<?php if(!empty($row['image'])): ?>
<img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="product-img">
<?php else: ?>-<?php endif; ?>
</td>
<td><?= $row['created_at'] ?></td>
<td>
<a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">แก้ไข</a>
<a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete"
onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="8">ไม่มีข้อมูลสินค้า</td></tr>
<?php endif; ?>
</tbody>
</table>

<a href="add.php" class="btn-add">➕ เพิ่มสินค้าใหม่</a>

<div class="bottom-actions">

<a href="dashboard.php" class="btn-dashboard">
📊 Dashboard
</a>

<?php if($user_role === 'admin'): ?>
<form method="POST" style="display:flex;gap:8px;">
<input type="hidden" name="csrf" value="<?= $_SESSION['csrf']; ?>">

<input type="password"
name="confirm_password"
required
placeholder="ยืนยันรหัสผ่าน"
class="password-input">

<button type="submit"
name="delete_all"
class="btn-danger">
ลบทั้งหมด
</button>

</form>
<?php endif; ?>

</div>


</div>
</body>
</html>
