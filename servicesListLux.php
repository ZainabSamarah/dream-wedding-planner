<?php
require_once 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$package_type = 'luxury';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_selection'])) {

    // Clear previous selections for this user ?? Or append? 
    // Usually replacing or updating. Let's assume we replace previous selection or just add.
    // Use case: User selects dishes.
    // For now, let's just insert new selections. A better approach might be to delete old ones first.
    // $pdo->prepare("DELETE FROM user_food_selections WHERE user_id = ?")->execute([$user_id]);

    if (isset($_POST['food_items']) && is_array($_POST['food_items'])) {
        $stmt_insert = $pdo->prepare("INSERT INTO user_food_selections (user_id, food_menu_id) VALUES (?, ?)");
        foreach ($_POST['food_items'] as $food_id) {
            $stmt_insert->execute([$user_id, $food_id]);
        }
        $success_msg = "Your menu selection has been saved!";
    }
}

// Fetch Items
try {
    $stmt = $pdo->prepare("SELECT * FROM food_menu WHERE package_type = ? ORDER BY category DESC, name ASC"); // ORDER by category to group them
    $stmt->execute([$package_type]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by category
    $menu_by_category = [];
    foreach ($items as $item) {
        $menu_by_category[$item['category']][] = $item;
    }

} catch (PDOException $e) {
    die("Error fetching menu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Luxury Food Menu – WEDÉ</title>
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

        /* Header */
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

        /* Hero */
        .food-hero {
            position: relative;
            height: 80vh;
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

        /* Intro */
        .food-intro {
            padding: 80px 60px;
            text-align: center;
            background: linear-gradient(135deg, #f5f8f3, var(--green-pale));
        }

        .food-intro p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 18px;
            line-height: 1.8;
            color: var(--green-dark);
        }

        /* Dishes */
        .menu-section {
            padding: 100px 60px;
            background: white;
        }

        .menu-section:nth-child(odd) {
            background: var(--green-extra-pale);
        }

        .menu-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            text-align: center;
            margin-bottom: 60px;
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
            position: relative;
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

        /* Checkbox styling */
        .select-dish-container {
            margin-top: 10px;
        }

        .select-dish-checkbox {
            width: 20px;
            height: 20px;
            accent-color: var(--green-dark);
            cursor: pointer;
        }

        .select-label {
            font-size: 16px;
            color: var(--green-dark);
            font-weight: 500;
            vertical-align: text-bottom;
            margin-left: 5px;
        }

        /* CTA */
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

        .food-cta p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto 40px;
            opacity: 0.95;
        }

        /* Submit Button Styling */
        .submit-btn {
            padding: 15px 40px;
            font-size: 18px;
            background: var(--green-light);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: var(--green-pale);
            color: var(--green-dark);
        }

        .alert-success {
            background-color: var(--green-light);
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        /* Footer */
        footer {
            background-color: var(--green-medium);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }

        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--green-pale);
        }

        .footer-column a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            font-size: 14px;
            transition: 0.3s;
        }

        .footer-column a:hover {
            color: var(--green-pale);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .food-hero h1 {
                font-size: 48px;
            }

            .food-hero .hero-text {
                padding: 30px;
            }

            .dishes-grid {
                grid-template-columns: 1fr;
            }

            .dish-card img {
                height: 200px;
            }
        }
    </style>
</head>

<body>

    <header>
        <h1>WEDÉ</h1>
        <nav>
            <a href="main.html#features">Features</a>
            <a href="main.html#tools">Tools</a>
            <a href="services.php">Services</a>
            <a href="gallery.html">Gallery</a>
            <a href="contact.php">Contact</a>
            <?php if ($user_id): ?>
                <a href="logout.php" class="nav-btn">Logout</a>
            <?php else: ?>
                <button id="loginBtn" class="nav-btn">Login</button>
            <?php endif; ?>
        </nav>
    </header>

    <section class="food-hero">
        <video autoplay muted loop playsinline>
            <source src="imgs/v5.mp4" type="video/mp4">
        </video>
        <div class="hero-text">
            <h1>Luxury Food Menu</h1>
            <p>Discover customized food menus and planning options designed to delight your wedding guests and suit your
                unique celebration style.</p>
        </div>
    </section>

    <?php if (isset($success_msg)): ?>
        <div style="max-width: 800px; margin: 20px auto;" class="alert-success">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <section class="food-intro">
        <p>Discover customized food menus and planning options designed to delight your wedding guests and suit your
            unique celebration style.</p>
    </section>

    <form method="POST" action="">
        <?php foreach ($menu_by_category as $category => $category_items): ?>
            <section class="menu-section">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="dishes-grid">
                    <?php foreach ($category_items as $item): ?>
                        <div class="dish-card">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="dish-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="select-dish-container">
                                    <input type="checkbox" name="food_items[]" value="<?php echo $item['id']; ?>"
                                        class="select-dish-checkbox" id="dish_<?php echo $item['id']; ?>">
                                    <label for="dish_<?php echo $item['id']; ?>" class="select-label">Select</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="food-cta">
            <h2>Ready to Finalize Your Menu?</h2>
            <p>Select your favorite dishes above and click below to save your menu.</p>
            <button type="submit" name="submit_selection" class="submit-btn">Save Content</button>
        </section>
    </form>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Plan</h3>
                <a href="services.php">All Services</a>
                <a href="rsvp.html">RSVP Manager</a>
                <a href="budget.html">Budget Tracker</a>
                <a href="gallery.html">Gallery</a>
            </div>
            <div class="footer-column">
                <h3>Discover</h3>
                <a href="services.php">Wedding Vendors</a>
                <a href="gallery.html">Photo Gallery</a>
                <a href="services.php">Planning Tips</a>
            </div>
            <div class="footer-column">
                <h3>Company</h3>
                <a href="contact.php">Contact Us</a>
                <a href="#">About WEDÉ</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
            <div class="footer-column">
                <h3>Follow Us</h3>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-pinterest"></i> Pinterest</a>
            </div>
        </div>
        <div class="footer-bottom">
            © 2025 <span style="color: var(--green-pale);">WEDÉ</span> | All rights reserved
        </div>
    </footer>

    <script>
        <?php if (!$user_id): ?>
            document.getElementById('loginBtn').addEventListener('click', function () {
                window.location.href = 'login.php';
            });
        <?php endif; ?>
    </script>

</body>

</html>