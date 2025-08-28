<?php
include 'db.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php"); // ถ้า login แล้วไปหน้า index.php
    exit;
}

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = md5($_POST['password']);
    $result = $conn->query("SELECT * FROM users WHERE username='$u' AND password='$p'");
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $u;
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
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
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      width: 320px;
      text-align: center;
    }

    h2 {
      margin-bottom: 30px;
      color: #333;
    }

    label {
      display: block;
      text-align: left;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      transition: 0.3s;
    }

    input[type="text"]:focus, input[type="password"]:focus {
      border-color: #2575fc;
      outline: none;
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
    }

    button:hover {
      background: #6a11cb;
    }

    .error {
      color: red;
      margin-top: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔑 เข้าสู่ระบบ</h2>
    <form method="POST">
      <label>ชื่อผู้ใช้</label>
      <input type="text" name="username" required>
      <label>รหัสผ่าน</label>
      <input type="password" name="password" required>
      <button type="submit" name="login">เข้าสู่ระบบ</button>
    </form>

    <?php
    if (isset($error)) {
        echo "<p class='error'>{$error}</p>";
    }
    ?>
  </div>
</body>
</html>
