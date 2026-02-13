<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "inventory";   // ต้องเป็น inventory เท่านั้น

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
