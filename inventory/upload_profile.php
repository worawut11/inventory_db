<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user'];

if ($_FILES['photo']['name']) {

    $target_dir = "upload/";
    $file_name = $user_id . "_" . time() . ".jpg";
    $target_file = $target_dir . $file_name;

    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

    $sql = "UPDATE users SET photo='$target_file' WHERE id=$user_id";
    $conn->query($sql);

    header("Location: index.php");
}
?>

<form action="upload_profile.php" method="post" enctype="multipart/form-data">
    <label>เลือกรูปโปรไฟล์:</label>
    <input type="file" name="photo" required>
    <button type="submit">อัปโหลด</button>
</form>
