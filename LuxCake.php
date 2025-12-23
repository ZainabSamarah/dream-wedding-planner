<?php
require_once 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$package_type = 'luxury';
$items = [];
$success_msg = "";

// 1. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_selections'])) {
    try {
        // Clear previous selections for this package
        $stmtDel = $pdo->prepare("DELETE FROM user_cake_selections WHERE user_id = ? AND cake_id IN (SELECT id FROM cake_menu WHERE package_type = ?)");
        $stmtDel->execute([$user_id, $package_type]);

        if (isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
            $stmtIns = $pdo->prepare("INSERT INTO user_cake_selections (user_id, cake_id) VALUES (?, ?)");
            foreach ($_POST['selected_items'] as $cake_id) {
                $stmtIns->execute([$user_id, $cake_id]);
            }
        }
        $success_msg = "Your selections have been saved successfully!";
    } catch (Exception $e) {
        $error_msg = "Error saving selections: " . $e->getMessage();
    }
}

// 2. Fetch Items from DB
try {
    $stmt = $pdo->prepare("SELECT * FROM cake_menu WHERE package_type = ? ORDER BY category DESC, name ASC");
    $stmt->execute([$package_type]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by category
    $menu_by_category = [];
    foreach ($items as $item) {
        $menu_by_category[$item['category']][] = $item;
    }

    // Fetch current user selections to pre-check checkboxes
    $stmt_sel = $pdo->prepare("SELECT cake_id FROM user_cake_selections WHERE user_id = ?");
    $stmt_sel->execute([$user_id]);
    $user_selections = $stmt_sel->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die("Error fetching menu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Luxury Cake Menu – WEDÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
            --green-extra-pale: #E8F0E5;
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
            overflow-x: hidden;
            padding-top: 90px;
        }

        header {
            background-color: var(--green-medium);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            height: 70px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 36px;
            color: var(--green-pale);
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
            transition: 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }

        .nav-btn {
            padding: 10px 20px;
            background-color: var(--green-light);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin-left: 25px;
            transition: 0.3s;
        }

        .nav-btn:hover {
            background-color: var(--green-dark);
            transform: translateY(-2px);
        }

        .food-hero {
            position: relative;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .food-hero video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            z-index: 1;
        }

        .food-hero .hero-text {
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 20px;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .food-hero h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 62px;
            color: var(--green-dark);
            margin-bottom: 20px;
        }

        .food-hero p {
            font-size: 18px;
            line-height: 1.6;
            color: #333;
        }

        .alert-success {
            background-color: var(--green-light);
            color: white;
            padding: 15px;
            text-align: center;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 10px;
        }

        .menu-section {
            padding: 80px 60px;
            background: white;
        }

        .menu-section:nth-child(even) {
            background: var(--green-extra-pale);
        }

        .menu-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            text-align: center;
            margin-bottom: 50px;
            color: var(--green-dark);
        }

        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dish-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(75, 89, 69, 0.12);
            transition: all 0.4s ease;
            text-align: center;
        }

        .dish-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(75, 89, 69, 0.2);
        }

        .dish-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .dish-card:hover img {
            transform: scale(1.1);
        }

        .dish-info {
            padding: 30px;
        }

        .dish-info h3 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--green-dark);
        }

        .dish-info p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .select-checkbox {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
        }

        .select-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--green-dark);
        }

        .select-checkbox label {
            font-weight: 500;
            color: var(--green-dark);
        }

        .food-cta {
            padding: 100px 60px;
            text-align: center;
            background: var(--green-dark);
            color: white;
        }

        .food-cta h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .submit-btn {
            padding: 15px 45px;
            font-size: 18px;
            background: var(--green-light);
            color: white;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 30px;
        }

        .submit-btn:hover {
            background: var(--green-pale);
            color: var(--green-dark);
            transform: scale(1.05);
        }

        footer {
            background-color: var(--green-medium);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <header>
        <h1>WEDÉ</h1>
        <nav>
            <a href="main.html">Home</a>
            <a href="services.php">Services</a>
            <a href="profile.php">Profile</a>
            <a href="contact.php">Contact</a>
            <a href="logout.php" class="nav-btn">Logout</a>
        </nav>
    </header>

    <section class="food-hero">
        <video autoplay muted loop playsinline>
            <source src="imgs/v5.mp4" type="video/mp4">
        </video>
        <div class="hero-text">
            <h1>Luxury Cake Menu</h1>
            <p>Indulge in our premium selection of sophisticated cakes and artisanal drinks, curated for your dream
                wedding.</p>
        </div>
    </section>

    <?php if ($success_msg): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($menu_by_category as $category => $items): ?>
            <section class="menu-section">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="dishes-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="dish-card">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="dish-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="select-checkbox">
                                    <input type="checkbox" name="selected_items[]" value="<?php echo $item['id']; ?>"
                                        id="cake_<?php echo $item['id']; ?>" <?php echo in_array($item['id'], $user_selections) ? 'checked' : ''; ?>>
                                    <label for="cake_<?php echo $item['id']; ?>">Select this item</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="food-cta">
            <h2>Finalized Your Dream Menu?</h2>
            <p>Save your selections and we'll take care of the rest.</p>
            <button type="submit" name="save_selections" class="submit-btn">Save Content</button>
        </section>
    </form>

    <footer>
        <div class="footer-bottom">
            © 2025 <span style="color: var(--green-pale);">WEDÉ</span> | All rights reserved
        </div>
    </footer>

</body>

</html>