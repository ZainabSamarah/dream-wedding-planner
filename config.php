<?php
// config.php - ملف الاتصال بقاعدة البيانات

session_start(); // لازم في كل صفحة تستخدم session

$host = 'localhost';
$db = 'wedding_db_local';  // صحيح حسب الصورة$user = 'root';
$pass = '';                  // فارغ في XAMPP الافتراضي
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // يظهر الأخطاء بوضوح أثناء التطوير
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,                   // أمان أعلى
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "تم الاتصال بنجاح!"; // اختبار (احذفه بعدين)
} catch (PDOException $e) {
    // في التطوير: اظهر الخطأ كامل
    // في الإنتاج: سجل الخطأ واظهر رسالة عامة للمستخدم
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>