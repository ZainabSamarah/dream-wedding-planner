<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$package_type = 'regular';
$cakes = [];
$error_msg = "";

// 1. Defined Hardcoded Data from HTML
$fallback_cakes = [
    // Cakes
    ['id' => 301, 'name' => 'Lemon Poppy Seed Cake', 'description' => 'Light and refreshing with a citrus twist.', 'image_url' => 'imgs/Lemon Poppy Seed Cake.jpg', 'category' => 'Cakes'],
    ['id' => 302, 'name' => 'Orange Poppy Seed Cake with Mascarpone Frosting', 'description' => 'Elegant orange cake with creamy frosting.', 'image_url' => 'imgs/Orange Poppy Seed Cake with Mascarpone Frosting.jpg', 'category' => 'Cakes'],
    ['id' => 303, 'name' => 'Strawberry Shortcake Cake with Mascarpone Cream', 'description' => 'Rustic cake with fresh strawberries and cream.', 'image_url' => 'imgs/Strawberry Shortcake Cake with Mascarpone Cream.jpg', 'category' => 'Cakes'],
    ['id' => 304, 'name' => 'Cinnamon Toast Crunch Cake', 'description' => 'Warm cinnamon flavor with a crunchy twist.', 'image_url' => 'imgs/Cinnamon Toast Crunch Cake.jpg', 'category' => 'Cakes'],
    ['id' => 305, 'name' => 'Pina Colada Cake', 'description' => 'Tropical pineapple and coconut delight.', 'image_url' => 'imgs/Pina Colada Cake.jpg', 'category' => 'Cakes'],
    ['id' => 306, 'name' => 'Cranberry Orange Cake', 'description' => 'Seasonal and refreshing citrus blend.', 'image_url' => 'imgs/Cranberry Orange Cake.jpg', 'category' => 'Cakes'],
    ['id' => 307, 'name' => 'Eggnog Cake', 'description' => 'Festive winter cake with eggnog flavor.', 'image_url' => 'imgs/Eggnog Cake.jpg', 'category' => 'Cakes'],
    ['id' => 308, 'name' => 'Blueberry Shortcake Cake', 'description' => 'Fresh blueberry layers with a shortcake base.', 'image_url' => 'imgs/Blueberry Shortcake Cake.jpg', 'category' => 'Cakes'],
    ['id' => 309, 'name' => 'Cinnamon Roll Cake', 'description' => 'Warm cinnamon roll flavor in cake form.', 'image_url' => 'imgs/Cinnamon Roll Cake.jpg', 'category' => 'Cakes'],
    ['id' => 310, 'name' => 'Banana Pudding Cake', 'description' => 'Classic banana pudding in cake layers.', 'image_url' => 'imgs/Banana Pudding Cake.jpg', 'category' => 'Cakes'],
    ['id' => 311, 'name' => 'Milk & Cookies Cake', 'description' => 'Cookie dough and milk flavor combo.', 'image_url' => 'imgs/Milk & Cookies Cake.jpg', 'category' => 'Cakes'],
    ['id' => 312, 'name' => 'Poppy Seed Cake', 'description' => 'Light cake with poppy seed texture.', 'image_url' => 'imgs/Poppy Seed Cake.jpg', 'category' => 'Cakes'],
    ['id' => 313, 'name' => 'Chocolate Chip Cookie Cake', 'description' => 'Soft cookie dough with chocolate chips.', 'image_url' => 'imgs/Chocolate Chip Cookie Cake.jpg', 'category' => 'Cakes'],
    // Drinks
    ['id' => 314, 'name' => 'Water', 'description' => 'Pure and refreshing hydration.', 'image_url' => 'imgs/water (2).jpg', 'category' => 'Drinks'],
    ['id' => 315, 'name' => 'Iced Latte', 'description' => 'Smooth espresso with chilled milk.', 'image_url' => 'imgs/icedLate.png', 'category' => 'Drinks'],
    ['id' => 316, 'name' => 'Iced Tea', 'description' => 'Crisp and refreshing tea infusion.', 'image_url' => 'imgs/icedTea.png', 'category' => 'Drinks'],
];

// 2. Try DB
try {
    $stmt = $pdo->prepare("SELECT * FROM cake_menu WHERE package_type = ? ORDER BY name");
    $stmt->execute([$package_type]);
    $cakes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cakes)) {
        $cakes = $fallback_cakes;
    }
} catch (Exception $e) {
    $cakes = $fallback_cakes;
    $error_msg = "Offline Mode: Showing cached content.";
}

// Group by Category
$grouped_items = [];
foreach ($cakes as $item) {
    if (!isset($grouped_items[$item['category']])) {
        $grouped_items[$item['category']] = [];
    }
    $grouped_items[$item['category']][] = $item;
}

$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_selections'])) {
    if (empty($error_msg)) {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM user_cake_selections WHERE user_id = ? AND cake_id IN (SELECT id FROM cake_menu WHERE package_type = ?)");
            $stmtDel->execute([$user_id, $package_type]);
            if (isset($_POST['selected_items'])) {
                $stmtIns = $pdo->prepare("INSERT INTO user_cake_selections (user_id, cake_id) VALUES (?, ?)");
                foreach ($_POST['selected_items'] as $cake_id) {
                    $stmtIns->execute([$user_id, $cake_id]);
                }
            }
            $success_msg = "Selections saved successfully!";
        } catch (Exception $e) {
            $success_msg = "Could not save selections (Database Error).";
        }
    } else {
        $success_msg = "Selections saved locally (Database Disconnected).";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regular Cake Menu – WEDÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-mid: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--green-pale);
            color: var(--green-dark);
            padding-top: 70px;
        }

        header {
            background-color: var(--green-mid);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 36px;
            margin: 0;
        }

        .hero {
            position: relative;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            background: #66785F;
        }

        .hero video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
        }

        .hero-text {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            color: black;
            border-radius: 20px;
        }

        .hero h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--green-dark);
        }

        .section-title {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            text-align: center;
            margin: 60px 0 40px;
            color: var(--green-dark);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .checkbox-container {
            margin-top: 15px;
        }

        .btn-save {
            display: block;
            width: 200px;
            margin: 40px auto;
            padding: 15px;
            background: var(--green-light);
            color: white;
            border-radius: 30px;
            border: none;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-save:hover {
            background: var(--green-dark);
        }

        .alert {
            background: #ffcccc;
            color: #cc0000;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            background: #ccffcc;
            color: #006600;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

    <header>
        <h1>WEDÉ - Regular</h1>
        <nav>
            <a href="main.html" style="color:white; margin-left:20px;">Home</a>
            <a href="profile.php" style="color:white; margin-left:20px;">Profile</a>
        </nav>
    </header>

    <div class="hero">
        <video autoplay muted loop playsinline>
            <source src="imgs/v5.mp4" type="video/mp4">
        </video>
        <div class="hero-text">
            <h1>Cakes & Drinks</h1>
            <p>Classic and refreshing selection.</p>
        </div>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($grouped_items as $category => $items): ?>
            <h2 class="section-title"><?php echo htmlspecialchars($category); ?></h2>
            <div class="grid">
                <?php foreach ($items as $item): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="checkbox-container">
                                <input type="checkbox" name="selected_items[]" value="<?php echo $item['id']; ?>"
                                    id="item_<?php echo $item['id']; ?>">
                                <label for="item_<?php echo $item['id']; ?>">Select</label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="save_selections" class="btn-save">Save Selections</button>
    </form>

</body>

</html>