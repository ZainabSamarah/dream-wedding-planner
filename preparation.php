<?php
global $pdo;
require_once __DIR__ . '/config.php'; // __DIR__ يضمن المسار الصحيح

// لو مش مسجل دخول، يرجع للوجن
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fullName = trim($input['fullName'] ?? '');
    $email = trim(strtolower($input['email'] ?? ''));
    $packageName = trim($input['package'] ?? '');
    $weddingDate = $input['weddingDate'] ?? null;
    $notes = trim($input['notes'] ?? '');

    if (empty($fullName) || empty($email) || empty($packageName)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    try {
        // Try DB Connection
        if ($pdo) {
            // Get package ID
            $stmt = $pdo->prepare("SELECT id FROM packages WHERE name = ?");
            $stmt->execute([$packageName]);
            $package = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($package) {
                $package_id = $package['id'];

                // Check/Update/Insert user_packages
                $stmt = $pdo->prepare("SELECT id FROM user_packages WHERE user_id = ?");
                $stmt->execute([$user_id]);
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("UPDATE user_packages SET full_name = ?, email = ?, package_id = ?, wedding_date = ?, notes = ? WHERE user_id = ?");
                    $stmt->execute([$fullName, $email, $package_id, $weddingDate, $notes, $user_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO user_packages (user_id, full_name, email, package_id, wedding_date, notes) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $fullName, $email, $package_id, $weddingDate, $notes]);
                }
                echo json_encode(['success' => true, 'message' => 'Details saved to database!']);
                exit();
            }
        }
        throw new Exception("Database connection failed or package not found.");
    } catch (Exception $e) {
        // Fallback: Save to JSON file
        $backupFile = 'user_packages_backup.json';
        $currentData = file_exists($backupFile) ? json_decode(file_get_contents($backupFile), true) : [];
        if (!is_array($currentData))
            $currentData = [];

        $currentData[$user_id] = [
            'user_id' => $user_id,
            'full_name' => $fullName,
            'email' => $email,
            'package_name' => $packageName,
            'wedding_date' => $weddingDate,
            'notes' => $notes,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        file_put_contents($backupFile, json_encode($currentData, JSON_PRETTY_PRINT));

        // Return success even if DB failed
        echo json_encode(['success' => true, 'message' => 'Saved locally (Offline Mode). Redirecting...', 'offline' => true]);
    }
    exit();
}

// جلب الـ package المختار من الـ URL عشان يعبي الـ select تلقائي
$selectedPackage = $_GET['package'] ?? '';

// Fetch packages from database
$packagesFromDB = [];
try {
    if ($pdo) {
        $stmt = $pdo->query("SELECT id, name FROM packages ORDER BY id");
        $packagesFromDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // If DB fails, use fallback
    $packagesFromDB = [];
}

// Package display mapping (code => [display_name, price])
$packageDisplayMap = [
    'reg' => ['Regular Package', 5000],
    'med' => ['Medium Bouquet', 6500],
    'lux' => ['Luxury Bouquet', 8000]
];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Preparation – WEDÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--green-pale);
            color: var(--green-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 60px 50px;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            max-width: 650px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 52px;
            color: var(--green-dark);
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 50px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 30px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--green-medium);
            font-size: 16px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 16px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--green-light);
            box-shadow: 0 0 0 3px rgba(145, 172, 143, 0.2);
        }

        textarea {
            height: 130px;
            resize: vertical;
        }

        .btn-submit {
            background: var(--green-light);
            color: white;
            padding: 18px 50px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            width: 100%;
        }

        .btn-submit:hover {
            background: var(--green-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .toast {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 18px 35px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transition: opacity 0.4s;
            z-index: 1000;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .toast.show {
            opacity: 1;
        }

        .toast.error {
            background: #d32f2f;
        }

        .toast i {
            font-size: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Wedding Preparation</h1>
        <p class="subtitle">Start planning your dream wedding — share your details and we'll guide you every step of the
            way.</p>

        <form id="weddingForm">
            <div class="form-group">
                <label for="fullName">Full Name *</label>
                <input type="text" id="fullName" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="package">Select Package *</label>
                <select id="package" required>
                    <option value="">Choose your package...</option>
                    <?php
                    // Display packages from database
                    if (!empty($packagesFromDB)) {
                        foreach ($packagesFromDB as $pkg) {
                            $code = $pkg['name']; // e.g., 'reg', 'med', 'lux'
                            $displayInfo = $packageDisplayMap[$code] ?? [$code, 0];
                            $displayName = $displayInfo[0];
                            $price = $displayInfo[1];
                            $isSelected = ($selectedPackage === $code || $selectedPackage === $displayName) ? 'selected' : '';
                            echo "<option value='$code' $isSelected>$displayName – $$price</option>";
                        }
                    } else {
                        // Fallback if DB is empty
                        echo '<option value="reg">Regular Package – $5000</option>';
                        echo '<option value="med">Medium Bouquet – $6500</option>';
                        echo '<option value="lux">Luxury Bouquet – $8000</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="weddingDate">Preferred Wedding Date</label>
                <input type="date" id="weddingDate">
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes"
                    placeholder="Any special requests, themes, or details you'd like to share..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Your Details</button>
        </form>
    </div>

    <div id="toast" class="toast">
        <i id="toastIcon" class="fas fa-check-circle"></i>
        <span id="toastMessage"></span>
    </div>

    <script>
        document.getElementById('weddingForm').onsubmit = async function (e) {
            e.preventDefault();

            const data = {
                fullName: document.getElementById('fullName').value.trim(),
                email: document.getElementById('email').value.trim().toLowerCase(),
                package: document.getElementById('package').value,
                weddingDate: document.getElementById('weddingDate').value || null,
                notes: document.getElementById('notes').value.trim()
            };

            try {
                const res = await fetch('preparation.php?action=submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (result.success) {
                    showToast('Submitted successfully! Redirecting to services...', 'success');
                    setTimeout(() => {
                        window.location.href = 'services.php';
                    }, 2000);
                } else {
                    showToast(result.message || 'Something went wrong. Please try again.', 'error');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                // If the response wasn't JSON (e.g., PHP error HTML), show a generic message or try to read text
                showToast('Connection error or server issue. Check console for details.', 'error');
            }
        };

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').textContent = msg;
            toast.classList.toggle('error', type === 'error');
            document.getElementById('toastIcon').className = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    </script>

</body>

</html>