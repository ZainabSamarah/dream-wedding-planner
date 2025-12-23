<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'User';
$last_name = $_SESSION['last_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? 'user';

// Redirect admin/owner to admin dashboard
if ($role === 'admin' || $role === 'owner') {
    header("Location: admin.php");
    exit();
}

// Fetch Package Details
$package_name = "No Package Selected";
try {
    $stmt = $pdo->prepare("
        SELECT p.name 
        FROM user_packages up
        JOIN packages p ON up.package_id = p.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $pkg = $stmt->fetch();
    if ($pkg) {
        $package_name = $pkg['name'];
    }
} catch (Exception $e) {
    $package_name = "Error fetching package";
}

// Food selections
$selected_food_names = [];
try {
    $stmt = $pdo->prepare("
        SELECT fm.name 
        FROM user_food_selections ufs
        JOIN food_menu fm ON ufs.food_menu_id = fm.id
        WHERE ufs.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $selected_food_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $food_count = count($selected_food_names);
} catch (Exception $e) {
    $food_count = 0;
}

// Cake selections
$selected_cake_names = [];
try {
    $stmt = $pdo->prepare("
        SELECT cm.name 
        FROM user_cake_selections ucs
        JOIN cake_menu cm ON ucs.cake_id = cm.id
        WHERE ucs.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $selected_cake_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $cake_count = count($selected_cake_names);
} catch (Exception $e) {
    $cake_count = 0;
}

// Card customizations (Wedding details source)
$wedding_details = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM user_card_customizations WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $wedding_details = $stmt->fetch();
} catch (Exception $e) {
    $wedding_details = null;
}

$card_count = $wedding_details ? 1 : 0;
$total_selections = $food_count + $cake_count + $card_count;

// Fetch Messages from owners
$messages = [];
$unread_count = 0;
try {
    $stmt = $pdo->prepare("
        SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.role as sender_role
        FROM messages m
        LEFT JOIN users u ON m.from_user_id = u.id
        WHERE m.to_user_id = ?
        ORDER BY m.sent_at DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();

    // Count unread
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE to_user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $unread_count = $stmt->fetchColumn();
} catch (Exception $e) {
    $messages = [];
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – WEDÉ</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&family=Outfit:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
            --green-extra-pale: #E8F0E5;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.5);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--green-extra-pale) 0%, #ffffff 100%);
            color: var(--green-dark);
            min-height: 100vh;
            padding-top: 90px;
            overflow-x: hidden;
        }

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
            z-index: 100;
            height: 80px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }

        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 42px;
            color: var(--green-pale);
        }

        header h1 a {
            color: var(--green-pale);
            text-decoration: none;
        }

        header nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Outfit', sans-serif;
        }

        header nav a:hover {
            color: var(--green-pale);
            transform: translateY(-2px);
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            padding: 10px 20px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            cursor: pointer;
        }

        .logout-btn:hover {
            background: var(--green-dark);
            border-color: var(--green-dark);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Profile Hero Section */
        .profile-hero {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 60px 40px;
            border-radius: 30px;
            text-align: center;
            margin-bottom: 50px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 40px rgba(75, 89, 69, 0.08);
            position: relative;
            overflow: hidden;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(145, 172, 143, 0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--green-medium), var(--green-light));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 60px;
            color: white;
            box-shadow: 0 15px 35px rgba(102, 120, 95, 0.3);
            border: 6px solid white;
        }

        .profile-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 8px;
        }

        .profile-hero .email {
            font-size: 18px;
            color: var(--green-medium);
            margin-bottom: 20px;
            font-weight: 400;
        }

        .package-badge {
            display: inline-block;
            background: var(--green-dark);
            color: white;
            padding: 10px 30px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            box-shadow: 0 10px 20px rgba(75, 89, 69, 0.15);
            margin-bottom: 40px;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding-top: 40px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat {
            position: relative;
        }

        .stat:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 40px;
            background: rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: var(--green-light);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            margin-top: 5px;
        }

        /* Sections Grid */
        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 25px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 15px 35px rgba(75, 89, 69, 0.05);
            transition: var(--transition);
        }

        .section:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 45px rgba(75, 89, 69, 0.1);
        }

        .section h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--green-dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section h2 i {
            color: var(--green-light);
            background: var(--green-extra-pale);
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 20px;
        }

        .info-list {
            list-style: none;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--green-medium);
            font-size: 15px;
            font-weight: 500;
        }

        .info-value {
            color: var(--green-dark);
            font-weight: 600;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            padding: 15px 25px;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 15px;
        }

        .btn-outline {
            background: transparent;
            color: var(--green-dark);
            border: 2px solid var(--green-light);
        }

        .btn-outline:hover {
            background: var(--green-light);
            color: white;
            box-shadow: 0 10px 20px rgba(145, 172, 143, 0.3);
        }

        .btn-danger {
            background: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
            margin-top: 10px;
        }

        .btn-danger:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.2);
        }

        /* Activity Card */
        .activity-card {
            grid-column: 1 / -1;
        }

        .activity-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .activity-list {
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 20px;
        }

        .activity-list h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-list ul {
            list-style: none;
        }

        .activity-list li {
            padding: 10px 0;
            font-size: 15px;
            color: var(--green-dark);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .activity-list li::before {
            content: '\f058';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--green-light);
            font-size: 14px;
        }

        footer {
            background-color: var(--green-dark);
            color: white;
            padding: 80px 40px 40px;
            margin-top: 100px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-column h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--green-pale);
        }

        .footer-column a {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 15px;
            font-size: 15px;
            transition: var(--transition);
        }

        .footer-column a:hover {
            color: white;
            padding-left: 10px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 40px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .profile-hero {
                padding: 40px 20px;
            }

            .profile-hero h1 {
                font-size: 32px;
            }

            .stats-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stat::after {
                display: none;
            }

            .activity-card {
                grid-column: auto;
            }

            header {
                padding: 15px 20px;
            }

            header h1 {
                font-size: 32px;
            }

            header nav a span {
                display: none;
            }
        }

        /* Messages Section */
        .messages-section {
            background: linear-gradient(135deg, var(--green-pale) 0%, #d4e5d0 100%);
        }

        .messages-section h2 i {
            background: var(--green-dark);
            color: white;
        }

        .unread-badge {
            background: #e74c3c;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
            font-weight: 700;
        }

        .message-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: var(--transition);
        }

        .message-item:hover {
            background: white;
            transform: translateX(5px);
        }

        .message-item.unread {
            border-left: 4px solid var(--green-light);
        }

        .msg-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .msg-sender {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 14px;
        }

        .msg-sender .team-badge {
            background: var(--green-medium);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 8px;
        }

        .msg-date {
            font-size: 11px;
            color: #888;
        }

        .msg-preview {
            font-size: 13px;
            color: #555;
            line-height: 1.5;
        }

        .compose-mini {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
        }

        .compose-mini textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            resize: none;
            margin-bottom: 10px;
        }

        .compose-mini textarea:focus {
            outline: none;
            border-color: var(--green-medium);
        }

        .compose-mini button {
            background: var(--green-dark);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }

        .compose-mini button:hover {
            background: var(--green-medium);
        }

        .no-messages {
            text-align: center;
            padding: 30px;
            color: var(--green-medium);
        }

        .no-messages i {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
</head>

<body>

    <header>
        <h1><a href="main.html">WEDÉ</a></h1>
        <nav>
            <a href="main.html"><i class="fas fa-home"></i> <span>Home</span></a>
            <a href="services.php"><i class="fas fa-sparkles"></i> <span>Services</span></a>
            <a href="gallery.html"><i class="fas fa-images"></i> <span>Gallery</span></a>
            <a href="contact.php"><i class="fas fa-envelope"></i> <span>Contact</span></a>
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </nav>
    </header>

    <div class="container">
        <!-- Hero Section -->
        <div class="profile-hero">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <h1><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h1>
            <p class="email"><?php echo htmlspecialchars($email); ?></p>
            <div class="package-badge"><?php echo htmlspecialchars($package_name); ?></div>

            <div class="stats-row">
                <div class="stat">
                    <div class="stat-number"><?php echo $total_selections; ?></div>
                    <div class="stat-label">Selections</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $food_count; ?></div>
                    <div class="stat-label">Food Items</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $cake_count; ?></div>
                    <div class="stat-label">Cakes/Drinks</div>
                </div>
            </div>
        </div>

        <div class="sections-grid">
            <!-- Information Sections -->
            <div class="section">
                <h2><i class="fas fa-user-circle"></i> Personal Info</h2>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Current Role</span>
                        <span class="info-value"><?php echo ucfirst($role); ?></span>
                    </div>
                </div>
                <button class="btn btn-outline" onclick="openEditProfileModal()">
                    <i class="fas fa-user-edit"></i> Edit Account
                </button>
            </div>

            <div class="section">
                <h2><i class="fas fa-ring"></i> Wedding Overview</h2>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Bride's Name</span>
                        <span
                            class="info-value"><?php echo $wedding_details ? htmlspecialchars($wedding_details['bride_name']) : '---'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Groom's Name</span>
                        <span
                            class="info-value"><?php echo $wedding_details ? htmlspecialchars($wedding_details['groom_name']) : '---'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Wedding Date</span>
                        <span
                            class="info-value"><?php echo $wedding_details ? date('F d, Y', strtotime($wedding_details['wedding_date'])) : 'Not set'; ?></span>
                    </div>
                </div>
                <button class="btn btn-outline" onclick="window.location.href='invCardLux.php'">
                    <i class="fas fa-pen-nib"></i> Design Invitation
                </button>
            </div>

            <div class="section">
                <h2><i class="fas fa-sliders"></i> Preferences</h2>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Selected Venue</span>
                        <span
                            class="info-value"><?php echo $wedding_details ? htmlspecialchars($wedding_details['location']) : 'Not selected'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Service Status</span>
                        <span
                            class="info-value"><?php echo $total_selections > 0 ? 'In Progress' : 'Pending Selection'; ?></span>
                    </div>
                </div>
                <button class="btn btn-outline" onclick="window.location.href='services.php'">
                    <i class="fas fa-clipboard-list"></i> Manage Services
                </button>
            </div>

            <div class="section">
                <h2><i class="fas fa-shield-halved"></i> Privacy & Safety</h2>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Password</span>
                        <span class="info-value">••••••••</span>
                    </div>
                </div>
                <button class="btn btn-outline" onclick="openChangePasswordModal()">
                    <i class="fas fa-key"></i> Update Password
                </button>
                <button class="btn btn-danger" onclick="deleteAccount()">
                    <i class="fas fa-trash-can"></i> Deactivate Account
                </button>
            </div>

            <!-- Messages Section -->
            <div class="section messages-section">
                <h2>
                    <i class="fas fa-envelope"></i> Messages from WEDÉ Team
                    <?php if ($unread_count > 0): ?>
                        <span class="unread-badge"><?php echo $unread_count; ?> new</span>
                    <?php endif; ?>
                </h2>

                <?php if (count($messages) > 0): ?>
                    <div class="messages-list">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-item <?php echo $msg['is_read'] == 0 ? 'unread' : ''; ?>"
                                data-id="<?php echo $msg['id']; ?>">
                                <div class="msg-header">
                                    <span class="msg-sender">
                                        <?php echo htmlspecialchars($msg['sender_name'] ?? 'WEDÉ Team'); ?>
                                        <?php if ($msg['sender_role'] === 'owner' || $msg['sender_role'] === 'admin'): ?>
                                            <span class="team-badge">WEDÉ Team</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="msg-date"><?php echo date('M d, h:i A', strtotime($msg['sent_at'])); ?></span>
                                </div>
                                <div class="msg-preview">
                                    <?php echo htmlspecialchars(strlen($msg['content']) > 150 ? substr($msg['content'], 0, 150) . '...' : $msg['content']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-messages">
                        <i class="fas fa-envelope-open"></i>
                        <p>No messages yet. The WEDÉ team will contact you here!</p>
                    </div>
                <?php endif; ?>

                <div class="compose-mini">
                    <textarea id="profile-message" placeholder="Send a message to the WEDÉ team..." rows="3"></textarea>
                    <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i> Send Message</button>
                </div>

                <button class="btn btn-outline" onclick="window.location.href='my_messages.php'"
                    style="margin-top:15px;">
                    <i class="fas fa-inbox"></i> View All Messages
                </button>
            </div>

            <!-- Activity Activity -->
            <div class="section activity-card">
                <h2><i class="fas fa-calendar-check"></i> Recent Selections & Activity</h2>
                <div class="activity-content">
                    <div class="activity-list">
                        <h3><i class="fas fa-utensils"></i> Selected Menu</h3>
                        <?php if (count($selected_food_names) > 0): ?>
                            <ul>
                                <?php foreach (array_slice($selected_food_names, 0, 5) as $food): ?>
                                    <li><?php echo htmlspecialchars($food); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($selected_food_names) > 5): ?>
                                    <li style="color: var(--green-light); font-weight: 600;">+
                                        <?php echo count($selected_food_names) - 5; ?> more items...
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <p style="color: var(--green-medium); font-style: italic; font-size: 14px;">No food items
                                selected yet.</p>
                        <?php endif; ?>
                    </div>

                    <div class="activity-list">
                        <h3><i class="fas fa-birthday-cake"></i> Selected Cakes</h3>
                        <?php if (count($selected_cake_names) > 0): ?>
                            <ul>
                                <?php foreach (array_slice($selected_cake_names, 0, 5) as $cake): ?>
                                    <li><?php echo htmlspecialchars($cake); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($selected_cake_names) > 5): ?>
                                    <li style="color: var(--green-light); font-weight: 600;">+
                                        <?php echo count($selected_cake_names) - 5; ?> more items...
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <p style="color: var(--green-medium); font-style: italic; font-size: 14px;">No cakes or drinks
                                selected yet.</p>
                        <?php endif; ?>
                    </div>

                    <div class="activity-list">
                        <h3><i class="fas fa-info-circle"></i> Milestone</h3>
                        <p
                            style="color: var(--green-dark); font-size: 15px; border-left: 4px solid var(--green-light); padding-left: 15px; margin-top: 10px;">
                            <?php
                            if ($total_selections == 0)
                                echo "Start your journey by choosing a package that fits your dream vision.";
                            elseif ($total_selections < 10)
                                echo "You're off to a great start! Keep exploring our bespoke services.";
                            else
                                echo "Your wedding is taking shape beautifully. Review your dashboard for the next steps.";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Plan with WEDÉ</h3>
                <a href="services.php">Bespoke Services</a>
                <a href="rsvp.html">RSVP Coordination</a>
                <a href="budget.html">Budget Management</a>
                <a href="gallery.html">Visual Inspiration</a>
            </div>
            <div class="footer-column">
                <h3>Discover</h3>
                <a href="services.php">Verified Vendors</a>
                <a href="gallery.html">Our Portfolio</a>
                <a href="services.php">Planning Insights</a>
            </div>
            <div class="footer-column">
                <h3>The Studio</h3>
                <a href="contact.php">Get in Touch</a>
                <a href="#">Our Story</a>
                <a href="#">Privacy Protocol</a>
                <a href="#">Terms of Service</a>
            </div>
            <div class="footer-column">
                <h3>Connect</h3>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-pinterest"></i> Pinterest</a>
            </div>
        </div>
        <div class="footer-bottom">
            © 2025 <span style="color: var(--green-pale); font-weight: 700;">WEDÉ</span> | EXCELLENCE IN WEDDING
            ARTISTRY
        </div>
    </footer>

    <!-- Modals -->
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal h2 {
            font-family: 'Outfit', sans-serif;
            margin-bottom: 20px;
            color: var(--green-dark);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--green-medium);
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #888;
        }
    </style>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('editProfileModal')">&times;</button>
            <h2>Edit Profile</h2>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" id="edit-firstName" value="<?php echo htmlspecialchars($first_name); ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" id="edit-lastName" value="<?php echo htmlspecialchars($last_name); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="edit-email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="modal-actions">
                <button class="btn btn-outline" onclick="closeModal('editProfileModal')">Cancel</button>
                <button class="btn" style="background: var(--green-dark); color: white;" onclick="updateProfile()">Save
                    Changes</button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('changePasswordModal')">&times;</button>
            <h2>Change Password</h2>
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" id="current-password">
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="new-password">
            </div>
            <div class="modal-actions">
                <button class="btn btn-outline" onclick="closeModal('changePasswordModal')">Cancel</button>
                <button class="btn" style="background: var(--green-dark); color: white;"
                    onclick="changePassword()">Update Password</button>
            </div>
        </div>
    </div>

    <script>
        function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }

        function openChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        async function updateProfile() {
            const firstName = document.getElementById('edit-firstName').value;
            const lastName = document.getElementById('edit-lastName').value;
            const email = document.getElementById('edit-email').value;

            if (!firstName || !lastName || !email) {
                alert('All fields are required');
                return;
            }

            try {
                const res = await fetch('update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update_info', firstName, lastName, email })
                });
                const data = await res.json();

                if (data.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Update failed');
                }
            } catch (e) {
                alert('Error updating profile');
            }
        }

        async function changePassword() {
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;

            if (!currentPassword || !newPassword) {
                alert('All fields are required');
                return;
            }

            try {
                const res = await fetch('update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'change_password', currentPassword, newPassword })
                });
                const data = await res.json();

                if (data.success) {
                    alert('Password updated successfully!');
                    closeModal('changePasswordModal');
                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                } else {
                    alert(data.message || 'Password update failed');
                }
            } catch (e) {
                alert('Error updating password');
            }
        }

        function logout() {
            if (confirm('Are you sure you want to securely logout?')) {
                window.location.href = 'logout.php';
            }
        }

        function deleteAccount() {
            if (confirm('Are you sure you want to deactivate your account? This will remove all your selections and cannot be undone.')) {
                window.location.href = 'delete_account.php';
            }
        }

        async function sendMessage() {
            const textarea = document.getElementById('profile-message');
            const message = textarea.value.trim();

            if (!message) {
                alert('Please enter a message');
                return;
            }

            const fd = new FormData();
            fd.append('action', 'send_message');
            fd.append('message', message);

            try {
                const res = await fetch('user_messages_api.php', { method: 'POST', body: fd });
                const json = await res.json();

                if (json.success) {
                    alert('Message sent successfully! The WEDÉ team will respond soon.');
                    textarea.value = '';
                } else {
                    alert(json.message || 'Failed to send message');
                }
            } catch (e) {
                alert('Error sending message. Please try again.');
            }
        }

        // Mark messages as read when clicked
        document.querySelectorAll('.message-item.unread').forEach(item => {
            item.addEventListener('click', async function () {
                const id = this.dataset.id;
                const fd = new FormData();
                fd.append('action', 'mark_read');
                fd.append('id', id);
                await fetch('user_messages_api.php', { method: 'POST', body: fd });
                this.classList.remove('unread');
            });
        });
    </script>
</body>

</html>