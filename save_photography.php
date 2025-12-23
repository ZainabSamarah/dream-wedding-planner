<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$host = 'localhost';
$dbname = 'wedding_db'; // Assuming standard db name, user's setup script used 'wedding_db_local' but services.php used 'wedding_db'. I should check services.php again. 
// services.php line 18 says: $db = new PDO("mysql:host=localhost;dbname=wedding_db;charset=utf8mb4", "root", "");
// So I will use 'wedding_db' to match services.php which is the live file.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'save_session') {
    $date = $input['date'] ?? null;
    $time = $input['time'] ?? null;
    $location = trim($input['location'] ?? '');
    $notes = trim($input['notes'] ?? '');

    if (!$date || !$time || !$location) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    try {
        // Check if session exists
        $stmt = $pdo->prepare("SELECT id FROM photography_sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update
            $stmt = $pdo->prepare("UPDATE photography_sessions SET session_date = ?, session_time = ?, location = ?, notes = ? WHERE user_id = ?");
            $stmt->execute([$date, $time, $location, $notes, $user_id]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO photography_sessions (user_id, session_date, session_time, location, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $date, $time, $location, $notes]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving session: ' . $e->getMessage()]);
    }

} elseif ($action === 'get_session') {
    try {
        $stmt = $pdo->prepare("SELECT session_date, session_time, location, notes FROM photography_sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Ensure time is in HH:mm format for input type="time"
            $data['session_time'] = substr($data['session_time'], 0, 5);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => true, 'data' => null]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error loading session']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>