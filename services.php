<?php
session_start();

// التحقق من اللوجن
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$has_package = false;
$package_suffix = 'Reg'; // افتراضي Regular لو مفيش اختيار
$package_name = '';

try {
    // استخدمنا MySQL حسب قاعدة البيانات عندك (wedding_db)
    $db = new PDO("mysql:host=localhost;dbname=wedding_db;charset=utf8mb4", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("
        SELECT p.name AS package_name 
        FROM user_packages up
        JOIN packages p ON up.package_id = p.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $selected_package = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($selected_package) {
        $has_package = true;
        $package_name = $selected_package['package_name'];

        if (strpos($package_name, 'Luxury') !== false) {
            $package_suffix = 'Lux';
        } elseif (strpos($package_name, 'Medium') !== false) {
            $package_suffix = 'Med';
        } else {
            $package_suffix = 'Reg';
        }
    }
} catch (Exception $e) {
    $has_package = false;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Our Services – WEDÉ</title>
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
            transition: opacity 0.5s ease-in-out;
            overflow-x: hidden;
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

        .services-hero {
            position: relative;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .services-hero video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            z-index: 1;
        }

        .services-hero h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 62px;
            font-weight: 500;
            color: white;
            z-index: 3;
            text-shadow: 0px 4px 15px rgba(0, 0, 0, 0.8);
            position: relative;
            letter-spacing: 1.5px;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 1.2s ease-out, transform 1.2s ease-out;
        }

        .services-hero.visible h1 {
            opacity: 1;
            transform: translateY(0);
        }

        .services-list-section {
            padding: 120px 60px;
            text-align: center;
            background: linear-gradient(135deg, #f5f8f3, var(--green-pale));
        }

        .services-list-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--green-dark);
        }

        .services-intro {
            max-width: 900px;
            margin: 0 auto 70px;
            font-size: 18px;
            color: var(--green-dark);
            line-height: 1.9;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1.2s ease-out 0.6s, transform 1.2s ease-out 0.6s;
        }

        .services-intro.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .services-intro strong {
            display: block;
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--green-medium);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 45px 30px;
            text-align: center;
            box-shadow: 0 12px 35px rgba(75, 89, 69, 0.15);
            transition: all 0.5s ease;
            opacity: 0;
            transform: translateY(50px);
            border: 1px solid rgba(145, 172, 143, 0.2);
            position: relative;
        }

        .service-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .service-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 60px rgba(75, 89, 69, 0.25);
            background: var(--green-extra-pale);
        }

        .service-card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 25px;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.1));
            transition: transform 0.4s ease;
        }

        .service-card:hover img {
            transform: scale(1.2) rotate(8deg);
        }

        .service-card h3 {
            font-size: 23px;
            color: var(--green-dark);
            margin-bottom: 15px;
        }

        .service-card p {
            font-size: 15.5px;
            color: #444;
            line-height: 1.6;
        }

        .cta-section {
            padding: 80px 60px;
            text-align: center;
            background: var(--green-medium);
            color: white;
        }

        .cta-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 42px;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.95;
        }

        .cta-section .nav-btn {
            padding: 15px 40px;
            font-size: 18px;
            background-color: var(--green-light);
        }

        .cta-section .nav-btn:hover {
            background-color: var(--green-pale);
            color: var(--green-dark);
        }

        .faq-section {
            padding: 100px 60px;
            background: var(--green-extra-pale);
            text-align: center;
        }

        .faq-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: var(--green-dark);
            margin-bottom: 60px;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
        }

        .faq-item {
            margin-bottom: 20px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .faq-item.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .faq-question {
            background: var(--green-light);
            color: white;
            padding: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 17px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question::after {
            content: '+';
            font-size: 24px;
        }

        .faq-question.active::after {
            content: '−';
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.5s ease;
            background: white;
            color: #444;
            font-size: 16px;
            line-height: 1.7;
        }

        .faq-answer.active {
            padding: 20px;
            max-height: 400px;
        }

        .services-gallery {
            text-align: center;
            padding: 80px 60px 120px;
            background: linear-gradient(135deg, var(--green-pale), var(--green-light), var(--green-dark));
            color: white;
        }

        .services-gallery h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 40px;
            color: white;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .services-gallery h2::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 100%;
            height: 6px;
            background-color: var(--green-pale);
            border-radius: 3px;
        }

        .packages-intro {
            font-size: 17px;
            color: #ffffff;
            max-width: 800px;
            margin: 0 auto 50px;
            line-height: 1.8;
            font-weight: 400;
        }

        .gallery-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .gallery-item {
            position: relative;
            width: 300px;
            height: 420px;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
            transition: transform 0.4s ease;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(3px) brightness(0.7);
            transition: filter 0.4s ease;
        }

        .gallery-item:hover img {
            filter: blur(1px) brightness(0.85);
        }

        .overlay {
            position: absolute;
            inset: 0;
            color: white;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(4px);
        }

        .overlay h3 {
            font-family: 'Great Vibes', cursive;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .overlay .price {
            font-size: 22px;
            margin-bottom: 5px;
            color: var(--green-pale);
        }

        .overlay .per {
            font-size: 14px;
            margin-bottom: 15px;
            opacity: 0.95;
        }

        .overlay ul {
            list-style: none;
            margin-bottom: 20px;
            line-height: 1.6;
            opacity: 0.95;
        }

        .overlay button {
            background-color: var(--green-light);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .overlay button:hover {
            background-color: var(--green-pale);
            color: var(--green-dark);
            transform: translateY(-2px);
        }

        .testimonials-section {
            padding: 100px 60px;
            background: white;
            text-align: center;
        }

        .testimonials-section h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 48px;
            color: var(--green-dark);
            margin-bottom: 60px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: var(--green-extra-pale);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 1s ease-out, transform 1s ease-out;
        }

        .testimonial-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .testimonial-card .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid var(--green-light);
        }

        .testimonial-card .stars {
            color: #FFD700;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .testimonial-card p {
            font-style: italic;
            font-size: 16px;
            color: #444;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .testimonial-card .author {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 15px;
        }

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
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--green-pale);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            margin-top: 30px;
        }

        @media (max-width: 1024px) {

            .services-grid,
            .testimonials-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {

            .services-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .service-card {
                padding: 35px 20px;
            }

            .service-card img {
                width: 70px;
                height: 70px;
            }

            .services-hero h1 {
                font-size: 48px;
            }

            nav a {
                margin-left: 15px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>WEDÉ</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="gallery.html">Gallery</a>
            <a href="contact.php">Contact</a>
            <button id="logoutBtn" class="nav-btn" onclick="logout()">Logout</button>
        </nav>
    </header>

    <section class="services-hero">
        <video autoplay muted loop playsinline>
            <source src="imgs/aboutus.mp4" type="video/mp4" />
            Your browser does not support the video tag.
        </video>
        <h1>Our Services</h1>
    </section>

    <section class="services-list-section">
        <h2>Our Wedding Services</h2>
        <div class="services-intro">
            <strong>Elegant Solutions for Every Detail</strong>
            <p>From floral design to menu planning and entertainment — we provide full wedding coordination tailored to
                your unique style and vision. Our team ensures every detail is perfect so your big day feels effortless,
                elegant, and unforgettable.</p>
        </div>
        <div class="services-grid">
            <div class="service-card" onclick="window.location.href='photography.php'" style="cursor: pointer;">
                <img src="https://thumbs.dreamstime.com/b/wedding-photography-logo-set-modern-camera-marriage-design-logos-combining-style-themes-great-photographers-bridal-399343573.jpg"
                    alt="Photography">
                <h3>Photography & Videography</h3>
                <p>Capture your most precious moments with professional photographers and cinematic video production.
                </p>
            </div>
            <div class="service-card">
                <img src="https://www.shutterstock.com/image-vector/minimalist-hand-drawn-food-beverages-260nw-2516147925.jpg"
                    alt="Food">
                <h3>Custom Food Menus & Planning</h3>
                <p>We help you choose the best menu for your guests, tailored to your style and theme.</p>
            </div>
            <div class="service-card">
                <img src="https://static.vecteezy.com/system/resources/thumbnails/039/222/118/small/square-and-circle-frame-decorated-with-tropical-green-leaves-flat-illustration-isolated-on-white-background-natural-border-for-invitation-card-free-vector.jpg"
                    alt="Invitations">
                <h3>Invitations & Stationery Design</h3>
                <p>Elegant invitation cards, seating charts, and thank-you notes.</p>
            </div>
            <div class="service-card">
                <img src="https://static.vecteezy.com/system/resources/thumbnails/017/122/700/small/music-instrument-concert-musician-tool-entertainment-detailed-green-dominant-color-icon-set-vector.jpg"
                    alt="Music">
                <h3>Music & Entertainment</h3>
                <p>We help you choose the best entertainment for your guests to keep the party alive.</p>
            </div>

            <div class="service-card">
                <img src="https://www.shutterstock.com/image-vector/color-palette-20-shades-green-260nw-2589083733.jpg"
                    alt="Theme">
                <h3>Theme & Color Consultation</h3>
                <p>Helping you choose color palettes, floral styles, and design elements.</p>
            </div>
            <div class="service-card">
                <img src="https://static.vecteezy.com/system/resources/thumbnails/068/226/121/small/delicious-cake-with-matcha-on-plate-flat-icon-vector.jpg"
                    alt="Cake">
                <h3>Cake & Dessert Table Design</h3>
                <p>Custom wedding cakes and dessert displays that look (and taste) amazing.</p>
            </div>
            <div class="service-card">
                <img src="https://img.freepik.com/free-vector/elegant-wedding-invitation-with-golden-frame-leaves_1361-2144.jpg"
                    alt="RSVP">
                <h3>RSVP & Guest Count</h3>
                <p>We help you keep track of your guests efficiently and easily.</p>
            </div>

        </div>
    </section>

    <section class="cta-section">
        <h2>Ready to Plan Your Dream Wedding?</h2>
        <p>Book a free consultation now and start your journey with us</p>
        <button class="nav-btn" onclick="goToContact()">Book Free Consultation</button>
    </section>

    <section class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">How much do the services cost?</div>
                <div class="faq-answer">Our packages start from $5000 and vary depending on the required services. We
                    offer a free consultation to determine the right package for your budget.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Do you offer services outside the city/country?</div>
                <div class="faq-answer">Yes! We cover most areas and are happy to coordinate destination weddings.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">How do we book a package?</div>
                <div class="faq-answer">Book a free consultation using the button above, and we'll send you a customized
                    quote within 24 hours.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Do you offer eco-friendly options?</div>
                <div class="faq-answer">Absolutely! We provide sustainable choices like reusable decor and seasonal
                    local flowers.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">When should we start planning?</div>
                <div class="faq-answer">Ideally 12-18 months before the date, but we can organize a perfect wedding even
                    in just 3 months!</div>
            </div>
        </div>
    </section>

    <section class="services-gallery">
        <h2>Our Packages</h2>
        <p class="packages-intro">
            Every couple deserves a celebration that matches their vision. Each of our wedding packages offers a
            different level of service and customization — from essential coordination in our Regular Package, to
            enhanced elegance in our Medium option, and full luxury experience in our top-tier plan.
        </p>
        <div class="gallery-container">
            <div class="gallery-item">
                <img src="imgs/p13.jpg" alt="Regular Package">
                <div class="overlay">
                    <h3>Regular Package</h3>
                    <div class="price">$5000</div>
                    <div class="per">per package</div>
                    <ul>
                        <li>Decoration</li>
                        <li>Flower Bouquet</li>
                        <li>Documentation</li>
                        <li>Dance Party</li>
                    </ul>
                    <?php if (!$has_package): ?>
                        <button onclick="goToPreparation('Regular Package')">Get Started</button>
                    <?php else: ?>
                        <button style="background:#4CAF50;cursor:not-allowed;opacity:0.8;">Selected ✓</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="gallery-item">
                <img src="imgs/p14.jpg" alt="Medium Bouquet">
                <div class="overlay">
                    <h3>Medium Bouquet</h3>
                    <div class="price">$6500</div>
                    <div class="per">per package</div>
                    <ul>
                        <li>Decoration</li>
                        <li>Flower Bouquet</li>
                        <li>Documentation</li>
                        <li>Dance Party</li>
                    </ul>
                    <?php if (!$has_package): ?>
                        <button onclick="goToPreparation('Medium Bouquet')">Get Started</button>
                    <?php else: ?>
                        <button style="background:#4CAF50;cursor:not-allowed;opacity:0.8;">Selected ✓</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="gallery-item">
                <img src="imgs/p15.jpg" alt="Luxury Bouquet">
                <div class="overlay">
                    <h3>Luxury Bouquet</h3>
                    <div class="price">$8000</div>
                    <div class="per">per package</div>
                    <ul>
                        <li>Decoration</li>
                        <li>Flower Bouquet</li>
                        <li>Documentation</li>
                        <li>Dance Party</li>
                    </ul>
                    <?php if (!$has_package): ?>
                        <button onclick="goToPreparation('Luxury Bouquet')">Get Started</button>
                    <?php else: ?>
                        <button style="background:#4CAF50;cursor:not-allowed;opacity:0.8;">Selected ✓</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <h2>What Our Couples Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah & Michael" class="avatar">
                <div class="stars">★★★★★</div>
                <p>"WEDÉ made our day magical! The coordination was perfect and every detail exceeded our expectations."
                </p>
                <div class="author">— Sarah & Michael, 2025</div>
            </div>
            <div class="testimonial-card">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Emma & James" class="avatar">
                <div class="stars">★★★★★</div>
                <p>"A professional and friendly team that guided us every step of the way. Choosing WEDÉ was the best
                    decision!"</p>
                <div class="author">— Emma & James, 2024</div>
            </div>
            <div class="testimonial-card">
                <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Olivia & David" class="avatar">
                <div class="stars">★★★★★</div>
                <p>"From decor to photography, everything was flawless. Thank you WEDÉ for an unforgettable day!"</p>
                <div class="author">— Olivia & David, 2025</div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>Plan</h3>
                <a href="services.php">All Services</a>
                <a href="rsvp.html">RSVP Manager</a>

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
        const hasPackage = <?php echo $has_package ? 'true' : 'false'; ?>;
        const packageSuffix = "<?php echo $package_suffix; ?>";

        if (hasPackage) {
            const serviceCards = document.querySelectorAll('.service-card');

            serviceCards.forEach((card, index) => {
                card.style.cursor = 'pointer';
                card.style.position = 'relative';

                // Icon addition removed as per user request


                card.addEventListener('click', () => handleServiceClick(index));

                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-15px) scale(1.05)';
                });
                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        }

        function handleServiceClick(serviceIndex) {
            let cakePage = 'regCake.php';
            if (packageSuffix === 'Lux') {
                cakePage = 'LuxCake.php';
            } else if (packageSuffix === 'Med') {
                cakePage = 'medCake.php';
            }

            const servicePages = [
                'photography.php',                     // 0: Photography
                'servicesList' + packageSuffix + '.php', // 1: Food/Menu (.php)
                'invCard' + packageSuffix + '.php',      // 2: Invitations (.php)
                'music.php',                            // 3: Music
                'decoration.php',                       // 4: Theme & Decor (Updated index, previously 5)
                cakePage,                               // 5: Cake
                'rsvp.php',                             // 6: RSVP

            ];

            const targetPage = servicePages[serviceIndex];

            if (targetPage) {
                document.body.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = targetPage;
                }, 300);
            }
        }

        function goToPreparation(packageName) {
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = `preparation.php?package=${encodeURIComponent(packageName)}`;
            }, 500);
        }

        function logout() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = 'logout.php';
            }, 500);
        }

        function goToContact() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = 'contact.html';
            }, 500);
        }

        window.addEventListener('load', () => {
            document.querySelector('.services-hero').classList.add('visible');

            setTimeout(() => document.querySelector('.services-intro').classList.add('visible'), 800);

            setTimeout(() => {
                const cards = document.querySelectorAll('.service-card');
                cards.forEach((card, index) => {
                    setTimeout(() => card.classList.add('visible'), index * 150);
                });
            }, 1400);

            setTimeout(() => {
                const testimonials = document.querySelectorAll('.testimonial-card');
                testimonials.forEach((card, index) => {
                    setTimeout(() => card.classList.add('visible'), index * 200);
                });
            }, 2000);

            setTimeout(() => {
                const faqs = document.querySelectorAll('.faq-item');
                faqs.forEach((item, index) => {
                    setTimeout(() => item.classList.add('visible'), index * 150);
                });
            }, 2500);
        });

        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const isActive = question.classList.contains('active');

                document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('active'));
                document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('active'));

                if (!isActive) {
                    question.classList.add('active');
                    answer.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>