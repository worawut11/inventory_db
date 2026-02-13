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

// ตรวจสอบไฟล์อัปโหลด
if(isset($_FILES['file'])){
    $file = $_FILES['file'];
    if($file['error'] === 0){
        // สร้างโฟลเดอร์ uploads ถ้าไม่มี
        if(!is_dir('uploads')){
            mkdir('uploads', 0777, true);
        }

        $fileName = 'uploads/' . time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $fileName);

        // ถ้ามีข้อมูลอื่น ๆ เช่น product_code หรือ name
        $product_code = $_POST['product_code'] ?? 'unknown';
        $name = $_POST['name'] ?? 'unknown';

        $stmt = $conn->prepare("INSERT INTO products (product_code, name, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $product_code, $name, $fileName);
        if($stmt->execute()){
            echo "Upload success";
        } else {
            echo "DB insert error";
        }
        $stmt->close();
    } else {
        echo "File upload error: " . $file['error'];
    }
} else {
    echo "No file received";
}

$conn->close();
?>
