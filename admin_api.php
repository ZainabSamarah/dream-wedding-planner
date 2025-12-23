<?php
require_once 'config.php';

header('Content-Type: application/json');

// Session check
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'owner')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        // --- USERS ---
        case 'get_users':
            $data = $pdo->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_user':
            $id = $_POST['id'] ?? 0;
            $fname = $_POST['first_name'] ?? '';
            $lname = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $pass = $_POST['password'] ?? '';

            if ($id > 0) {
                // Update
                if (!empty($pass)) {
                    $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, password=? WHERE id=?");
                    $stmt->execute([$fname, $lname, $email, $role, password_hash($pass, PASSWORD_DEFAULT), $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE id=?");
                    $stmt->execute([$fname, $lname, $email, $role, $id]);
                }
            } else {
                // Add
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, role, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$fname, $lname, $email, $role, password_hash($pass, PASSWORD_DEFAULT)]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_user':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'owner'");
            $success = $stmt->execute([$id]);
            echo json_encode(['success' => $success]);
            break;

        // --- PACKAGES ---
        case 'get_packages':
            $data = $pdo->query("SELECT * FROM packages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_package':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE packages SET name=? WHERE id=?");
                $stmt->execute([$name, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO packages (name) VALUES (?)");
                $stmt->execute([$name]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_package':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- SERVICES ---
        case 'get_services':
            $data = $pdo->query("SELECT * FROM services ORDER BY category, name")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_service':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $cat = $_POST['category'] ?? 'other';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE services SET name=?, description=?, price=?, category=? WHERE id=?");
                $stmt->execute([$name, $desc, $price, $cat, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO services (name, description, price, category) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $desc, $price, $cat]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_service':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- FOOD & CAKES ---
        case 'get_food':
            $data = $pdo->query("SELECT * FROM food_menu ORDER BY package_type, name")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_food':
            $id = $_POST['id'] ?? 0;
            $pkg = $_POST['package_type'] ?? 'regular';
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';
            $img = $_POST['image_url'] ?? '';
            $cat = $_POST['category'] ?? 'Main Dishes';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE food_menu SET package_type=?, name=?, description=?, image_url=?, category=? WHERE id=?");
                $stmt->execute([$pkg, $name, $desc, $img, $cat, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO food_menu (package_type, name, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$pkg, $name, $desc, $img, $cat]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_food':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM food_menu WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'get_cakes':
            $data = $pdo->query("SELECT * FROM cake_menu ORDER BY package_type, name")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_cake':
            $id = $_POST['id'] ?? 0;
            $pkg = $_POST['package_type'] ?? 'regular';
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';
            $img = $_POST['image_url'] ?? '';
            $cat = $_POST['category'] ?? 'Cakes';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE cake_menu SET package_type=?, name=?, description=?, image_url=?, category=? WHERE id=?");
                $stmt->execute([$pkg, $name, $desc, $img, $cat, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cake_menu (package_type, name, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$pkg, $name, $desc, $img, $cat]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_cake':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM cake_menu WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- CARDS ---
        case 'get_cards':
            $data = $pdo->query("SELECT * FROM card_templates ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_card':
            $id = $_POST['id'] ?? 0;
            $pkg = $_POST['package_type'] ?? 'regular';
            $name = $_POST['template_name'] ?? '';
            $img = $_POST['preview_image'] ?? '';
            $json = $_POST['design_json'] ?? '';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE card_templates SET package_type=?, template_name=?, preview_image=?, design_json=? WHERE id=?");
                $stmt->execute([$pkg, $name, $img, $json, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO card_templates (package_type, template_name, preview_image, design_json) VALUES (?, ?, ?, ?)");
                $stmt->execute([$pkg, $name, $img, $json]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_card':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM card_templates WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- CONTENT ---
        case 'get_tips':
            $data = $pdo->query("SELECT * FROM planning_tips ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_tip':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE planning_tips SET title=?, content=? WHERE id=?");
                $stmt->execute([$title, $content, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO planning_tips (title, content) VALUES (?, ?)");
                $stmt->execute([$title, $content]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_tip':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM planning_tips WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'get_vendors':
            $data = $pdo->query("SELECT * FROM vendors ORDER BY category, name")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'save_vendor':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $cat = $_POST['category'] ?? '';
            $info = $_POST['contact_info'] ?? '';
            $desc = $_POST['description'] ?? '';
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE vendors SET name=?, category=?, contact_info=?, description=? WHERE id=?");
                $stmt->execute([$name, $cat, $info, $desc, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO vendors (name, category, contact_info, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $cat, $info, $desc]);
            }
            echo json_encode(['success' => true]);
            break;
        case 'delete_vendor':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM vendors WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- BOOKINGS / SELECTIONS ---
        case 'get_selections':
            $sql = "SELECT up.*, 
                    (SELECT GROUP_CONCAT(fm.name SEPARATOR ', ') FROM user_food_selections ufs JOIN food_menu fm ON ufs.food_menu_id = fm.id WHERE ufs.user_id = up.user_id) as selected_food,
                    (SELECT GROUP_CONCAT(cm.name SEPARATOR ', ') FROM user_cake_selections ucs JOIN cake_menu cm ON ucs.cake_id = cm.id WHERE ucs.user_id = up.user_id) as selected_cakes
                    FROM user_packages up 
                    ORDER BY up.created_at DESC";
            $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        case 'delete_selection':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM user_packages WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // --- MESSAGES ---
        case 'get_messages':
            // Get messages sent to the current logged-in owner/admin
            $userId = $_SESSION['user_id'] ?? 0;
            $sql = "SELECT m.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as sender_name
                    FROM messages m
                    LEFT JOIN users u ON m.from_user_id = u.id
                    WHERE m.to_user_id = ?
                    ORDER BY m.sent_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'mark_read':
            $id = $_POST['id'] ?? 0;
            $userId = $_SESSION['user_id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND to_user_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_message':
            $id = $_POST['id'] ?? 0;
            $userId = $_SESSION['user_id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND to_user_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
            break;

        case 'send_reply':
            $messageId = $_POST['message_id'] ?? 0;
            $replyContent = $_POST['reply'] ?? '';
            $ownerId = $_SESSION['user_id'] ?? 0;

            if (empty($replyContent)) {
                echo json_encode(['success' => false, 'message' => 'Reply cannot be empty']);
                break;
            }

            // Get the original message to find the sender
            $stmt = $pdo->prepare("SELECT from_user_id, guest_email FROM messages WHERE id = ? AND to_user_id = ?");
            $stmt->execute([$messageId, $ownerId]);
            $originalMsg = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$originalMsg) {
                echo json_encode(['success' => false, 'message' => 'Original message not found']);
                break;
            }

            // Insert reply - if original sender was a guest, we still store it
            // If original sender was logged in user, send to them
            $toUserId = $originalMsg['from_user_id'];
            $guestEmail = $originalMsg['from_user_id'] ? null : $originalMsg['guest_email'];

            if ($toUserId) {
                // Registered user - insert as message
                $stmt = $pdo->prepare("INSERT INTO messages (from_user_id, to_user_id, content, is_read, sent_at) VALUES (?, ?, ?, 0, NOW())");
                $stmt->execute([$ownerId, $toUserId, $replyContent]);
            } else {
                // Guest user - just mark original as replied (could send email in production)
                $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$messageId]);
            }

            // Mark original message as read
            $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
            $stmt->execute([$messageId]);

            echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);
            break;

        case 'send_email':
            $to = $_POST['to'] ?? 'All Users';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $log = "Email sent to $to: $subject\n";
            file_put_contents('mail_log.txt', $log, FILE_APPEND);
            echo json_encode(['success' => true, 'message' => 'Email broadcast logged successfully (Demo Mode)']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>