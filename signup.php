<?php
global $pdo;
require_once 'config.php';

$error = '';
$success = '';
$firstName = '';
$lastName = '';
$age = '';
$email = '';
$role = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = $_POST['role'] ?? '';

    $isValid = true;

    if (empty($firstName)) {
        $errors['firstName'] = 'First Name is required.';
        $isValid = false;
    }

    if (empty($lastName)) {
        $errors['lastName'] = 'Last Name is required.';
        $isValid = false;
    }

    if (empty($age) || !is_numeric($age) || $age < 18) {
        $errors['age'] = 'Age must be 18 or older.';
        $isValid = false;
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
        $isValid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
        $isValid = false;
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
        $isValid = false;
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
        $isValid = false;
    }

    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match.';
        $isValid = false;
    }

    if (empty($role) || !in_array($role, ['user', 'owner'])) {
        $errors['role'] = 'Please select a role.';
        $isValid = false;
    }

    if ($isValid) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $errors['email'] = 'This email is already registered.';
                $isValid = false;
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, age, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $age, $email, $hashedPassword, $role]);

                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                $_SESSION['role'] = $role;

                $success = 'Account created successfully! Welcome ' . htmlspecialchars($firstName);
                $firstName = '';
                $lastName = '';
                $age = '';
                $email = '';
                $role = '';
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up – Dream Wedding Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #D0DDD0;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .role-btn.active {
            background-color: #66785F;
            color: white;
            border-color: #66785F;
        }
        .signup-box {
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
        }
        .error {
            color: #d32f2f;
            font-size: 0.9rem;
            display: block;
            margin-top: 5px;
            text-align: left;
        }
        .success {
            color: #2e7d32;
            font-size: 0.9rem;
            display: block;
            margin-top: 5px;
            text-align: left;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #2e7d32;
        }
        .role-choice {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .role-btn {
            background: transparent;
            border: 1px solid #66785F;
            color: #66785F;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .role-btn:hover {
            background: #66785F;
            color: white;
        }
        button[type="submit"] {
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
        }
        button[type="submit"]:hover { background-color: #4B5945; }
        .link {
            color: #66785F;
            text-decoration: none;
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }
        .link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="signup-box">
    <h2>Sign Up</h2>
    <form method="post" id="signupForm">
        <input type="text" name="firstName" id="firstName" placeholder="First Name" value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required>
        <span id="firstNameError" class="error"><?php echo isset($errors['firstName']) ? $errors['firstName'] : ''; ?></span>

        <input type="text" name="lastName" id="lastName" placeholder="Last Name" value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>
        <span id="lastNameError" class="error"><?php echo isset($errors['lastName']) ? $errors['lastName'] : ''; ?></span>

        <input type="number" name="age" id="age" placeholder="Age" value="<?php echo isset($age) ? htmlspecialchars($age) : ''; ?>" required>
        <span id="ageError" class="error"><?php echo isset($errors['age']) ? $errors['age'] : ''; ?></span>

        <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        <span id="emailError" class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>

        <input type="password" name="password" id="password" placeholder="Password" required>
        <span id="passwordError" class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>

        <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
        <span id="confirmPasswordError" class="error"><?php echo isset($errors['confirmPassword']) ? $errors['confirmPassword'] : ''; ?></span>

        <div class="role-choice">
            <button type="button" class="role-btn <?php echo (isset($role) && $role === 'user') ? 'active' : ''; ?>" onclick="selectRole('user')">User</button>
            <button type="button" class="role-btn <?php echo (isset($role) && $role === 'owner') ? 'active' : ''; ?>" onclick="selectRole('owner')">Owner</button>
        </div>
        <input type="hidden" name="role" id="role" value="<?php echo isset($role) ? htmlspecialchars($role) : ''; ?>">
        <span id="roleError" class="error"><?php echo isset($errors['role']) ? $errors['role'] : ''; ?></span>

        <button type="submit">Create Account</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="error" style="margin-top:15px; padding: 10px; background: #ffebee; border-radius: 5px; border-left: 4px solid #d32f2f;"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success" style="margin-top:15px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <a href="login.php" class="link">← Back to Login</a>
</div>

<script>
    let selectedRole = '<?php echo isset($role) ? addslashes($role) : ''; ?>';

    function selectRole(role) {
        selectedRole = role;
        document.getElementById('role').value = role;
        document.getElementById('roleError').textContent = '';

        const buttons = document.querySelectorAll('.role-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        if (role === 'user') buttons[0].classList.add('active');
        else if (role === 'owner') buttons[1].classList.add('active');
    }

    const inputs = ['firstName', 'lastName', 'age', 'email', 'password', 'confirmPassword'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', function() {
            document.getElementById(id + 'Error').textContent = '';
        });
    });

    if (selectedRole) selectRole(selectedRole);
</script>
</body>
</html>
