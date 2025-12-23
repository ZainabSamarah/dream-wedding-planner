<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_messages':
            // Get messages where user is recipient (replies from owners)
            $sql = "SELECT m.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                    u.role as sender_role
                    FROM messages m
                    LEFT JOIN users u ON m.from_user_id = u.id
                    WHERE m.to_user_id = ?
                    ORDER BY m.sent_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'get_sent_messages':
            // Get messages user has sent
            $sql = "SELECT m.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as recipient_name
                    FROM messages m
                    LEFT JOIN users u ON m.to_user_id = u.id
                    WHERE m.from_user_id = ?
                    ORDER BY m.sent_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'send_message':
            $content = $_POST['message'] ?? '';
            if (empty(trim($content))) {
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                break;
            }

            // Get all owners
            $owners = $pdo->query("SELECT id FROM users WHERE role = 'owner'")->fetchAll(PDO::FETCH_COLUMN);

            if (empty($owners)) {
                echo json_encode(['success' => false, 'message' => 'No owners available']);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO messages (from_user_id, to_user_id, content, is_read, sent_at) VALUES (?, ?, ?, 0, NOW())");

            foreach ($owners as $ownerId) {
                $stmt->execute([$userId, $ownerId, $content]);
            }

            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
            break;

        case 'mark_read':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND to_user_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
            break;

        case 'get_unread_count':
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE to_user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
            $count = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'count' => $count]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>