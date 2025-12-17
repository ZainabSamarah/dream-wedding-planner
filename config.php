<?php
// config.php - ملف الاتصال بقاعدة البيانات المركزي

session_start(); // لازم في كل صفحة تستخدم session

$host = 'localhost';
$db = 'wedding_db';          // اسم الداتابيز
$user = 'root';
$pass = '123456';            // الباسوورد الصحيح
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // اختياري: رسالة نجاح للتطوير فقط (احذفها لاحقاً)
    // echo "✓ تم الاتصال بقاعدة البيانات بنجاح!";
} catch (PDOException $e) {
    die("❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>