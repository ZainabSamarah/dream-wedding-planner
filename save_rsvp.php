<?php
/**
 * Save RSVP Data
 * API endpoint to manage wedding events, guests, and RSVP responses
 */

require_once 'config.php';

// Set JSON response header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

$action = $data['action'] ?? '';

// Helper function to generate unique codes
function generateUniqueCode($length = 12)
{
    return bin2hex(random_bytes($length / 2));
}

try {
    switch ($action) {
        // ========== GUEST RSVP RESPONSE (PUBLIC) ==========
        case 'guest_respond':
            $guest_code = $data['guest_code'] ?? '';
            $status = $data['status'] ?? '';
            $party_size = intval($data['party_size'] ?? 1);
            $dietary = $data['dietary_restrictions'] ?? '';
            $message = $data['message'] ?? '';

            if (empty($guest_code) || !in_array($status, ['attending', 'not-attending'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                exit();
            }

            // Get the guest
            $stmt = $pdo->prepare("SELECT * FROM rsvp_guests WHERE unique_code = ?");
            $stmt->execute([$guest_code]);
            $guest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$guest) {
                echo json_encode(['success' => false, 'message' => 'Guest not found']);
                exit();
            }

            // Update guest response
            $stmt = $pdo->prepare("
                UPDATE rsvp_guests 
                SET status = ?, party_size = ?, dietary_restrictions = ?, responded_at = NOW()
                WHERE unique_code = ?
            ");
            $stmt->execute([$status, $party_size, $dietary, $guest_code]);

            // Save message if provided
            if (!empty($message)) {
                $stmt = $pdo->prepare("
                    INSERT INTO rsvp_messages (guest_id, user_id, message) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$guest['id'], $guest['user_id'], $message]);
            }

            echo json_encode(['success' => true, 'message' => 'Thank you for your response!']);
            break;

        // ========== AUTHENTICATED USER ACTIONS ==========
        default:
            // Check if user is logged in for other actions
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                exit();
            }
            $user_id = $_SESSION['user_id'];

            switch ($action) {
                case 'add_guest':
                    $name = trim($data['name'] ?? '');
                    $email = trim($data['email'] ?? '');
                    $phone = trim($data['phone'] ?? '');
                    $status = $data['status'] ?? 'pending';
                    $notes = trim($data['notes'] ?? '');
                    $event_id = $data['event_id'] ?? null;

                    if (empty($name)) {
                        echo json_encode(['success' => false, 'message' => 'Guest name is required']);
                        exit();
                    }

                    $unique_code = generateUniqueCode();

                    $stmt = $pdo->prepare("
                        INSERT INTO rsvp_guests (user_id, event_id, unique_code, name, email, phone, status, notes)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$user_id, $event_id, $unique_code, $name, $email, $phone, $status, $notes]);

                    $guest_id = $pdo->lastInsertId();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Guest added successfully',
                        'guest' => [
                            'id' => $guest_id,
                            'unique_code' => $unique_code,
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone,
                            'status' => $status,
                            'notes' => $notes
                        ]
                    ]);
                    break;

                case 'add_bulk_guests':
                    $names = $data['names'] ?? [];
                    $status = $data['status'] ?? 'pending';
                    $event_id = $data['event_id'] ?? null;

                    if (empty($names)) {
                        echo json_encode(['success' => false, 'message' => 'No guest names provided']);
                        exit();
                    }

                    $added = [];
                    $stmt = $pdo->prepare("
                        INSERT INTO rsvp_guests (user_id, event_id, unique_code, name, status)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    foreach ($names as $name) {
                        $name = trim($name);
                        if (!empty($name)) {
                            $unique_code = generateUniqueCode();
                            $stmt->execute([$user_id, $event_id, $unique_code, $name, $status]);
                            $added[] = [
                                'id' => $pdo->lastInsertId(),
                                'name' => $name,
                                'unique_code' => $unique_code
                            ];
                        }
                    }

                    echo json_encode([
                        'success' => true,
                        'message' => count($added) . ' guests added successfully',
                        'guests' => $added
                    ]);
                    break;

                case 'update_guest':
                    $guest_id = intval($data['guest_id'] ?? 0);
                    $name = trim($data['name'] ?? '');
                    $email = trim($data['email'] ?? '');
                    $phone = trim($data['phone'] ?? '');
                    $status = $data['status'] ?? 'pending';
                    $notes = trim($data['notes'] ?? '');

                    if ($guest_id <= 0 || empty($name)) {
                        echo json_encode(['success' => false, 'message' => 'Invalid data']);
                        exit();
                    }

                    $stmt = $pdo->prepare("
                        UPDATE rsvp_guests 
                        SET name = ?, email = ?, phone = ?, status = ?, notes = ?
                        WHERE id = ? AND user_id = ?
                    ");
                    $stmt->execute([$name, $email, $phone, $status, $notes, $guest_id, $user_id]);

                    echo json_encode(['success' => true, 'message' => 'Guest updated successfully']);
                    break;

                case 'delete_guest':
                    $guest_id = intval($data['guest_id'] ?? 0);

                    $stmt = $pdo->prepare("DELETE FROM rsvp_guests WHERE id = ? AND user_id = ?");
                    $stmt->execute([$guest_id, $user_id]);

                    echo json_encode(['success' => true, 'message' => 'Guest deleted successfully']);
                    break;

                case 'delete_all_guests':
                    $stmt = $pdo->prepare("DELETE FROM rsvp_guests WHERE user_id = ?");
                    $stmt->execute([$user_id]);

                    echo json_encode(['success' => true, 'message' => 'All guests deleted']);
                    break;

                case 'get_guests':
                    $event_id = $data['event_id'] ?? null;

                    if ($event_id) {
                        $stmt = $pdo->prepare("
                            SELECT * FROM rsvp_guests 
                            WHERE user_id = ? AND event_id = ?
                            ORDER BY created_at DESC
                        ");
                        $stmt->execute([$user_id, $event_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            SELECT * FROM rsvp_guests 
                            WHERE user_id = ?
                            ORDER BY created_at DESC
                        ");
                        $stmt->execute([$user_id]);
                    }
                    $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Get stats
                    $total = count($guests);
                    $attending = count(array_filter($guests, fn($g) => $g['status'] === 'attending'));
                    $pending = count(array_filter($guests, fn($g) => $g['status'] === 'pending'));
                    $notAttending = count(array_filter($guests, fn($g) => $g['status'] === 'not-attending'));

                    echo json_encode([
                        'success' => true,
                        'guests' => $guests,
                        'stats' => [
                            'total' => $total,
                            'attending' => $attending,
                            'pending' => $pending,
                            'not_attending' => $notAttending
                        ]
                    ]);
                    break;

                case 'get_guest_qr':
                    $guest_id = intval($data['guest_id'] ?? 0);

                    $stmt = $pdo->prepare("SELECT unique_code, name FROM rsvp_guests WHERE id = ? AND user_id = ?");
                    $stmt->execute([$guest_id, $user_id]);
                    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$guest) {
                        echo json_encode(['success' => false, 'message' => 'Guest not found']);
                        exit();
                    }

                    // Generate QR code URL using local IP if localhost
                    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
                    $host = $_SERVER['HTTP_HOST'];
                    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                        $host = gethostbyname(gethostname());
                    }

                    $rsvp_url = $protocol . "://" . $host . dirname($_SERVER['REQUEST_URI']) . "/guest_rsvp.php?code=" . $guest['unique_code'];

                    echo json_encode([
                        'success' => true,
                        'guest_name' => $guest['name'],
                        'unique_code' => $guest['unique_code'],
                        'rsvp_url' => $rsvp_url
                    ]);
                    break;

                // === EVENT MANAGEMENT ===
                case 'update_event':
                    $event_name = trim($data['event_name'] ?? 'My Wedding');
                    $event_date = $data['event_date'] ?? null;
                    $event_location = trim($data['event_location'] ?? '');
                    $rsvp_deadline = $data['rsvp_deadline'] ?? null;

                    // Update user's latest event
                    $stmt = $pdo->prepare("
                        UPDATE wedding_events 
                        SET event_name = ?, event_date = ?, event_location = ?, rsvp_deadline = ?
                        WHERE user_id = ? 
                        ORDER BY created_at DESC LIMIT 1
                    ");
                    $stmt->execute([$event_name, $event_date, $event_location, $rsvp_deadline, $user_id]);

                    if ($stmt->rowCount() === 0) {
                        // If no event exists, create one
                        $rsvp_code = generateUniqueCode(16);
                        $stmt = $pdo->prepare("
                            INSERT INTO wedding_events (user_id, event_name, event_date, event_location, rsvp_code, rsvp_deadline)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$user_id, $event_name, $event_date, $event_location, $rsvp_code, $rsvp_deadline]);
                    }

                    echo json_encode(['success' => true, 'message' => 'Event updated']);
                    break;

                case 'create_event':
                    $event_name = trim($data['event_name'] ?? 'My Wedding');
                    $event_date = $data['event_date'] ?? null;
                    $event_location = trim($data['event_location'] ?? '');
                    $rsvp_deadline = $data['rsvp_deadline'] ?? null;
                    $max_guests = intval($data['max_guests'] ?? 0);

                    $rsvp_code = generateUniqueCode(16);

                    $stmt = $pdo->prepare("
                        INSERT INTO wedding_events (user_id, event_name, event_date, event_location, rsvp_code, rsvp_deadline, max_guests)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$user_id, $event_name, $event_date, $event_location, $rsvp_code, $rsvp_deadline, $max_guests]);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Event created',
                        'event_id' => $pdo->lastInsertId(),
                        'rsvp_code' => $rsvp_code
                    ]);
                    break;

                case 'get_events':
                    $stmt = $pdo->prepare("SELECT * FROM wedding_events WHERE user_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$user_id]);
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode(['success' => true, 'events' => $events]);
                    break;

                case 'get_user_rsvp_code':
                    // Get or create the user's main RSVP link
                    $stmt = $pdo->prepare("SELECT * FROM wedding_events WHERE user_id = ? ORDER BY created_at ASC LIMIT 1");
                    $stmt->execute([$user_id]);
                    $event = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$event) {
                        // Create a default event for the user
                        $rsvp_code = generateUniqueCode(16);
                        $stmt = $pdo->prepare("
                            INSERT INTO wedding_events (user_id, event_name, rsvp_code)
                            VALUES (?, 'My Wedding', ?)
                        ");
                        $stmt->execute([$user_id, $rsvp_code]);
                        $event_id = $pdo->lastInsertId();
                    } else {
                        $rsvp_code = $event['rsvp_code'];
                        $event_id = $event['id'];
                    }

                    // Generate QR code URL
                    if (defined('BASE_URL') && BASE_URL !== '') {
                        $rsvp_url = BASE_URL . "/guest_rsvp.php?event=" . $rsvp_code;
                    } else {
                        // Auto-detect if no BASE_URL set
                        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
                        $host = $_SERVER['HTTP_HOST'];
                        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                            $host = gethostbyname(gethostname());
                        }
                        $rsvp_url = $protocol . "://" . $host . dirname($_SERVER['REQUEST_URI']) . "/guest_rsvp.php?event=" . $rsvp_code;
                    }

                    echo json_encode([
                        'success' => true,
                        'rsvp_code' => $rsvp_code,
                        'rsvp_url' => $rsvp_url,
                        'event_id' => $event_id
                    ]);
                    break;

                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
            }
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>