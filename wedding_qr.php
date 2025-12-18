<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// إعدادات قاعدة البيانات
$host = 'localhost';
$dbname = 'wedding_db';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'submit_wedding_form':
        submitWeddingForm($pdo);
        break;
    case 'check_user_registration':
        checkUserRegistration($pdo);
        break;
    case 'get_user_package':
        getUserPackage($pdo);
        break;
    case 'create_wedding':
        createWedding($pdo);
        break;
    case 'get_wedding':
        getWedding($pdo);
        break;
    case 'submit_rsvp':
        submitRSVP($pdo);
        break;
    case 'get_guests':
        getGuests($pdo);
        break;
    case 'update_wedding':
        updateWedding($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}



// تسجيل بيانات الزفاف من النموذج
function submitWeddingForm($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    $fullName = trim($data['fullName'] ?? '');
    $email = trim($data['email'] ?? '');
    $package = trim($data['package'] ?? '');
    $weddingDate = trim($data['weddingDate'] ?? '');
    $notes = trim($data['notes'] ?? '');

    if (empty($fullName) || empty($email) || empty($package)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        return;
    }

    try {
        // التحقق من وجود المستخدم
        $stmt = $pdo->prepare("SELECT id FROM weddings WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing) {
            // تحديث البيانات الموجودة
            $stmt = $pdo->prepare("UPDATE weddings SET full_name = ?, package = ?, wedding_date = ?, notes = ?, updated_at = NOW() WHERE email = ?");
            $stmt->execute([$fullName, $package, $weddingDate, $notes, $email]);
            $weddingId = $existing['id'];
        } else {
            // إضافة مستخدم جديد
            $stmt = $pdo->prepare("INSERT INTO weddings (full_name, email, package, wedding_date, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$fullName, $email, $package, $weddingDate, $notes]);
            $weddingId = $pdo->lastInsertId();
        }

        // حفظ بيانات الجلسة
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_package'] = $package;
        $_SESSION['wedding_id'] = $weddingId;

        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'weddingId' => $weddingId,
            'package' => $package
        ]);

    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// التحقق من تسجيل المستخدم
function checkUserRegistration($pdo) {
    $email = $_GET['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'registered' => false]);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT package, full_name, wedding_date FROM weddings WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode([
                'success' => true,
                'registered' => true,
                'package' => $result['package'],
                'fullName' => $result['full_name'],
                'weddingDate' => $result['wedding_date']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'registered' => false
            ]);
        }

    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// جلب باقة المستخدم
function getUserPackage($pdo) {
    $email = $_GET['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'registered' => false]);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM weddings WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode([
                'success' => true,
                'registered' => true,
                'package' => $user['package'],
                'fullName' => $user['full_name'],
                'weddingDate' => $user['wedding_date']
            ]);
        } else {
            echo json_encode(['success' => false, 'registered' => false]);
        }

    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// === وظائف QR Code والضيوف ===

// إنشاء حفل زفاف جديد للبطاقات
function createWedding($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    $brideName = $data['brideName'] ?? 'Bride';
    $groomName = $data['groomName'] ?? 'Groom';
    $brideEmail = $data['brideEmail'] ?? '';
    $groomEmail = $data['groomEmail'] ?? '';
    $weddingDate = $data['weddingDate'] ?? date('Y-m-d');
    $location = $data['location'] ?? 'London';

    try {
        $stmt = $pdo->prepare("INSERT INTO weddings (bride_name, groom_name, bride_email, groom_email, wedding_date, location, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$brideName, $groomName, $brideEmail, $groomEmail, $weddingDate, $location]);

        $weddingId = $pdo->lastInsertId();

        // توليد رابط QR Code
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $qrUrl = $protocol . $host . '/qr.html?weddingId=' . $weddingId;

        // تحديث QR Code
        $stmt = $pdo->prepare("UPDATE weddings SET qr_code = ? WHERE id = ?");
        $stmt->execute([$qrUrl, $weddingId]);

        echo json_encode([
            'success' => true,
            'weddingId' => $weddingId,
            'qrCode' => $qrUrl,
            'message' => 'Wedding created successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error creating wedding']);
    }
}

// تحديث معلومات الزفاف
function updateWedding($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    $weddingId = $data['weddingId'] ?? 0;
    $brideName = $data['brideName'] ?? '';
    $groomName = $data['groomName'] ?? '';
    $brideEmail = $data['brideEmail'] ?? '';
    $groomEmail = $data['groomEmail'] ?? '';
    $weddingDate = $data['weddingDate'] ?? '';
    $location = $data['location'] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE weddings SET bride_name = ?, groom_name = ?, bride_email = ?, groom_email = ?, wedding_date = ?, location = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$brideName, $groomName, $brideEmail, $groomEmail, $weddingDate, $location, $weddingId]);

        echo json_encode([
            'success' => true,
            'message' => 'Wedding updated successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Update error']);
    }
}

// جلب معلومات الزفاف
function getWedding($pdo) {
    $weddingId = $_GET['weddingId'] ?? 0;

    try {
        $stmt = $pdo->prepare("SELECT * FROM weddings WHERE id = ?");
        $stmt->execute([$weddingId]);
        $wedding = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($wedding) {
            echo json_encode([
                'success' => true,
                'wedding' => $wedding
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Wedding not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// تسجيل RSVP من الضيف
function submitRSVP($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    $weddingId = $data['weddingId'] ?? 0;
    $guestName = $data['guestName'] ?? '';
    $guestEmail = $data['guestEmail'] ?? '';
    $guestPhone = $data['guestPhone'] ?? '';
    $status = $data['status'] ?? 'pending';
    $notes = $data['notes'] ?? '';

    if (empty($guestName)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your name']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM guests WHERE wedding_id = ? AND name = ?");
        $stmt->execute([$weddingId, $guestName]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE guests SET email = ?, phone = ?, status = ?, notes = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$guestEmail, $guestPhone, $status, $notes, $existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO guests (wedding_id, name, email, phone, status, notes, source, created_at) VALUES (?, ?, ?, ?, ?, ?, 'qr_scan', NOW())");
            $stmt->execute([$weddingId, $guestName, $guestEmail, $guestPhone, $status, $notes]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'RSVP submitted successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// جلب قائمة الضيوف
function getGuests($pdo) {
    $weddingId = $_GET['weddingId'] ?? 0;

    try {
        $stmt = $pdo->prepare("SELECT * FROM guests WHERE wedding_id = ? ORDER BY created_at DESC");
        $stmt->execute([$weddingId]);
        $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = count($guests);
        $attending = count(array_filter($guests, fn($g) => $g['status'] === 'attending'));
        $notAttending = count(array_filter($guests, fn($g) => $g['status'] === 'not-attending'));
        $pending = count(array_filter($guests, fn($g) => $g['status'] === 'pending'));

        echo json_encode([
            'success' => true,
            'guests' => $guests,
            'stats' => [
                'total' => $total,
                'attending' => $attending,
                'notAttending' => $notAttending,
                'pending' => $pending
            ]
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>