<?php
/**
 * Wedding Preparation API + Form Page
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'wedding_db';

try {
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
sendJsonResponse(false, 'Database connection failed', 500);
}

// API Routes
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit_wedding_form') {
handleSubmitForm($db);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_wedding_data') {
handleGetData($db);
}

// Show the HTML form if no API action
showFormPage();

function handleSubmitForm($db) {
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
sendJsonResponse(false, 'Invalid JSON data', 400);
return;
}

$required = ['fullName', 'email', 'package'];
foreach ($required as $field) {
if (empty($input[$field])) {
sendJsonResponse(false, "Missing required field: $field", 400);
return;
}
}

$fullName = trim($input['fullName']);
$email = trim(strtolower($input['email']));
$package = trim($input['package']);
$phone = trim($input['phone'] ?? '');
$partnerName = trim($input['partnerName'] ?? '');
$weddingDate = $input['weddingDate'] ?? null;
$guestCount = (int)($input['guestCount'] ?? 0);
$venue = trim($input['venue'] ?? '');
$budget = trim($input['budget'] ?? '');
$notes = trim($input['notes'] ?? '');
$services = trim($input['services'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
sendJsonResponse(false, 'Invalid email address', 400);
return;
}

try {
// Check if record exists
$stmt = $db->prepare("SELECT id FROM wedding_preparations WHERE email = ?");
$stmt->execute([$email]);
$exists = $stmt->fetchColumn();

if ($exists) {
// Update
$sql = "UPDATE wedding_preparations SET
fullName = ?, phone = ?, partnerName = ?, package = ?,
weddingDate = ?, guestCount = ?, venue = ?, budget = ?,
notes = ?, services = ?, updated_at = NOW()
WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$fullName, $phone, $partnerName, $package, $weddingDate, $guestCount, $venue, $budget, $notes, $services, $email]);
sendJsonResponse(true, 'Wedding preparation updated successfully', 200, ['action' => 'updated']);
} else {
// Insert
$sql = "INSERT INTO wedding_preparations
(fullName, email, phone, partnerName, package, weddingDate, guestCount, venue, budget, notes, services, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $db->prepare($sql);
$stmt->execute([$fullName, $email, $phone, $partnerName, $package, $weddingDate, $guestCount, $venue, $budget, $notes, $services]);
sendJsonResponse(true, 'Wedding preparation submitted successfully', 201, ['action' => 'created']);
}
} catch (Exception $e) {
sendJsonResponse(false, 'Database error: ' . $e->getMessage(), 500);
}
}

function handleGetData($db) {
$email = trim(strtolower($_GET['email'] ?? ''));
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
sendJsonResponse(false, 'Valid email required', 400);
return;
}

try {
$stmt = $db->prepare("SELECT * FROM wedding_preparations WHERE email = ?");
$stmt->execute([$email]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
sendJsonResponse(true, 'Data retrieved', 200, $data);
} else {
sendJsonResponse(false, 'No data found', 404);
}
} catch (Exception $e) {
sendJsonResponse(false, 'Database error', 500);
}
}

function sendJsonResponse($success, $message, $code = 200, $data = null) {
http_response_code($code);
$res = ['success' => $success, 'message' => $message];
if ($data) $res['data'] = $data;
echo json_encode($res);
exit();
}

function showFormPage() {
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Preparation – WEDÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f7f2, #eaf2e8);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: var(--green-dark);
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.6;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--green-dark);
            font-weight: 600;
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--green-light);
            box-shadow: 0 0 0 3px rgba(145, 172, 143, 0.1);
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--green-medium), var(--green-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(75, 89, 69, 0.3);
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 18px 24px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            border-left: 4px solid #10B981;
        }
        .toast.show { display: flex; }
        .toast.error { border-left-color: #EF4444; }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .container { padding: 30px 20px; }
            h1 { font-size: 36px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Wedding Preparation</h1>
    <p class="subtitle">Start planning your dream wedding — share your details and we'll guide you every step of the way.</p>

    <form id="weddingForm">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" id="fullName" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" id="email" required placeholder="your@email.com">
        </div>

        <div class="form-group">
            <label>Select Package *</label>
            <select id="package" required>
                <option value="">Choose...</option>
                <option value="Regular Package">Regular Package – $5000</option>
                <option value="Medium Bouquet">Medium Bouquet – $6500</option>
                <option value="Luxury Bouquet">Luxury Bouquet – $8000</option>
            </select>
        </div>

        <div class="form-group">
            <label>Preferred Wedding Date</label>
            <input type="date" id="weddingDate">
        </div>

        <div class="form-group">
            <label>Additional Notes</label>
            <textarea id="notes" placeholder="Tell us about your vision, preferences, or any special requests..."></textarea>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i>
            Submit Your Details
        </button>
    </form>
</div>

<div id="toast" class="toast">
    <i id="toastIcon" class="fas fa-check-circle"></i>
    <span id="toastMessage">Success!</span>
</div>

<script>
    // تعبئة الباكيدج من URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedPackage = urlParams.get('package');
    if (selectedPackage) {
        document.getElementById('package').value = selectedPackage;
    }

    document.getElementById('weddingForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            fullName: document.getElementById('fullName').value.trim(),
            email: document.getElementById('email').value.trim().toLowerCase(),
            package: document.getElementById('package').value,
            weddingDate: document.getElementById('weddingDate').value || null,
            notes: document.getElementById('notes').value.trim()
        };

        if (!formData.fullName || !formData.email || !formData.package) {
            showToast('Please fill all required fields', 'error');
            return;
        }

        try {
            const response = await fetch('?action=submit_wedding_form', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                sessionStorage.setItem('userEmail', formData.email);
                sessionStorage.setItem('userPackage', formData.package);
                sessionStorage.setItem('userName', formData.fullName);

                showToast('Submitted successfully! Redirecting...', 'success');

                setTimeout(() => {
                    window.location.href = 'services.php';
                }, 2000);
            } else {
                showToast(result.message || 'Submission failed', 'error');
            }
        } catch (err) {
            showToast('Connection error. Please try again.', 'error');
        }
    });

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toastMessage');
        const icon = document.getElementById('toastIcon');

        msg.textContent = message;
        toast.classList.toggle('error', type === 'error');
        icon.className = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';

        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }
</script>

</body>
</html>
<?php
}
?>