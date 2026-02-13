<?php
include 'db.php';
session_start();

$msg = "";

if (isset($_POST['reset'])) {
    $username    = trim($_POST['username']);
    $new_pass    = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);

    if ($new_pass === "" || $confirm_pass === "" || $username === "") {
        $msg = "⚠️ กรุณากรอกข้อมูลให้ครบ";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "❌ รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน";
    } else {
        // hash รหัสใหม่
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        // อัปเดตรหัสผ่านใน DB ด้วย username
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $msg = "✅ เปลี่ยนรหัสผ่านสำเร็จแล้ว สามารถเข้าสู่ระบบด้วยรหัสใหม่ได้";
        } else {
            $msg = "❌ ไม่พบชื่อผู้ใช้นี้ หรือไม่มีการเปลี่ยนแปลงข้อมูล";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ลืมรหัสผ่าน</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        background: linear-gradient(135deg, #007bff, #00bcd4);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: "Prompt", sans-serif;
    }
    .box {
        background: #fff;
        padding: 30px 25px;
        border-radius: 15px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
    }
    .mb-3 { margin-bottom: 15px; }
    label { font-weight: 500; }
    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    button {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: none;
        background: #007bff;
        color: #fff;
        font-size: 15px;
        cursor: pointer;
    }
    .back-link {
        margin-top: 10px;
        text-align: center;
    }
    .message {
        margin-bottom: 10px;
        font-size: 14px;
        text-align: center;
    }
</style>
</head>
<body>
<div class="box">
    <h2>ลืมรหัสผ่าน</h2>

    <?php if($msg != ""): ?>
        <div class="message"><?= $msg; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>ชื่อผู้ใช้</label>
            <input type="text" name="username" required>
        </div>
        <div class="mb-3">
            <label>รหัสผ่านใหม่</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label>ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" name="reset">เปลี่ยนรหัสผ่าน</button>
    </form>

    <div class="back-link">
        <a href="login.php">⬅ กลับไปหน้าเข้าสู่ระบบ</a>
    </div>
</div>
</body>
</html>