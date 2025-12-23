<?php
session_start();
require_once 'config.php'; // Your database connection file with $pdo

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        try {
            // Get the owner ID
            $stmt = $pdo->query("SELECT id FROM users WHERE role = 'owner' LIMIT 1");
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$owner) {
$error = 'No owner account found in the system.';
} else {
$from_user_id = $_SESSION['user_id'] ?? null; // If logged in user

$sql = "INSERT INTO messages
(from_user_id, to_user_id, content, guest_name, guest_email, is_read, sent_at)
VALUES (?, ?, ?, ?, ?, 0, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
$from_user_id,
$owner['id'],
$message,
$from_user_id ? null : $name,
$from_user_id ? null : $email
]);

$success = 'Your message has been sent successfully! We will get back to you soon.';
// Clear fields after success
$_POST = [];
}
} catch (Exception $e) {
$error = 'An error occurred while sending the message. Please try again.';
}
}
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEDÉ - Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--green-pale); color: var(--green-dark); }
        header {
            background-color: var(--green-medium);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            height: 70px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        header h1 { font-family: 'Great Vibes', cursive; font-size: 36px; color: var(--green-pale); }
        nav a { color: white; text-decoration: none; margin-left: 25px; font-weight: 500; transition: 0.3s; }
        nav a:hover { opacity: 0.8; }
        .nav-btn {
            padding: 10px 20px;
            background-color: var(--green-light);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin-left: 25px;
            transition: 0.3s;
        }
        .nav-btn:hover { background-color: var(--green-medium); transform: translateY(-2px); }
        .contact-section { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 120px 20px 80px; }
        .contact-box {
            background-color: white;
            padding: 50px;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
        }
        .contact-box h2 { font-family: 'Great Vibes', cursive; font-size: 48px; margin-bottom: 15px; color: var(--green-dark); }
        .contact-box p { font-size: 18px; margin-bottom: 40px; color: #555; }
        form { display: flex; flex-direction: column; gap: 20px; }
        input, textarea {
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--green-light);
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus { outline: none; border-color: var(--green-medium); }
        textarea { min-height: 150px; resize: vertical; }
        button {
            background-color: var(--green-light);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            align-self: center;
            width: 200px;
        }
        button:hover { background-color: var(--green-medium); transform: translateY(-2px); }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; }
        .success { background: #e8f5e9; color: #2e7d32; }
        .error { background: #ffebee; color: #d32f2f; }
        footer { background-color: var(--green-medium); color: white; text-align: center; padding: 40px 20px; }
        .footer-content { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 30px; }
        .footer-column h3 { font-size: 18px; margin-bottom: 15px; color: var(--green-pale); }
        .footer-column a { display: block; color: white; text-decoration: none; margin-bottom: 10px; font-size: 14px; transition: color 0.3s; }
        .footer-column a:hover { color: var(--green-pale); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px; margin-top: 30px; }
        @media (max-width: 768px) {
            .contact-box { padding: 30px; }
            .contact-box h2 { font-size: 38px; }
        }
    </style>
</head>
<body>

<header>
    <h1>WEDÉ</h1>
    <nav>
        <a href="#features">Features</a>
        <a href="#tools">Tools</a>
        <a href="services.html">Services</a>
        <a href="gallery.html">Gallery</a>
        <a href="contact.php">Contact</a>
        <button id="loginBtn" class="nav-btn">Login</button>
    </nav>
</header>

<section class="contact-section">
    <div class="contact-box">
        <h2>Contact Us</h2>
        <p>We'd love to hear from you — let's plan something beautiful together!</p>

        <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="name" placeholder="Your Name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            <input type="email" name="email" placeholder="Your Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <textarea name="message" placeholder="Your Message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <div class="footer-content">
        <div class="footer-column">
            <h3>Plan</h3>
            <a href="services.html">All Services</a>
            <a href="rsvp.html">RSVP Manager</a>
            <a href="budget.html">Budget Tracker</a>
            <a href="gallery.html">Gallery</a>
        </div>
        <div class="footer-column">
            <h3>Discover</h3>
            <a href="services.html">Wedding Vendors</a>
            <a href="gallery.html">Photo Gallery</a>
            <a href="services.html">Planning Tips</a>
        </div>
        <div class="footer-column">
            <h3>Company</h3>
            <a href="contact.php">Contact Us</a>
            <a href="#">About WEDÉ</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
        <div class="footer-column">
            <h3>Follow Us</h3>
            <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
            <a href="#"><i class="fab fa-pinterest"></i> Pinterest</a>
        </div>
    </div>
    <div class="footer-bottom">
        © 2025 <span style="color: var(--green-pale);">WEDÉ</span> | All rights reserved
    </div>
</footer>

<script>
    document.getElementById('loginBtn').addEventListener('click', () => {
        window.location.href = 'login.php';
    });
</script>
</body>
</html>