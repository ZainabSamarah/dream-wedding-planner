<?php
/**
 * Guest RSVP Response Page
 * Public page for guests to respond to wedding invitations via QR code
 */

require_once 'config.php';

$guest = null;
$event = null;
$error_msg = '';
$success_msg = '';
$mode = 'form'; // 'form', 'success', 'error'

// Check for guest-specific code
if (isset($_GET['code'])) {
    $guest_code = $_GET['code'];

    try {
        $stmt = $pdo->prepare("
            SELECT g.*, e.event_name, e.event_date, e.event_location, e.rsvp_deadline
            FROM rsvp_guests g
            LEFT JOIN wedding_events e ON g.event_id = e.id
            WHERE g.unique_code = ?
        ");
        $stmt->execute([$guest_code]);
        $guest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$guest) {
            $error_msg = "Invalid or expired invitation link.";
            $mode = 'error';
        } elseif ($guest['responded_at']) {
            $mode = 'already_responded';
        }
    } catch (Exception $e) {
        $error_msg = "An error occurred. Please try again.";
        $mode = 'error';
    }
}

// Check for event-wide code (for adding new guests)
elseif (isset($_GET['event'])) {
    $event_code = $_GET['event'];

    try {
        $stmt = $pdo->prepare("
            SELECT * FROM wedding_events WHERE rsvp_code = ?
        ");
        $stmt->execute([$event_code]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            $error_msg = "Invalid invitation link.";
            $mode = 'error';
        } else {
            $mode = 'new_guest';
        }
    } catch (Exception $e) {
        $error_msg = "An error occurred. Please try again.";
        $mode = 'error';
    }
} else {
    $error_msg = "No invitation code provided.";
    $mode = 'error';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'respond' && $guest) {
        $status = $_POST['status'] ?? '';
        $party_size = intval($_POST['party_size'] ?? 1);
        $dietary = trim($_POST['dietary_restrictions'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (in_array($status, ['attending', 'not-attending'])) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE rsvp_guests 
                    SET status = ?, party_size = ?, dietary_restrictions = ?, responded_at = NOW()
                    WHERE unique_code = ?
                ");
                $stmt->execute([$status, $party_size, $dietary, $guest['unique_code']]);

                if (!empty($message)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO rsvp_messages (guest_id, user_id, message) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$guest['id'], $guest['user_id'], $message]);
                }

                $success_msg = $status === 'attending'
                    ? "Thank you! We can't wait to see you!"
                    : "Thank you for letting us know. We'll miss you!";
                $mode = 'success';

            } catch (Exception $e) {
                $error_msg = "An error occurred. Please try again.";
            }
        }
    } elseif ($action === 'new_rsvp' && $event) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $party_size = intval($_POST['party_size'] ?? 1);
        $dietary = trim($_POST['dietary_restrictions'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!empty($name)) {
            try {
                $unique_code = bin2hex(random_bytes(6));

                $stmt = $pdo->prepare("
                    INSERT INTO rsvp_guests (user_id, event_id, unique_code, name, email, phone, status, party_size, dietary_restrictions, responded_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $event['user_id'],
                    $event['id'],
                    $unique_code,
                    $name,
                    $email,
                    $phone,
                    $status,
                    $party_size,
                    $dietary
                ]);

                $guest_id = $pdo->lastInsertId();

                if (!empty($message)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO rsvp_messages (guest_id, user_id, message) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$guest_id, $event['user_id'], $message]);
                }

                $success_msg = $status === 'attending'
                    ? "Thank you, $name! We can't wait to see you!"
                    : "Thank you for letting us know, $name!";
                $mode = 'success';

            } catch (Exception $e) {
                $error_msg = "An error occurred. Please try again.";
            }
        } else {
            $error_msg = "Please enter your name.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RSVP – WEDÉ Wedding</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
            --white: #FFFFFF;
            --gray-light: #F9FAFB;
            --success: #10B981;
            --danger: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--green-pale) 0%, var(--green-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .rsvp-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .rsvp-header {
            background: linear-gradient(135deg, var(--green-medium), var(--green-light));
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .rsvp-header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            margin-bottom: 10px;
        }

        .rsvp-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .event-details {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .event-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .rsvp-body {
            padding: 40px 30px;
        }

        .guest-greeting {
            text-align: center;
            margin-bottom: 30px;
        }

        .guest-greeting h2 {
            color: var(--green-dark);
            font-size: 24px;
            margin-bottom: 10px;
        }

        .guest-greeting p {
            color: #666;
            font-size: 14px;
        }

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
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--green-light);
        }

        .rsvp-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .rsvp-option {
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .rsvp-option:hover {
            border-color: var(--green-light);
        }

        .rsvp-option.selected {
            border-color: var(--green-medium);
            background: var(--green-pale);
        }

        .rsvp-option.attending.selected {
            border-color: var(--success);
            background: #D1FAE5;
        }

        .rsvp-option.not-attending.selected {
            border-color: var(--danger);
            background: #FEE2E2;
        }

        .rsvp-option input {
            display: none;
        }

        .rsvp-option i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }

        .rsvp-option.attending i {
            color: var(--success);
        }

        .rsvp-option.not-attending i {
            color: var(--danger);
        }

        .rsvp-option span {
            font-weight: 600;
            color: var(--green-dark);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--green-medium), var(--green-light));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Success State */
        .success-state {
            text-align: center;
            padding: 60px 30px;
        }

        .success-state i {
            font-size: 80px;
            color: var(--success);
            margin-bottom: 20px;
        }

        .success-state h2 {
            color: var(--green-dark);
            margin-bottom: 15px;
            font-size: 28px;
        }

        .success-state p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        /* Error State */
        .error-state {
            text-align: center;
            padding: 60px 30px;
        }

        .error-state i {
            font-size: 80px;
            color: var(--danger);
            margin-bottom: 20px;
        }

        .error-state h2 {
            color: var(--green-dark);
            margin-bottom: 15px;
        }

        .error-state p {
            color: #666;
        }

        /* Already Responded */
        .already-responded {
            text-align: center;
            padding: 60px 30px;
        }

        .already-responded i {
            font-size: 80px;
            color: var(--green-light);
            margin-bottom: 20px;
        }

        .already-responded h2 {
            color: var(--green-dark);
            margin-bottom: 15px;
        }

        .already-responded p {
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 15px;
        }

        .status-badge.attending {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-badge.not-attending {
            background: #FEE2E2;
            color: #991B1B;
        }

        .powered-by {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }

        .powered-by a {
            color: var(--green-medium);
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .rsvp-header h1 {
                font-size: 36px;
            }

            .rsvp-options {
                grid-template-columns: 1fr;
            }

            .rsvp-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="rsvp-container">
        <div class="rsvp-header">
            <h1>WEDÉ</h1>
            <p>You're Invited!</p>

            <?php if ($guest && $guest['event_name']): ?>
                <div class="event-details">
                    <p><strong><?php echo htmlspecialchars($guest['event_name']); ?></strong></p>
                    <?php if ($guest['event_date']): ?>
                        <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($guest['event_date'])); ?></p>
                    <?php endif; ?>
                    <?php if ($guest['event_location']): ?>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($guest['event_location']); ?></p>
                    <?php endif; ?>
                </div>
            <?php elseif ($event): ?>
                <div class="event-details">
                    <p><strong><?php echo htmlspecialchars($event['event_name']); ?></strong></p>
                    <?php if ($event['event_date']): ?>
                        <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                    <?php endif; ?>
                    <?php if ($event['event_location']): ?>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['event_location']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($mode === 'success'): ?>
            <!-- Success State -->
            <div class="success-state">
                <i class="fas fa-check-circle"></i>
                <h2>Response Received!</h2>
                <p><?php echo htmlspecialchars($success_msg); ?></p>
            </div>

        <?php elseif ($mode === 'error'): ?>
            <!-- Error State -->
            <div class="error-state">
                <i class="fas fa-exclamation-circle"></i>
                <h2>Oops!</h2>
                <p><?php echo htmlspecialchars($error_msg); ?></p>
            </div>

        <?php elseif ($mode === 'already_responded'): ?>
            <!-- Already Responded State -->
            <div class="already-responded">
                <i class="fas fa-envelope-open-text"></i>
                <h2>Already Responded</h2>
                <p>Hi <?php echo htmlspecialchars($guest['name']); ?>, you've already submitted your RSVP.</p>
                <span class="status-badge <?php echo $guest['status']; ?>">
                    <?php echo $guest['status'] === 'attending' ? '✓ Attending' : '✗ Not Attending'; ?>
                </span>
            </div>

        <?php elseif ($mode === 'new_guest' && $event): ?>
            <!-- New Guest Form -->
            <div class="rsvp-body">
                <div class="guest-greeting">
                    <h2>RSVP</h2>
                    <p>Please let us know if you can make it!</p>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="new_rsvp">

                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" placeholder="+1 (555) 000-0000">
                    </div>

                    <label style="display: block; margin-bottom: 10px; font-weight: 500; color: var(--green-dark);">Will you
                        attend? *</label>
                    <div class="rsvp-options">
                        <label class="rsvp-option attending" onclick="selectOption(this)">
                            <input type="radio" name="status" value="attending" required>
                            <i class="fas fa-heart"></i>
                            <span>Joyfully Accept</span>
                        </label>
                        <label class="rsvp-option not-attending" onclick="selectOption(this)">
                            <input type="radio" name="status" value="not-attending" required>
                            <i class="fas fa-heart-broken"></i>
                            <span>Regretfully Decline</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests</label>
                        <select name="party_size">
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Dietary Restrictions</label>
                        <input type="text" name="dietary_restrictions" placeholder="e.g., Vegetarian, Allergies">
                    </div>

                    <div class="form-group">
                        <label>Message for the Couple</label>
                        <textarea name="message" rows="3" placeholder="Send your wishes..."></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit RSVP
                    </button>
                </form>
            </div>

        <?php elseif ($guest): ?>
            <!-- Guest Response Form -->
            <div class="rsvp-body">
                <div class="guest-greeting">
                    <h2>Dear <?php echo htmlspecialchars($guest['name']); ?></h2>
                    <p>We would be honored to have you celebrate with us!</p>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="respond">

                    <label style="display: block; margin-bottom: 10px; font-weight: 500; color: var(--green-dark);">Will you
                        attend? *</label>
                    <div class="rsvp-options">
                        <label class="rsvp-option attending" onclick="selectOption(this)">
                            <input type="radio" name="status" value="attending" required>
                            <i class="fas fa-heart"></i>
                            <span>Joyfully Accept</span>
                        </label>
                        <label class="rsvp-option not-attending" onclick="selectOption(this)">
                            <input type="radio" name="status" value="not-attending" required>
                            <i class="fas fa-heart-broken"></i>
                            <span>Regretfully Decline</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests</label>
                        <select name="party_size">
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Dietary Restrictions</label>
                        <input type="text" name="dietary_restrictions" placeholder="e.g., Vegetarian, Allergies">
                    </div>

                    <div class="form-group">
                        <label>Message for the Couple</label>
                        <textarea name="message" rows="3" placeholder="Send your wishes..."></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit RSVP
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div class="powered-by">
            Powered by <a href="main.html">WEDÉ</a> Wedding Planner
        </div>
    </div>

    <script>
        function selectOption(element) {
            document.querySelectorAll('.rsvp-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input').checked = true;
        }
    </script>
</body>

</html>