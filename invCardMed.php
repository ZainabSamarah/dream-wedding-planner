<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$package_type = 'medium';
$cards = [];
$error_msg = "";

// 1. Defined Hardcoded Data from HTML
$fallback_cards = [
    ['id' => 201, 'name' => 'Romantic Bloom', 'image_url' => 'imgs/SM5.png'],
    ['id' => 202, 'name' => 'Golden Chic', 'image_url' => 'imgs/SM2.png'],
    ['id' => 203, 'name' => 'Vintage Charm', 'image_url' => 'imgs/SM3.png'],
    ['id' => 204, 'name' => 'Minimal Elegance', 'image_url' => 'imgs/SM4.png']
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
    <title>Medium - Select Invitation</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-light: #91AC8F;
            --bg: #f8f9f5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f7f0, #e8f5e8);
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
            border-color: var(--green-light);
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
            background: var(--green-light);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 30px;
        }

        .btn-continue:hover {
            background: var(--green-dark);
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

    <h1>Select Your Invitation</h1>
    <p>Medium Package – Choose your favorite card design.</p>

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
                localStorage.setItem('selected_card_SM', selected.dataset.card);
                window.location.href = 'editSM.php';
            } else {
                alert('Please select a card design first.');
            }
        });
    </script>

</body>

</html>