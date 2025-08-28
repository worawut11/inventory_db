<?php
$target_dir = "uploads/";

// ✅ สร้างโฟลเดอร์ถ้ายังไม่มี
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo "<pre>";
    print_r($_FILES); // Debug ดูว่าไฟล์ส่งมาหรือไม่
    echo "</pre>";

    if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) {

        $target_file = $target_dir . uniqid() . ".jpg";

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Upload Success: " . basename($target_file);

            // ✅ เชื่อมต่อ MySQL
            $servername = "localhost";
            $username   = "root";   // ค่า default ของ XAMPP
            $password   = "";
            $dbname     = "inventory_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // ✅ ข้อมูลตัวอย่าง
            $product_code = "AUTO_" . uniqid();
            $name = "Captured Product";

            // ✅ บันทึกลงฐานข้อมูล
            $sql = "INSERT INTO products (product_code, name, image, quantity) 
                    VALUES ('$product_code', '$name', '$target_file', 1)";

            if ($conn->query($sql) === TRUE) {
                echo " | Saved to DB";
            } else {
                echo " | Error: " . $conn->error;
            }

            $conn->close();

        } else {
            echo "Upload Failed";
        }

    } else {
        echo "No file received (check input name='file')";
    }

} else {
    echo "Not a POST request";
}
?>
