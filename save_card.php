<?php
/**
 * Save Card API
 * Handles saving card customizations to user_card_selection table
 */

require_once 'config.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Extract data
    $bride_name = trim($input['brideName'] ?? '');
    $groom_name = trim($input['groomName'] ?? '');
    $wedding_date = $input['weddingDate'] ?? null;
    $location = trim($input['location'] ?? '');
    $card_template_id = $input['cardTemplateId'] ?? null;
    $custom_text = $input['customText'] ?? '';
    $card_design_json = isset($input['cardDesign']) ? json_encode($input['cardDesign']) : null;

    // Validate required fields
    if (empty($bride_name) || empty($groom_name)) {
        echo json_encode(['success' => false, 'message' => 'Bride and Groom names are required']);
        exit();
    }

    try {
        // Check if user already has a card selection
        $stmt = $pdo->prepare("SELECT id FROM user_card_selection WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE user_card_selection 
                SET card_template_id = ?, 
                    bride_name = ?, 
                    groom_name = ?, 
                    wedding_date = ?, 
                    location = ?, 
                    custom_text = ?, 
                    card_design_json = ?,
                    selected_at = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            $stmt->execute([
                $card_template_id,
                $bride_name,
                $groom_name,
                $wedding_date,
                $location,
                $custom_text,
                $card_design_json,
                $user_id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Card updated successfully!',
                'action' => 'updated'
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO user_card_selection 
                (user_id, card_template_id, bride_name, groom_name, wedding_date, location, custom_text, card_design_json) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $card_template_id,
                $bride_name,
                $groom_name,
                $wedding_date,
                $location,
                $custom_text,
                $card_design_json
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Card saved successfully!',
                'action' => 'created'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user's saved card
    try {
        $stmt = $pdo->prepare("SELECT * FROM user_card_selection WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($card) {
            // Decode JSON if exists
            if ($card['card_design_json']) {
                $card['card_design'] = json_decode($card['card_design_json'], true);
            }

            echo json_encode([
                'success' => true,
                'card' => $card
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No saved card found'
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>