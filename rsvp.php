<?php
/**
 * RSVP Manager - Wedding Planner Dashboard
 * Manage wedding guests, track RSVPs, generate QR codes
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Guest';
$user_email = '';

// Get user email
try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_email = $user['email'] ?? '';
} catch (Exception $e) {
    // Ignore
}

// Get or create the user's wedding event
$event = null;
$rsvp_url = '';
try {
    $stmt = $pdo->prepare("SELECT * FROM wedding_events WHERE user_id = ? ORDER BY created_at ASC LIMIT 1");
    $stmt->execute([$user_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        // Create default event
        $rsvp_code = bin2hex(random_bytes(8));
        $stmt = $pdo->prepare("INSERT INTO wedding_events (user_id, event_name, rsvp_code) VALUES (?, 'My Wedding', ?)");
        $stmt->execute([$user_id, $rsvp_code]);
        $event = [
            'id' => $pdo->lastInsertId(),
            'rsvp_code' => $rsvp_code,
            'event_name' => 'My Wedding'
        ];
    }

    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $rsvp_url = $base_url . dirname($_SERVER['REQUEST_URI']) . "/guest_rsvp.php?event=" . $event['rsvp_code'];
} catch (Exception $e) {
    // Tables might not exist
}

// Load guests
$guests = [];
$stats = ['total' => 0, 'attending' => 0, 'pending' => 0, 'not_attending' => 0];
try {
    $stmt = $pdo->prepare("SELECT * FROM rsvp_guests WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats['total'] = count($guests);
    $stats['attending'] = count(array_filter($guests, fn($g) => $g['status'] === 'attending'));
    $stats['pending'] = count(array_filter($guests, fn($g) => $g['status'] === 'pending'));
    $stats['not_attending'] = count(array_filter($guests, fn($g) => $g['status'] === 'not-attending'));
} catch (Exception $e) {
    // Tables might not exist
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSVP Manager – WEDÉ</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- QRCode.js for generating QR codes -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
            --white: #FFFFFF;
            --gray-light: #F9FAFB;
            --gray-medium: #E5E7EB;
            --gray-dark: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gray-light);
            color: var(--green-dark);
            overflow-x: hidden;
        }

        /* Header */
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
            height: 70px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 32px;
            color: var(--green-pale);
        }

        header h1 a {
            color: var(--green-pale);
            text-decoration: none;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            font-size: 14px;
        }

        nav a:hover {
            opacity: 0.8;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-btn {
            padding: 10px 20px;
            background-color: var(--green-light);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-btn:hover {
            background-color: var(--green-dark);
            transform: translateY(-2px);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 250px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 100;
            border-radius: 10px;
            top: 50px;
            overflow: hidden;
        }

        .dropdown-content.show {
            display: block;
        }

        .dropdown-header {
            padding: 15px 20px;
            background-color: var(--green-light);
            color: white;
        }

        .dropdown-header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .dropdown-header .user-name {
            font-weight: 600;
            font-size: 16px;
        }

        .dropdown-content a {
            color: var(--green-dark);
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        .dropdown-content a i {
            color: var(--green-light);
        }

        .dropdown-divider {
            height: 1px;
            background-color: #ddd;
            margin: 5px 0;
        }

        .logout-link {
            color: #d32f2f !important;
        }

        .logout-link i {
            color: #d32f2f !important;
        }

        /* Main Content */
        main {
            margin-top: 70px;
            padding: 40px 20px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: var(--green-dark);
        }

        .header-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-left: 5px solid var(--green-light);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-card.total {
            border-left-color: var(--green-medium);
        }

        .stat-card.attending {
            border-left-color: var(--success);
        }

        .stat-card.pending {
            border-left-color: var(--warning);
        }

        .stat-card.not-attending {
            border-left-color: var(--danger);
        }

        .stat-number {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-card.total .stat-number {
            color: var(--green-medium);
        }

        .stat-card.attending .stat-number {
            color: var(--success);
        }

        .stat-card.pending .stat-number {
            color: var(--warning);
        }

        .stat-card.not-attending .stat-number {
            color: var(--danger);
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-dark);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* QR Code Section */
        .qr-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .qr-code-container {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid var(--green-pale);
        }

        .qr-code-container canvas {
            display: block;
        }

        .qr-info {
            flex: 1;
            min-width: 250px;
        }

        .qr-info h3 {
            color: var(--green-dark);
            margin-bottom: 10px;
            font-size: 20px;
        }

        .qr-info p {
            color: var(--gray-dark);
            margin-bottom: 15px;
            font-size: 14px;
        }

        .rsvp-link {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--gray-light);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .rsvp-link input {
            flex: 1;
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--green-dark);
        }

        .rsvp-link button {
            padding: 8px 15px;
            background: var(--green-light);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: 0.3s;
        }

        .rsvp-link button:hover {
            background: var(--green-dark);
        }

        /* Cards */
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--green-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--green-light);
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--green-dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--green-light);
            box-shadow: 0 0 0 3px rgba(145, 172, 143, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--green-medium);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--green-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--gray-medium);
            color: var(--green-dark);
        }

        .btn-secondary:hover {
            background-color: var(--gray-dark);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #DC2626;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-small {
            padding: 8px 12px;
            font-size: 12px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        /* Table */
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-dark);
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 15px;
            border: 1px solid var(--gray-medium);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
            color: var(--gray-dark);
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: var(--green-light);
            background: var(--green-pale);
            color: white;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .guests-table {
            width: 100%;
            border-collapse: collapse;
        }

        .guests-table thead {
            background-color: var(--green-medium);
            color: white;
        }

        .guests-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .guests-table td {
            padding: 15px;
            border-bottom: 1px solid var(--gray-medium);
            font-size: 14px;
        }

        .guests-table tbody tr {
            transition: 0.3s;
        }

        .guests-table tbody tr:hover {
            background-color: var(--gray-light);
        }

        .guest-name {
            font-weight: 600;
            color: var(--green-dark);
        }

        .guest-email {
            color: var(--gray-dark);
            font-size: 13px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-badge.attending {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-badge.pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-badge.not-attending {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .action-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            transition: 0.3s;
        }

        .action-btn.edit {
            background: var(--green-pale);
            color: var(--green-dark);
        }

        .action-btn.qr {
            background: #E0E7FF;
            color: #3730A3;
        }

        .action-btn.delete {
            background: #FEE2E2;
            color: #991B1B;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-dark);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--green-light);
            margin-bottom: 20px;
        }

        .empty-state p {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            font-size: 24px;
            color: var(--green-dark);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-dark);
        }

        .modal-close:hover {
            color: var(--green-dark);
        }

        /* QR Modal */
        .qr-modal-content {
            text-align: center;
        }

        .qr-modal-content canvas {
            margin: 20px auto;
            display: block;
        }

        .qr-modal-content .guest-name {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .qr-download-btn {
            margin-top: 20px;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 300;
            animation: slideInUp 0.3s ease;
            max-width: 400px;
        }

        .toast.success {
            border-left: 4px solid var(--success);
        }

        .toast.error {
            border-left: 4px solid var(--danger);
        }

        .toast.success i {
            color: var(--success);
        }

        .toast.error i {
            color: var(--danger);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Export Section */
        .export-section {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            nav a {
                display: none;
            }

            main {
                padding: 20px 15px;
            }

            .page-header h1 {
                font-size: 36px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .qr-section {
                flex-direction: column;
                text-align: center;
            }

            .card {
                padding: 20px;
            }

            .table-controls {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
                justify-content: space-between;
            }

            .action-cell {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <h1><a href="services.php">WEDÉ</a></h1>
        <nav>
            <a href="services.php">Services</a>
            <a href="gallery.html">Gallery</a>
            <a href="contact.php">Contact</a>

            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <button class="profile-btn" onclick="toggleDropdown()">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($username); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="dropdownMenu" class="dropdown-content">
                    <div class="dropdown-header">
                        <p class="user-name"><?php echo htmlspecialchars($username); ?></p>
                        <p><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                    <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="services.php"><i class="fas fa-concierge-bell"></i> My Bookings</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <div class="page-header">
            <h1>Guest Management</h1>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportAsCSV()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
                <button class="btn btn-primary" onclick="openAddGuestModal()">
                    <i class="fas fa-plus"></i> Add Guest
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number" id="totalCount"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Guests</div>
            </div>
            <div class="stat-card attending">
                <div class="stat-number" id="attendingCount"><?php echo $stats['attending']; ?></div>
                <div class="stat-label">Attending</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number" id="pendingCount"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card not-attending">
                <div class="stat-number" id="notAttendingCount"><?php echo $stats['not_attending']; ?></div>
                <div class="stat-label">Not Attending</div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <div class="qr-code-container" id="mainQrCode"></div>
            <div class="qr-info">
                <h3><i class="fas fa-qrcode"></i> Your RSVP QR Code</h3>
                <p>Share this QR code on your invitation cards. Guests can scan it to RSVP directly!</p>
                <div class="rsvp-link">
                    <input type="text" id="rsvpLinkInput" value="<?php echo htmlspecialchars($rsvp_url); ?>" readonly>
                    <button onclick="copyRsvpLink()"><i class="fas fa-copy"></i> Copy</button>
                </div>
                <button class="btn btn-primary" onclick="downloadMainQR()">
                    <i class="fas fa-download"></i> Download QR Code
                </button>
            </div>
        </div>

        <!-- Add Guest Card -->
        <div class="card">
            <h2 class="section-title"><i class="fas fa-user-plus"></i> Add Guest</h2>

            <form id="addGuestForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="guestName" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="guestEmail" placeholder="john@example.com">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="guestPhone" placeholder="+1 (555) 000-0000">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="guestStatus">
                            <option value="pending">Pending</option>
                            <option value="attending">Attending</option>
                            <option value="not-attending">Not Attending</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="guestNotes" placeholder="Add any special notes..."></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Guest
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Guest List Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="section-title"><i class="fas fa-users"></i> Guest List</h2>
            </div>

            <div class="table-controls">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search by name, email, or phone...">
                </div>
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="attending">Attending</button>
                    <button class="filter-btn" data-filter="pending">Pending</button>
                    <button class="filter-btn" data-filter="not-attending">Not Attending</button>
                </div>
            </div>

            <div class="table-wrapper">
                <div id="guestsContainer">
                    <?php if (empty($guests)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No guests yet</p>
                            <small>Add your first guest to get started</small>
                        </div>
                    <?php else: ?>
                        <table class="guests-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Party Size</th>
                                    <th>Responded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="guestsTableBody">
                                <?php foreach ($guests as $guest): ?>
                                    <tr data-id="<?php echo $guest['id']; ?>" data-status="<?php echo $guest['status']; ?>">
                                        <td>
                                            <div class="guest-name"><?php echo htmlspecialchars($guest['name']); ?></div>
                                        </td>
                                        <td>
                                            <div class="guest-email"><?php echo htmlspecialchars($guest['email'] ?: '-'); ?>
                                            </div>
                                            <div style="font-size: 12px; color: var(--gray-dark);">
                                                <?php echo htmlspecialchars($guest['phone'] ?: ''); ?></div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $guest['status']; ?>">
                                                <?php echo ucfirst(str_replace('-', ' ', $guest['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $guest['party_size'] ?? 1; ?></td>
                                        <td><?php echo $guest['responded_at'] ? date('M j, Y', strtotime($guest['responded_at'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                                <button class="action-btn qr"
                                                    onclick="showGuestQR(<?php echo $guest['id']; ?>, '<?php echo htmlspecialchars($guest['name']); ?>', '<?php echo $guest['unique_code']; ?>')">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                                <button class="action-btn edit"
                                                    onclick="editGuest(<?php echo $guest['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn delete"
                                                    onclick="deleteGuest(<?php echo $guest['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div class="export-section">
                <button class="btn btn-primary" onclick="exportAsCSV()" <?php echo empty($guests) ? 'disabled' : ''; ?>>
                    <i class="fas fa-download"></i> Export as CSV
                </button>
                <button class="btn btn-danger" onclick="clearAllGuests()" <?php echo empty($guests) ? 'disabled' : ''; ?>>
                    <i class="fas fa-trash"></i> Clear All
                </button>
            </div>
        </div>
    </main>

    <!-- Guest QR Modal -->
    <div class="modal" id="qrModal">
        <div class="modal-content qr-modal-content">
            <div class="modal-header">
                <h2>Guest QR Code</h2>
                <button class="modal-close" onclick="closeQRModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="guest-name" id="qrGuestName"></div>
            <p style="color: var(--gray-dark); margin-bottom: 20px;">Scan to RSVP</p>
            <div id="guestQrCode"></div>
            <button class="btn btn-primary qr-download-btn" onclick="downloadGuestQR()">
                <i class="fas fa-download"></i> Download QR Code
            </button>
        </div>
    </div>

    <!-- Edit Guest Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Guest</h2>
                <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="editGuestForm">
                <input type="hidden" id="editGuestId">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="editEmail">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" id="editPhone">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="editStatus">
                        <option value="pending">Pending</option>
                        <option value="attending">Attending</option>
                        <option value="not-attending">Not Attending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="editNotes"></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const userId = <?php echo json_encode($user_id); ?>;
        const rsvpUrl = <?php echo json_encode($rsvp_url); ?>;
        let guests = <?php echo json_encode($guests); ?>;
        let currentFilter = 'all';
        let currentGuestCode = '';

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            generateMainQR();
            initEventListeners();
        });

        // Generate main QR code
        function generateMainQR() {
            const container = document.getElementById('mainQrCode');
            container.innerHTML = '';

            if (typeof QRCode !== 'undefined') {
                QRCode.toCanvas(rsvpUrl, { width: 150, margin: 2 }, function (error, canvas) {
                    if (!error) {
                        container.appendChild(canvas);
                    }
                });
            }
        }

        // Initialize event listeners
        function initEventListeners() {
            // Add guest form
            document.getElementById('addGuestForm').addEventListener('submit', addGuest);

            // Edit guest form
            document.getElementById('editGuestForm').addEventListener('submit', saveEditedGuest);

            // Search
            document.getElementById('searchInput').addEventListener('input', filterGuests);

            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    filterGuests();
                });
            });
        }

        // Profile dropdown
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('show');
        }

        window.addEventListener('click', function (event) {
            if (!event.target.matches('.profile-btn') && !event.target.closest('.profile-btn')) {
                const dropdown = document.getElementById('dropdownMenu');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Add guest
        async function addGuest(e) {
            e.preventDefault();

            const data = {
                action: 'add_guest',
                name: document.getElementById('guestName').value,
                email: document.getElementById('guestEmail').value,
                phone: document.getElementById('guestPhone').value,
                status: document.getElementById('guestStatus').value,
                notes: document.getElementById('guestNotes').value
            };

            try {
                const response = await fetch('save_rsvp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Guest added successfully!', 'success');
                    e.target.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Error adding guest', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        // Edit guest
        function editGuest(id) {
            const guest = guests.find(g => g.id == id);
            if (!guest) return;

            document.getElementById('editGuestId').value = guest.id;
            document.getElementById('editName').value = guest.name;
            document.getElementById('editEmail').value = guest.email || '';
            document.getElementById('editPhone').value = guest.phone || '';
            document.getElementById('editStatus').value = guest.status;
            document.getElementById('editNotes').value = guest.notes || '';

            document.getElementById('editModal').classList.add('active');
        }

        async function saveEditedGuest(e) {
            e.preventDefault();

            const data = {
                action: 'update_guest',
                guest_id: document.getElementById('editGuestId').value,
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value,
                status: document.getElementById('editStatus').value,
                notes: document.getElementById('editNotes').value
            };

            try {
                const response = await fetch('save_rsvp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Guest updated!', 'success');
                    closeEditModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Error updating guest', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Delete guest
        async function deleteGuest(id) {
            if (!confirm('Are you sure you want to delete this guest?')) return;

            try {
                const response = await fetch('save_rsvp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_guest', guest_id: id })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Guest deleted!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(result.message || 'Error', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        // Clear all guests
        async function clearAllGuests() {
            if (!confirm('Are you sure you want to delete ALL guests? This cannot be undone!')) return;

            try {
                const response = await fetch('save_rsvp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_all_guests' })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('All guests deleted!', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        // Show guest QR code
        function showGuestQR(id, name, code) {
            currentGuestCode = code;
            document.getElementById('qrGuestName').textContent = name;

            const container = document.getElementById('guestQrCode');
            container.innerHTML = '';

            const guestUrl = window.location.origin + window.location.pathname.replace('rsvp.php', 'guest_rsvp.php') + '?code=' + code;

            if (typeof QRCode !== 'undefined') {
                QRCode.toCanvas(guestUrl, { width: 200, margin: 2 }, function (error, canvas) {
                    if (!error) {
                        canvas.id = 'guestQRCanvas';
                        container.appendChild(canvas);
                    }
                });
            }

            document.getElementById('qrModal').classList.add('active');
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.remove('active');
        }

        function downloadGuestQR() {
            const canvas = document.getElementById('guestQRCanvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = 'guest-qr-' + currentGuestCode + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
        }

        function downloadMainQR() {
            const canvas = document.querySelector('#mainQrCode canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = 'wedding-rsvp-qr.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
        }

        // Copy RSVP link
        function copyRsvpLink() {
            const input = document.getElementById('rsvpLinkInput');
            input.select();
            document.execCommand('copy');
            showToast('Link copied!', 'success');
        }

        // Filter guests
        function filterGuests() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#guestsTableBody tr');

            rows.forEach(row => {
                const name = row.querySelector('.guest-name').textContent.toLowerCase();
                const email = row.querySelector('.guest-email').textContent.toLowerCase();
                const status = row.dataset.status;

                const matchesSearch = name.includes(search) || email.includes(search);
                const matchesFilter = currentFilter === 'all' || status === currentFilter;

                row.style.display = matchesSearch && matchesFilter ? '' : 'none';
            });
        }

        // Export as CSV
        function exportAsCSV() {
            let csv = 'Name,Email,Phone,Status,Party Size,Responded\n';
            guests.forEach(g => {
                csv += `"${g.name}","${g.email || ''}","${g.phone || ''}","${g.status}","${g.party_size || 1}","${g.responded_at || ''}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'wedding-guests.csv';
            a.click();
        }

        // Toast notification
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Close modals on outside click
        window.addEventListener('click', function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });
    </script>
</body>

</html>