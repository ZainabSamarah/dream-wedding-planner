<?php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – Dream Wedding Planner</title>
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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: var(--green-dark);
            padding-top: 70px;
        }

        /* HEADER - SAME AS MAIN */
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
        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 36px;
            color: var(--green-pale);
        }
        header h1 a {
            color: var(--green-pale);
            text-decoration: none;
        }
        header nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        header nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        header nav a:hover { opacity: 0.8; }
        .nav-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background: var(--green-light);
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-btn:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }

        /* MAIN CONTENT */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* PROFILE HERO */
        .profile-hero {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-light), var(--green-pale));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 50px;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .profile-hero h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: var(--green-dark);
            margin-bottom: 10px;
        }

        .profile-hero p {
            color: var(--green-medium);
            font-size: 16px;
            margin-bottom: 5px;
        }

        .profile-role-badge {
            display: inline-block;
            background: var(--green-light);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 15px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #eee;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--green-light);
        }

        .stat-label {
            font-size: 12px;
            color: var(--green-medium);
            text-transform: uppercase;
            margin-top: 5px;
        }

        /* SECTIONS */
        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section h2 {
            font-size: 20px;
            color: var(--green-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section h2 i {
            color: var(--green-light);
            font-size: 24px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--green-medium);
            font-weight: 500;
        }

        .info-value {
            color: var(--green-dark);
            font-weight: 600;
        }

        .btn {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--green-light);
            color: white;
        }

        .btn-primary:hover {
            background: var(--green-medium);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* FOOTER - SAME AS MAIN */
        footer {
            background-color: var(--green-medium);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--green-pale);
        }
        .footer-column a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .footer-column a:hover {
            color: var(--green-pale);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 20px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .profile-hero { padding: 40px 20px; }
            .sections-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- HEADER - SAME AS MAIN -->
<header>
    <h1><a href="main.html">WEDÉ</a></h1>
    <nav>
        <a href="main.html">Features</a>
        <a href="main.html">Tools</a>
        <a href="services.html">Services</a>
        <a href="gallery.html">Gallery</a>
        <a href="contact.html">Contact</a>
        <button class="nav-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </button>
    </nav>
</header>

<div class="container">
    <!-- PROFILE HERO -->
    <div class="profile-hero">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <h1><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h1>
        <p><?php echo htmlspecialchars($email); ?></p>
        <span class="profile-role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>

        <div class="stats-row">
            <div class="stat">
                <div class="stat-number">0</div>
                <div class="stat-label">Bookings</div>
            </div>
            <div class="stat">
                <div class="stat-number">0</div>
                <div class="stat-label">Upcoming</div>
            </div>
            <div class="stat">
                <div class="stat-number">0</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- SECTIONS -->
    <div class="sections-grid">
        <!-- PERSONAL INFO -->
        <div class="section">
            <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-value"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Phone</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Location</span>
                <span class="info-value">Not set</span>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
        </div>

        <!-- WEDDING DETAILS -->
        <div class="section">
            <h2><i class="fas fa-ring"></i> Wedding Details</h2>
            <div class="info-item">
                <span class="info-label">Wedding Date</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Guest Count</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Theme</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Budget</span>
                <span class="info-value">Not set</span>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-edit"></i> Update Details
            </button>
        </div>

        <!-- PREFERENCES -->
        <div class="section">
            <h2><i class="fas fa-heart"></i> Preferences</h2>
            <div class="info-item">
                <span class="info-label">Colors</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Flowers</span>
                <span class="info-value">Not set</span>
            </div>
            <div class="info-item">
                <span class="info-label">Saved Ideas</span>
                <span class="info-value">0 items</span>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-eye"></i> View All
            </button>
        </div>

        <!-- SETTINGS -->
        <div class="section">
            <h2><i class="fas fa-cog"></i> Settings</h2>
            <div class="info-item">
                <span class="info-label">Notifications</span>
                <span class="info-value">Enabled</span>
            </div>
            <div class="info-item">
                <span class="info-label">Password</span>
                <span class="info-value">••••••••</span>
            </div>
            <div class="info-item">
                <span class="info-label">Language</span>
                <span class="info-value">English</span>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-key"></i> Change Password
            </button>
            <button class="btn btn-danger" onclick="deleteAccount()">
                <i class="fas fa-trash"></i> Delete Account
            </button>
        </div>

        <!-- MY BOOKINGS -->
        <div class="section" style="grid-column: span 2;">
            <h2><i class="fas fa-calendar-check"></i> My Bookings</h2>
            <div style="text-align: center; padding: 40px 20px; color: var(--green-medium);">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                <p>No bookings yet. Start exploring our services!</p>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER - SAME AS MAIN -->
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
            <a href="contact.html">Contact Us</a>
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
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    function deleteAccount() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            window.location.href = 'delete_account.php';
        }
    }
</script>

</body>
</html>
