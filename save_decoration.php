<?php
/**
 * Save Decoration Data
 * API endpoint to manage user decoration choices
 */

require_once 'config.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

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

try {
    switch ($action) {
        case 'save_decoration':
            $theme = $data['theme'] ?? null;
            $flowers = $data['flowers'] ?? null;
            $lighting = $data['lighting'] ?? null;
            $centerpieces = $data['centerpieces'] ?? null;
            $custom_notes = $data['custom_notes'] ?? null;

            // Check if record exists
            $stmt = $pdo->prepare("SELECT id FROM user_decorations WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Determine what to update based on provided fields (partial updates allowed)
                $updates = [];
                $params = [];

                if (isset($data['theme'])) {
                    $updates[] = "theme = ?";
                    $params[] = $theme;
                }
                if (isset($data['flowers'])) {
                    $updates[] = "flowers = ?";
                    $params[] = $flowers;
                }
                if (isset($data['lighting'])) {
                    $updates[] = "lighting = ?";
                    $params[] = $lighting;
                }
                if (isset($data['centerpieces'])) {
                    $updates[] = "centerpieces = ?";
                    $params[] = $centerpieces;
                }
                if (isset($data['custom_notes'])) {
                    $updates[] = "custom_notes = ?";
                    $params[] = $custom_notes;
                } // Allow saving empty string

                if (empty($updates)) {
                    echo json_encode(['success' => true, 'message' => 'No changes provided']);
                    exit;
                }

                $params[] = $user_id; // For WHERE clause
                $sql = "UPDATE user_decorations SET " . implode(", ", $updates) . " WHERE user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

            } else {
                // Insert new recorrd
                $stmt = $pdo->prepare("
                    INSERT INTO user_decorations (user_id, theme, flowers, lighting, centerpieces, custom_notes)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $theme, $flowers, $lighting, $centerpieces, $custom_notes]);
            }

            echo json_encode(['success' => true, 'message' => 'Decoration preferences saved']);
            break;

        case 'get_decoration':
            $stmt = $pdo->prepare("SELECT * FROM user_decorations WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $decoration = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($decoration) {
                echo json_encode(['success' => true, 'decoration' => $decoration]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No decoration found']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>