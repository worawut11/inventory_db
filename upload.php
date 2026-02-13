<?php
include 'db.php';

// โฟลเดอร์เก็บไฟล์
$target_dir = "uploads/";

// สร้างโฟลเดอร์ถ้ายังไม่มี
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        die("❌ ไม่สามารถสร้างโฟลเดอร์ uploads/");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) {

        // ตรวจสอบชนิดไฟล์
        $fileType = mime_content_type($_FILES["file"]["tmp_name"]);
        if (!in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png'])) {
            die("❌ รองรับเฉพาะ JPG / PNG เท่านั้น");
        }

        // สร้างชื่อไฟล์ใหม่
        $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $imageName = uniqid("img_") . "." . $ext;
        $target_file = $target_dir . $imageName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

            // ข้อมูลสินค้าอัตโนมัติ
            $name        = "Captured Product";
            $description = "Uploaded from ESP32-CAM";
            $quantity    = 1;
            $price       = 0;

            // บันทึกลงฐานข้อมูล
            $stmt = $conn->prepare("
                INSERT INTO products (name, description, quantity, price, image)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssids", $name, $description, $quantity, $price, $imageName);

            if ($stmt->execute()) {
                echo "✅ Upload + Save DB Success: " . $imageName;
            } else {
                echo "❌ DB Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "❌ Upload Failed";
        }
    } else {
        echo "❌ No file received";
    }
} else {
    echo "❌ Not POST request";
}
