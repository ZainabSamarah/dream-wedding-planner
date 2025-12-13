<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = cleanInput($_POST['firstName']);
    $lastName = cleanInput($_POST['lastName']);
    $age = (int)$_POST['age'];
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = cleanInput($_POST['role']);

    $errors = [];

    if (empty($firstName)) {
        $errors[] = "First name is required";
    }

    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }

    if ($age < 18) {
        $errors[] = "Age must be 18 or older";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // ← شلنا الشرط هاد:
    // if (!preg_match('/@gmail\.com$/i', $email)) {
    //     $errors[] = "Gmail only";
    // }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    if (!in_array($role, ['user', 'owner'])) {
        $errors[] = "Please select a role";
    }

    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $errors[] = "Email already registered";
    }
    $checkEmail->close();

    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, age, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $firstName, $lastName, $email, $hashedPassword, $age, $role);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully!',
                'redirect' => 'main.html'
            ]);

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error occurred, please try again'
            ]);
        }

    } else {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

$conn->close();
?>