<?php
$servername = "localhost";
$username = "root";
$password = "123456";        // الكلمة الجديدة
$dbname = "wedding_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>