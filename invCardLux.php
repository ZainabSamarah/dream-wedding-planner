<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$package_type = 'luxury';
$cards = [];
$error_msg = "";

// 1. Defined Hardcoded Data from HTML (Corrected Mapping)
$fallback_cards = [
    ['id' => 101, 'name' => 'Olive Elegance', 'image_url' => 'imgs/LX1.png'],
    ['id' => 102, 'name' => 'Sage Harmony', 'image_url' => 'imgs/LX2.png'],
    ['id' => 103, 'name' => 'Emerald Whisper', 'image_url' => 'imgs/LX3.png'],
    ['id' => 104, 'name' => 'Forest Bloom', 'image_url' => 'imgs/LX4.png'],
    ['id' => 105, 'name' => 'Mint Luxury', 'image_url' => 'imgs/LX5.png'],
    ['id' => 106, 'name' => 'Verdant Gold', 'image_url' => 'imgs/LX6.png'],
    ['id' => 107, 'name' => 'Green Marble', 'image_url' => 'imgs/LX7.png'],
    ['id' => 108, 'name' => 'Luxury Fern', 'image_url' => 'imgs/LX8.png']
];

// 2. Try DB
try {
    $stmt = $pdo->prepare("SELECT * FROM invitation_cards WHERE package_type = ?");
    $stmt->execute([$package_type]);
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cards)) {
        $cards = $fallback_cards;
    }
} catch (Exception $e) {
    $cards = $fallback_cards;
    $error_msg = "Offline Mode: Showing cached content.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Luxury - Select Invitation</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --green-dark: #3a5a40;
            --green-light: #a3b18a;
            --bg: #f9f9f9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            padding: 20px;
            text-align: center;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .card-option {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            border: 3px solid transparent;
            transition: 0.3s;
        }

        .card-option:hover {
            transform: translateY(-5px);
        }

        .card-option.active {
            border-color: var(--green-dark);
            transform: scale(1.02);
        }

        .card-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .card-info {
            padding: 15px;
        }

        .btn-continue {
            background: var(--green-dark);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 30px;
        }

        .btn-continue:hover {
            background: #2f4a33;
        }

        .alert {
            background: #ffcccc;
            color: #cc0000;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <h1>Select Your Luxury Invitation</h1>
    <p>Choose a design that represents your special day.</p>

    <?php if ($error_msg): ?>
        <div class="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="cards-container">
        <?php foreach ($cards as $card): ?>
            <div class="card-option" data-card="<?php echo basename($card['image_url']); ?>"
                data-id="<?php echo $card['id']; ?>">
                <img src="<?php echo htmlspecialchars($card['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($card['name']); ?>" class="card-img">
                <div class="card-info">
                    <h3><?php echo htmlspecialchars($card['name']); ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="btn-continue" id="continue-btn">Continue to Customize</button>

    <script>
        const cardOptions = document.querySelectorAll('.card-option');
        cardOptions.forEach(card => {
            card.addEventListener('click', () => {
                cardOptions.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            });
        });

        document.getElementById('continue-btn').addEventListener('click', () => {
            const selected = document.querySelector('.card-option.active');
            if (selected) {
                localStorage.setItem('selected_card_LX', selected.dataset.card);
                localStorage.setItem('selected_card_id', selected.dataset.id);
                window.location.href = 'editLuxury.php';
            } else {
                alert('Please select a card design first.');
            }
        });
    </script>

</body>

</html>