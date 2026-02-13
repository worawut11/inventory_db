<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user'];

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

  $target_dir = "upload/";

  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
  $file_name = $user_id . "_" . time() . "." . $ext;
  $target_file = $target_dir . $file_name;

  if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {

    $stmt = $conn->prepare("UPDATE users SET photo=? WHERE id=?");
    $stmt->bind_param("si", $target_file, $user_id);
    $stmt->execute();

    header("Location: index.php");
    exit;
  } else {
    echo "❌ Upload failed";
  }
}
?>

<form action="upload_profile.php" method="post" enctype="multipart/form-data">
  <label>เลือกรูปโปรไฟล์:</label>
  <input type="file" name="photo" accept="image/*" required>
  <button type="submit">อัปโหลด</button>
</form>


<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>อัปโหลดรูปโปรไฟล์</title>
  <style>
    body {
      font-family: Arial;
      background: #f5f7fb;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .box {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, .15);
      width: 300px;
      text-align: center;
    }

    input,
    button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 8px;
    }

    button {
      background: #3498db;
      color: white;
      border: none;
      cursor: pointer;
    }

    .msg {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

  <div class="box">
    <h3>📷 เปลี่ยนรูปโปรไฟล์</h3>

    <?php if ($msg != ""): ?>
      <div class="msg"><?= $msg; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="file" name="photo" accept="image/*" required>
      <button type="submit">อัปโหลด</button>
    </form>

    <br>
    <a href="index.php">⬅ กลับหน้าหลัก</a>
  </div>

</body>

</html>