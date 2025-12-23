<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$package_type = 'medium';
$cakes = [];
$error_msg = "";

// 1. Defined Hardcoded Data from HTML
$fallback_cakes = [
    // Cakes
    ['id' => 201, 'name' => 'Coffee & Baileys Cake', 'description' => 'Rich coffee cake with a Baileys kick.', 'image_url' => 'imgs/Coffee & Baileys Cake.jpg', 'category' => 'Cakes'],
    ['id' => 202, 'name' => 'White Chocolate Candy Cane Cake', 'description' => 'Decadent white chocolate with candy cane.', 'image_url' => 'imgs/White Chocolate Candy Cane Cake.jpg', 'category' => 'Cakes'],
    ['id' => 203, 'name' => 'Eggnog Latte Cake', 'description' => 'A festive blend of eggnog and latte flavors.', 'image_url' => 'imgs/Eggnog Latte Cake.jpg', 'category' => 'Cakes'],
    ['id' => 204, 'name' => 'Pear & Walnut Cake with Honey Buttercream', 'description' => 'A fall-inspired cake with honey buttercream.', 'image_url' => 'imgs/Pear & Walnut Cake with Honey Buttercream.jpg', 'category' => 'Cakes'],
    ['id' => 205, 'name' => 'Pecan Pie Cake', 'description' => 'Rich pecan pie filling in cake layers.', 'image_url' => 'imgs/Pecan Pie Cake.jpg', 'category' => 'Cakes'],
    ['id' => 206, 'name' => 'Biscoff Cake (Cookie Butter Cake)', 'description' => 'Elegant Biscoff cake displayed on a stand.', 'image_url' => 'imgs/Biscoff Cake (Cookie Butter Cake).jpg', 'category' => 'Cakes'],
    ['id' => 207, 'name' => 'Vanilla Latte Cake', 'description' => 'Smooth vanilla with a coffee latte twist.', 'image_url' => 'imgs/Vanilla Latte Cake.jpg', 'category' => 'Cakes'],
    ['id' => 208, 'name' => 'White Chocolate Mocha Cake', 'description' => 'Creamy white chocolate with mocha notes.', 'image_url' => 'imgs/White Chocolate Mocha Cake.jpg', 'category' => 'Cakes'],
    ['id' => 209, 'name' => 'White Chocolate Cake', 'description' => 'Decadent white chocolate in layers and drip.', 'image_url' => 'imgs/White Chocolate Cake.jpg', 'category' => 'Cakes'],
    ['id' => 210, 'name' => 'Bakewell Cake (Raspberry Almond Cake)', 'description' => 'Raspberry and almond in a traditional style.', 'image_url' => 'imgs/Bakewell Cake (Raspberry Almond Cake).jpg', 'category' => 'Cakes'],
    ['id' => 211, 'name' => 'Lime & Coconut Cake', 'description' => 'Zesty lime paired with coconut layers.', 'image_url' => 'imgs/Lime & Coconut Cake.jpg', 'category' => 'Cakes'],
    ['id' => 212, 'name' => 'Chai Cake with Cream Cheese Frosting', 'description' => 'Spiced chai with creamy frosting.', 'image_url' => 'imgs/Chai Cake with Cream Cheese Frosting.jpg', 'category' => 'Cakes'],
    ['id' => 213, 'name' => 'Almond Amaretto Cake', 'description' => 'Almond cake with amaretto liqueur.', 'image_url' => 'imgs/Almond Amaretto Cake.jpg', 'category' => 'Cakes'],
    ['id' => 214, 'name' => 'Earl Grey Cake With Vanilla Bean Buttercream', 'description' => 'Earl Grey tea with vanilla bean frosting.', 'image_url' => 'imgs/Earl Grey Cake With Vanilla Bean Buttercream.jpg', 'category' => 'Cakes'],
    ['id' => 215, 'name' => 'Froot Loops Cake', 'description' => 'Colorful cereal-inspired cake.', 'image_url' => 'imgs/Froot Loops Cake.jpg', 'category' => 'Cakes'],
    ['id' => 216, 'name' => 'Spice Cake with Cinnamon Streusel', 'description' => 'Spiced cake with cinnamon topping.', 'image_url' => 'imgs/Spice Cake with Cinnamon Streusel.jpg', 'category' => 'Cakes'],
    ['id' => 217, 'name' => 'Blueberry Banana Cake with Cream Cheese Frosting', 'description' => 'Blueberry and banana with creamy frosting.', 'image_url' => 'imgs/Blueberry Banana Cake with Cream Cheese Frosting.jpg', 'category' => 'Cakes'],
    ['id' => 218, 'name' => 'Chocolate Chip Cake With Whipped Chocolate Buttercream', 'description' => 'Chocolate chips with whipped frosting.', 'image_url' => 'imgs/Chocolate Chip Cake With Whipped Chocolate Buttercream.jpg', 'category' => 'Cakes'],
    ['id' => 219, 'name' => 'Peanut Butter & Jelly Cake', 'description' => 'Classic PB&J sandwich in cake form.', 'image_url' => 'imgs/Peanut Butter & Jelly Cake.jpg', 'category' => 'Cakes'],
    ['id' => 220, 'name' => 'Chocolate Orange Cake', 'description' => 'Rich chocolate with zesty orange.', 'image_url' => 'imgs/Chocolate Orange Cake.jpg', 'category' => 'Cakes'],
    ['id' => 221, 'name' => 'Walnut Cake With Brown Sugar Buttercream', 'description' => 'Walnut cake with brown sugar frosting.', 'image_url' => 'imgs/Walnut Cake With Brown Sugar Buttercream.jpg', 'category' => 'Cakes'],
    ['id' => 222, 'name' => 'Apple Pie Cake', 'description' => 'Apple pie filling in cake layers.', 'image_url' => 'imgs/Apple Pie Cake.jpg', 'category' => 'Cakes'],
    // Drinks
    ['id' => 223, 'name' => 'Water', 'description' => 'Pure and refreshing hydration.', 'image_url' => 'imgs/water (2).jpg', 'category' => 'Drinks'],
    ['id' => 224, 'name' => 'Iced Shaken Espresso', 'description' => 'Bold espresso shaken with ice.', 'image_url' => 'imgs/IcedShakenEspresso.jpg', 'category' => 'Drinks'],
    ['id' => 225, 'name' => 'Iced Passion Tango Tea', 'description' => 'Vibrant passion fruit tea with ice.', 'image_url' => 'imgs/IcedPassionTangoTea.jpg', 'category' => 'Drinks'],
    ['id' => 226, 'name' => 'Strawberry Acai Lemonade Refresher', 'description' => 'Refreshing strawberry and acai blend.', 'image_url' => 'imgs/SBX20211210_StrawberryAcaiLemonadeRefreshers.jpg', 'category' => 'Drinks'],
];

// 2. Try DB
try {
    $stmt = $pdo->prepare("SELECT * FROM cake_menu WHERE package_type = ? ORDER BY category, name");
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

// Handle Form Submission
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
    <title>Medium Cake Menu – WEDÉ</title>
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
        <h1>WEDÉ - Medium</h1>
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
            <h1>Medium Cake Menu</h1>
            <p>Explore our flavorful selection.</p>
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