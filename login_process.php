<?php
// login_process.php - معالجة تسجيل الدخول

global $conn;
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    $errors = [];

    // التحقق من البيانات
    if (empty($email) || empty($password)) {
        $errors[] = "جميع الحقول مطلوبة";
    }

    if (empty($errors)) {

        // البحث عن المستخدم
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // التحقق من كلمة المرور
            if (password_verify($password, $user['password'])) {

                // حفظ بيانات الجلسة
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                echo json_encode([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح',
                    'redirect' => 'main.html'
                ]);

            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
                ]);
            }

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
    }
}

$conn->close();
?>