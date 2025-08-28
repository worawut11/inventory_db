<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_db";

// เชื่อมต่อ Database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูล Base64 จาก ESP32
if(isset($_POST['image']) && isset($_POST['product_code']) && isset($_POST['name'])){
    $image = $_POST['image'];
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];

    // แปลง Base64 เป็นไฟล์รูป
    $imageData = base64_decode($image);
    $fileName = 'uploads/' . time() . '_' . $product_code . '.jpg';
    
    if(!is_dir('uploads')){
        mkdir('uploads', 0777, true);
    }

    file_put_contents($fileName, $imageData);

    // เก็บข้อมูลลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO products (product_code, name, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $product_code, $name, $fileName);
    if($stmt->execute()){
        echo "Upload success";
    } else {
        echo "DB insert error";
    }
    $stmt->close();
} else {
    echo "No image received";
}

$conn->close();
?>
