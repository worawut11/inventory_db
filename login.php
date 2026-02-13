<?php
include 'db.php';
session_start();

//////////////////////////////////////////////////
// 🔥 SECURITY HEADERS
//////////////////////////////////////////////////
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

//////////////////////////////////////////////////
// ถ้า login อยู่แล้ว → เข้า index
//////////////////////////////////////////////////
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

//////////////////////////////////////////////////
// FUNCTION: GET USER IP
//////////////////////////////////////////////////
function getUserIP() {

    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

//////////////////////////////////////////////////
// LOGIN
//////////////////////////////////////////////////
if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // กัน bot brute force แบบง่าย
    sleep(1);

    //////////////////////////////////////////////////
    // SELECT เฉพาะ field (เร็ว + ปลอดภัยกว่า)
    //////////////////////////////////////////////////
    $stmt = $conn->prepare("
        SELECT id, password 
        FROM users 
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $login_success = false;
    $user_id = null;

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            //////////////////////////////////////////////////
            // 🔥 ป้องกัน Session Fixation
            //////////////////////////////////////////////////
            session_regenerate_id(true);

            $_SESSION['user'] = $row['id'];

            $login_success = true;
            $user_id = $row['id'];
        }
    }

    //////////////////////////////////////////////////
    // 🔥 LOGIN LOG
    //////////////////////////////////////////////////
    $ip = getUserIP();
    $action = $login_success ? "LOGIN SUCCESS" : "LOGIN FAILED";

    $stmt = $conn->prepare("
        INSERT INTO logs(user_id, action, ip)
        VALUES (?, ?, ?)
    ");

    // ถ้า login fail ไม่มี user → ใส่ 0
    $uid = $user_id ?? 0;

    $stmt->bind_param("iss", $uid, $action, $ip);
    $stmt->execute();

    //////////////////////////////////////////////////
    // RESULT
    //////////////////////////////////////////////////
    if ($login_success) {

        header("Location: index.php");
        exit;

    } else {

        // ไม่บอกว่า user หรือ pass ผิด
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
body{
font-family:'Segoe UI',sans-serif;
background:linear-gradient(135deg,#6a11cb,#2575fc);
height:100vh;
display:flex;
justify-content:center;
align-items:center;
margin:0;
}

.login-box{
background:#fff;
padding:40px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,.3);
width:320px;
text-align:center;
}

input{
width:100%;
padding:10px;
margin-bottom:15px;
border-radius:8px;
border:1px solid #ccc;
}

input:focus{
border-color:#2575fc;
outline:none;
box-shadow:0 0 0 2px rgba(37,117,252,0.2);
}

button{
width:100%;
padding:12px;
border:none;
border-radius:8px;
background:#003cff;
color:white;
font-size:16px;
cursor:pointer;
}

button:hover{
background:#6a11cb;
}

.error{
color:#e74c3c;
font-weight:bold;
margin-top:10px;
}
</style>
</head>

<body>

<div class="login-box">

<h2>🔐 เข้าสู่ระบบคลังสินค้า</h2>

<form method="POST">

<input type="text"
name="username"
placeholder="ชื่อผู้ใช้"
required>

<input type="password"
name="password"
placeholder="รหัสผ่าน"
required>

<button name="login">เข้าสู่ระบบ</button>

</form>

<?php if(isset($error)): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

</div>
</body>
</html>
