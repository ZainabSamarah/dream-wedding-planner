<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$action = $input['action'] ?? '';

if ($action === 'create_wedding') {
    $brideName = $input['brideName'] ?? '';
    $groomName = $input['groomName'] ?? '';
    $weddingDate = $input['weddingDate'] ?? null;
    $location = $input['location'] ?? '';
    // card_template_id might be needed if we want to associate with specific card
    // For now we'll assume the user ID is enough to link, or we could add it to the input from JS?
    // The JS currently doesn't send cardId. We'll default to 0 or try to fetch from previous selection if possible, 
    // but the table allows NULL or we just set 1 for now.
    $cardTemplateId = 1; // Default or need to pass from JS

    try {
        $stmt = $pdo->prepare("INSERT INTO user_card_customizations (user_id, card_template_id, bride_name, groom_name, wedding_date, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $cardTemplateId, $brideName, $groomName, $weddingDate, $location]);
        $weddingId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'weddingId' => $weddingId,
            'wedding' => [
                'id' => $weddingId,
                'bride_name' => $brideName,
                'groom_name' => $groomName,
                'wedding_date' => $weddingDate,
                'location' => $location
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($action === 'get_wedding') {
    $weddingId = $_GET['weddingId'] ?? 0;
    try {
        $stmt = $pdo->prepare("SELECT * FROM user_card_customizations WHERE id = ? AND user_id = ?");
        $stmt->execute([$weddingId, $user_id]);
        $wedding = $stmt->fetch();

        if ($wedding) {
            echo json_encode(['success' => true, 'wedding' => $wedding]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Wedding not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>