<?php
session_start();

// إعدادات الاتصال
$host = '127.0.0.1';
$dbname = 'wedding_db';
$username = 'root';
$password = '';                    // فارغ لـ XAMPP، غيريه لو في باسوورد
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("rror connecting to DB " . $e->getMessage());
}



$user_id = $_SESSION['user_id'] ?? null;

// Define Base URL for public access
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://hacking-nonbotanically-ivana.ngrok-free.dev/dream-wedding-planner1');
}
?>