<?php
// config.php - ملف الاتصال بقاعدة البيانات

// إعدادات الاتصال
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // اسم المستخدم (غالباً root)
define('DB_PASS', '');               // كلمة السر (فاضية في XAMPP)
define('DB_NAME', 'wedding_db');

// إنشاء الاتصال
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // التحقق من الاتصال
    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    // تعيين الترميز
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("خطأ: " . $e->getMessage());
}

// دالة لتنظيف المدخلات
function cleanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// بدء الجلسة
session_start();
?>