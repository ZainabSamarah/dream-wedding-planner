<?php
global $pdo;
require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: main.html");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Dream Wedding Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #D0DDD0;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            width: 350px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        h2 {
            font-family: "Great Vibes", cursive;
            font-size: 32px;
            color: #66785F;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }
        button {
            background-color: #66785F;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            transition: 0.3s;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #4B5945;
        }
        .link {
            color: #66785F;
            text-decoration: none;
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }
        .link:hover {
            text-decoration: underline;
        }
        .signup-text {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }
        .signup-text a {
            color: #66785F;
            text-decoration: none;
            font-weight: 500;
        }
        .signup-text a:hover {
            text-decoration: underline;
        }
        .error {
            color: #d32f2f;
            margin-top: 10px;
            font-size: 14px;
            padding: 10px;
            background: #ffebee;
            border-radius: 5px;
            border-left: 4px solid #d32f2f;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>

    <?php if (!empty($error)) { echo '<div class="error">' . htmlspecialchars($error) . '</div>'; } ?>

    <div class="signup-text">
        Don't have an account? <a href="signup.php">Sign up</a>
    </div>
    <a href="main.html" class="link">← Back to Home</a>
</div>
</body>
</html>
