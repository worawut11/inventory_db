<?php
// โฟลเดอร์เก็บไฟล์
$target_dir = "uploads/";

// ✅ สร้างโฟลเดอร์ถ้ายังไม่มี
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        die("ไม่สามารถสร้างโฟลเดอร์ uploads/");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Debug: ตรวจสอบไฟล์ที่ส่งมา
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";

    if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) {

        // ตรวจสอบว่าเป็นไฟล์รูป JPEG
        $fileType = mime_content_type($_FILES["file"]["tmp_name"]);
        if ($fileType !== 'image/jpeg' && $fileType !== 'image/jpg') {
            die("❌ ไฟล์ต้องเป็น JPEG เท่านั้น");
        }

        // สร้างชื่อไฟล์ใหม่ไม่ซ้ำ
        $target_file = $target_dir . uniqid("img_") . ".jpg";

        // ย้ายไฟล์จาก tmp → โฟลเดอร์ปลายทาง
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "✅ Upload Success: " . basename($target_file);

            // ✅ เชื่อมต่อ MySQL
            $conn = new mysqli("localhost", "root", "", "inventory_db");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // ข้อมูลตัวอย่างสำหรับบันทึก
            $product_code = "AUTO_" . uniqid();
            $name = "Captured Product";

            // บันทึกลงฐานข้อมูล
            $stmt = $conn->prepare("INSERT INTO products (product_code, name, image, quantity) VALUES (?, ?, ?, ?)");
            $quantity = 1;
            $stmt->bind_param("sssi", $product_code, $name, $target_file, $quantity);

            if ($stmt->execute()) {
                echo " | Saved to DB";
            } else {
                echo " | DB Error: " . $stmt->error;
            }

            $stmt->close();
            $conn->close();

        } else {
            echo "❌ Upload Failed: ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง";
        }

    } else {
        echo "❌ No file received (ตรวจสอบ input name='file')";
    }

} else {
    echo "❌ Not a POST request";
}
?>
