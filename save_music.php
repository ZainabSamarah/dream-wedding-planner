<?php
/**
 * Save Music & Entertainment Selections
 * API endpoint to save user's music preferences and selections
 */

require_once 'config.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

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

$user_id = $_SESSION['user_id'];
$event_time = $data['event_time'] ?? '';
$vibe = $data['vibe'] ?? '';
$duration = intval($data['duration'] ?? 0);
$special_requests = $data['special_requests'] ?? '';
$selections = $data['selections'] ?? [];

try {
    $pdo->beginTransaction();

    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_music_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_time VARCHAR(100),
            vibe VARCHAR(100),
            duration INT DEFAULT 0,
            special_requests TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_music_selections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_name VARCHAR(255) NOT NULL,
            item_type VARCHAR(100) NOT NULL,
            price_range VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Check if user already has preferences
    $stmt = $pdo->prepare("SELECT id FROM user_music_preferences WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing preferences
        $stmt = $pdo->prepare("
            UPDATE user_music_preferences 
            SET event_time = ?, vibe = ?, duration = ?, special_requests = ?, updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([$event_time, $vibe, $duration, $special_requests, $user_id]);
    } else {
        // Insert new preferences
        $stmt = $pdo->prepare("
            INSERT INTO user_music_preferences (user_id, event_time, vibe, duration, special_requests)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $event_time, $vibe, $duration, $special_requests]);
    }

    // Delete existing selections for this user
    $stmt = $pdo->prepare("DELETE FROM user_music_selections WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Insert new selections
    $selection_ids = [];
    if (!empty($selections)) {
        $stmt = $pdo->prepare("
            INSERT INTO user_music_selections (user_id, item_name, item_type, price_range)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($selections as $selection) {
            $item_name = $selection['name'] ?? '';
            $item_type = $selection['type'] ?? '';
            $price_range = $selection['price'] ?? '';

            if (!empty($item_name)) {
                $stmt->execute([$user_id, $item_name, $item_type, $price_range]);
                $selection_ids[] = $pdo->lastInsertId();
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Playlist saved successfully!',
        'selection_ids' => $selection_ids
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>