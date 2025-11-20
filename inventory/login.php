<?php
include 'db.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']); // ไม่ md5 แล้ว

    // ดึงข้อมูลผู้ใช้จาก username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // ตรวจสอบรหัสผ่านด้วย password_verify
        if (password_verify($p, $row['password'])) {

            // เก็บ id user แทน username เพื่อใช้ join กับข้อมูลอื่นได้ง่าย
            $_SESSION['user'] = $row['id'];

            header("Location: index.php");
            exit;
        } else {
            $error = "❌ รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "❌ ไม่พบชื่อผู้ใช้ในระบบ";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบ</title>
  <style>
    /* พื้นหลังและจัดกึ่งกลาง */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* กล่องฟอร์ม */
    .login-box {
      background: #fff;
      padding: 40px 40px 25px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      width: 320px;
      text-align: center;
    }

    h2 {
      margin-bottom: 25px;
      color: #333;
    }

    label {
      display: block;
      text-align: left;
      margin-bottom: 5px;
      font-weight: bold;
      font-size: 14px;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      transition: 0.3s;
      font-size: 14px;
    }

    input[type="text"]:focus, input[type="password"]:focus {
      border-color: #2575fc;
      outline: none;
      box-shadow: 0 0 0 2px rgba(37,117,252,0.2);
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #2575fc;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 5px;
    }

    button:hover {
      background: #6a11cb;
    }

    .error {
      color: #e74c3c;
      margin-top: 15px;
      font-weight: bold;
      font-size: 14px;
    }

    .helper-area {
      margin-top: 15px;
      font-size: 13px;
      color: #777;
    }

    .helper-area a {
      color: #2575fc;
      text-decoration: none;
    }

    .helper-area a:hover {
      text-decoration: underline;
    }

    .copyright {
      margin-top: 5px;
      font-size: 11px;
      color: #aaa;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔑 เข้าสู่ระบบคลังสินค้า 🔑</h2>

    <form method="POST">
      <label>ชื่อผู้ใช้</label>
      <input type="text" name="username" required>

      <label>รหัสผ่าน</label>
      <input type="password" name="password" required>

      <button type="submit" name="login">เข้าสู่ระบบ</button>
    </form>

    <div class="helper-area">
      <div>
        <a href="forgot_password.php">ลืมรหัสผ่าน ?</a>
      </div>
      <div class="copyright">
        © 2025 ระบบคลังสินค้าอัจฉริยะ
      </div>
    </div>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
