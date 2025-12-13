<?php
// get_user_data.php - جلب بيانات المستخدم

global $conn;
require_once 'config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'يرجى تسجيل الدخول أولاً',
        'redirect' => 'login.html'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// جلب بيانات المستخدم
$stmt = $conn->prepare("SELECT first_name, last_name, email, age, phone, location, wedding_date, guest_count, theme, budget, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'المستخدم غير موجود'
    ]);
}

$stmt->close();
$conn->close();
?>