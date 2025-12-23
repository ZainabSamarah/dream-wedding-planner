<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$user_email = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>WEDÉ – Decoration</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
            margin: 0;
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
            margin: 0;
        }

        main {
            max-width: 1100px;
            margin: 120px auto 40px;
            /* Adjusted for fixed header */
            padding: 0 20px;
        }

        main h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 42px;
            margin-bottom: 40px;
            text-align: center;
            color: var(--green-dark);
        }

        /* THEMES GRID */
        .themes-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-bottom: 60px;
        }

        .theme-card {
            background: white;
            width: 300px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .theme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .theme-card img {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .theme-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            height: 30px;
            /* Fixed height for alignment */
        }

        .choose-btn {
            margin-top: 15px;
            background: var(--green-light);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            font-weight: 500;
            transition: 0.3s;
        }

        .choose-btn:hover {
            background: var(--green-medium);
        }

        .choose-btn.selected {
            background: var(--green-dark);
            content: "Selected";
        }

        /* COLOR PALETTES */
        .palette {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
            height: 25px;
        }

        .color-box {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .color-box:hover {
            transform: scale(1.2);
        }

        /* NOTES SECTION */
        .notes-section {
            margin-top: 60px;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .notes-section textarea {
            width: 100%;
            box-sizing: border-box;
            height: 150px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 15px;
            resize: vertical;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .notes-section textarea:focus {
            outline: none;
            border-color: var(--green-light);
        }

        .save-notes {
            background: var(--green-medium);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
        }

        .save-notes:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }

        /* Navigation Styles */
        nav {
            display: flex;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
            transition: 0.3s;
            font-size: 16px;
        }

        nav a:hover {
            opacity: 0.8;
        }

        .nav-btn {
            padding: 8px 20px;
            background-color: var(--green-light);
            color: white;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin-left: 25px;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-btn:hover {
            background-color: var(--green-dark);
            transform: translateY(-2px);
        }

        /* Footer Styles */
        footer {
            background-color: var(--green-medium);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
            text-align: left;
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

        /* Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateY(100px);
            transition: 0.3s;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.success {
            border-left: 5px solid #4CAF50;
        }

        .toast.error {
            border-left: 5px solid #f44336;
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
            <a href="logout.php" class="nav-btn">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Choose Your Decoration Theme</h2>

        <div class="themes-grid" id="theme-grid">

            <!-- 1: Classic White -->
            <div class="theme-card">
                <img src="imgs/classicwhite.jpg" alt="Classic White">
                <div class="theme-name">Classic White</div>

                <div class="palette">
                    <div class="color-box" style="background:#FFFFFF"></div>
                    <div class="color-box" style="background:#F3F3F3"></div>
                    <div class="color-box" style="background:#D5D5D5"></div>
                    <div class="color-box" style="background:#AFAFAF"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('theme', 'Classic White', this)">Choose
                    Theme</button>
            </div>

            <!-- 2: Sage Green -->
            <div class="theme-card">
                <img src="imgs/sagegreen.jpg" alt="Sage Green">
                <div class="theme-name">Sage Green</div>

                <div class="palette">
                    <div class="color-box" style="background:#6E8F6A"></div>
                    <div class="color-box" style="background:#AFC8A7"></div>
                    <div class="color-box" style="background:#DCE7D7"></div>
                    <div class="color-box" style="background:#E9EFE9"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('theme', 'Sage Green', this)">Choose Theme</button>
            </div>

            <!-- 3: Golden Royal -->
            <div class="theme-card">
                <img src="imgs/goldenroyal.jpg" alt="Golden Royal">
                <div class="theme-name">Golden Royal</div>

                <div class="palette">
                    <div class="color-box" style="background:#E5C16A"></div>
                    <div class="color-box" style="background:#C79C34"></div>
                    <div class="color-box" style="background:#8A692A"></div>
                    <div class="color-box" style="background:#F4E8CA"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('theme', 'Golden Royal', this)">Choose Theme</button>
            </div>

            <!-- 4: Blush Pink -->
            <div class="theme-card">
                <img src="imgs/blushpink.jpg" alt="Blush Pink">
                <div class="theme-name">Blush Pink</div>

                <div class="palette">
                    <div class="color-box" style="background:#F7D9D9"></div>
                    <div class="color-box" style="background:#F2B6C1"></div>
                    <div class="color-box" style="background:#D18E99"></div>
                    <div class="color-box" style="background:#F9ECEC"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('theme', 'Blush Pink', this)">Choose Theme</button>
            </div>

            <!-- 5: Rustic Garden -->
            <div class="theme-card">
                <img src="imgs/dustylavender.jpg" alt="Dusty Lavender">
                <div class="theme-name">Dusty Lavender</div>

                <div class="palette">
                    <div class="color-box" style="background:#A990A5"></div>
                    <div class="color-box" style="background:#C7B8CC"></div>
                    <div class="color-box" style="background:#E6DFEB"></div>
                    <div class="color-box" style="background:#EDE6F2"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('theme', 'Rustic Garden', this)">Choose
                    Theme</button>
            </div>

        </div>


        <h2>Choose Your Decoration Flowers</h2>

        <div class="themes-grid" id="flowers-grid">

            <!-- Lilies -->
            <div class="theme-card">
                <img src="imgs/lily.jpg" alt="Lilies">
                <div class="theme-name">Lilies</div>

                <div class="palette">
                    <div class="color-box" data-image="lilywhite.jpg" style="background:#FFFFFF"></div>
                    <div class="color-box" data-image="lily.jpg" style="background:#F28A90"></div>
                    <div class="color-box" data-image="lilyyellow.jpg" style="background:#FFF700"></div>
                    <div class="color-box" data-image="lilyredd.jpg" style="background:#FF0000"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('flowers', 'Lilies', this)">Choose Flowers</button>
            </div>

            <!-- Roses -->
            <div class="theme-card">
                <img src="imgs/rosepink.jpg" alt="Roses">
                <div class="theme-name">Roses</div>

                <div class="palette">
                    <div class="color-box" data-image="whiteroses.jpg" style="background:#FFFFFF"></div>
                    <div class="color-box" data-image="rosepink.jpg" style="background:#FFC0CB"></div>
                    <div class="color-box" data-image="roselavender.jpg" style="background:#DFC5FE"></div>
                    <div class="color-box" data-image="roseredd.jpg" style="background:#C00000"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('flowers', 'Roses', this)">Choose Flowers</button>
            </div>

            <!-- Tulips -->
            <div class="theme-card">
                <img src="imgs/tulippink.jpg" alt="Tulips">
                <div class="theme-name">Tulips</div>

                <div class="palette">
                    <div class="color-box" data-image="tulipwhite.jpg" style="background:#FFFFFF"></div>
                    <div class="color-box" data-image="tulippink.jpg" style="background:#F4A6C4"></div>
                    <div class="color-box" data-image="tuliporange.jpg" style="background:#FF5C00"></div>
                    <div class="color-box" data-image="tulipred.jpg" style="background:#D10000"></div>
                </div>

                <button class="choose-btn" onclick="savePreference('flowers', 'Tulips', this)">Choose Flowers</button>
            </div>
        </div>


        <h2>Choose Your Lighting Decoration</h2>

        <div class="themes-grid" id="lighting-grid">

            <!-- String Lighting -->
            <div class="theme-card">
                <img src="imgs/stringlights.jpg" alt="String Lighting">
                <div class="theme-name">String Lighting</div>
                <button class="choose-btn" onclick="savePreference('lighting', 'String Lighting', this)">Choose
                    Lighting</button>
            </div>

            <!-- Lighting Balls -->
            <div class="theme-card">
                <img src="imgs/lightingballs.jpg" alt="Lighting Balls">
                <div class="theme-name">Lighting Balls</div>
                <button class="choose-btn" onclick="savePreference('lighting', 'Lighting Balls', this)">Choose
                    Lighting</button>
            </div>

            <!-- Fairy Lighting -->
            <div class="theme-card">
                <img src="imgs/fairylights.jpg" alt="Fairy Lighting">
                <div class="theme-name">Fairy Lighting</div>
                <button class="choose-btn" onclick="savePreference('lighting', 'Fairy Lighting', this)">Choose
                    Lighting</button>
            </div>

            <!-- LED Curtains -->
            <div class="theme-card">
                <img src="imgs/ledcurtains.jpg" alt="LED Curtains">
                <div class="theme-name">LED Curtains</div>
                <button class="choose-btn" onclick="savePreference('lighting', 'LED Curtains', this)">Choose
                    Lighting</button>
            </div>

            <!--Garden Lights-->
            <div class="theme-card">
                <img src="imgs/gardenlights.jpg" alt="Garden Lights">
                <div class="theme-name">Garden Lights</div>
                <button class="choose-btn" onclick="savePreference('lighting', 'Garden Lights', this)">Choose
                    Lighting</button>
            </div>

        </div>


        <h2>Choose Your Table Centerpieces</h2>

        <div class="themes-grid" id="centerpieces-grid">

            <!-- Candles -->
            <div class="theme-card">
                <img src="imgs/candles.jpg" alt="Candles">
                <div class="theme-name">Candles</div>
                <button class="choose-btn" onclick="savePreference('centerpieces', 'Candles', this)">Choose
                    Centerpieces</button>
            </div>

            <!-- Flower Vase -->
            <div class="theme-card">
                <img src="imgs/flowervases.jpg" alt="Flower Vases">
                <div class="theme-name">Flower Vases</div>
                <button class="choose-btn" onclick="savePreference('centerpieces', 'Flower Vases', this)">Choose
                    Centerpieces</button>
            </div>

            <!-- Lantern Centerpieces -->
            <div class="theme-card">
                <img src="imgs/lantern.jpg" alt="Lantern Centerpieces">
                <div class="theme-name">Lanterns</div>
                <button class="choose-btn" onclick="savePreference('centerpieces', 'Lantern Centerpieces', this)">Choose
                    Centerpieces</button>
            </div>

            <!-- Floating Water -->
            <div class="theme-card">
                <img src="imgs/floatingwater.jpg" alt="Floating Water">
                <div class="theme-name">Floating Water</div>
                <button class="choose-btn" onclick="savePreference('centerpieces', 'Floating Water', this)">Choose
                    Centerpieces</button>
            </div>
        </div>


        <div class="notes-section">
            <h3
                style="font-family:'Great Vibes', cursive; font-size:32px; margin-bottom:20px; color:var(--green-dark);">
                Custom Requests</h3>
            <textarea id="userNotes" placeholder="Describe any custom decoration ideas here..."></textarea>
            <button class="save-notes" onclick="saveNotes()">Save Custom Notes</button>
        </div>

    </main>

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
        document.addEventListener('DOMContentLoaded', () => {
            loadPreferences();
        });

        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('show');
        }

        // Close dropdown
        window.onclick = function (event) {
            if (!event.target.matches('.profile-btn') && !event.target.closest('.profile-btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        async function loadPreferences() {
            try {
                const response = await fetch('save_decoration.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_decoration' })
                });
                const data = await response.json();

                if (data.success && data.decoration) {
                    // Pre-populate selections
                    if (data.decoration.theme) highlightSelection('theme', data.decoration.theme);
                    if (data.decoration.flowers) highlightSelection('flowers', data.decoration.flowers);
                    if (data.decoration.lighting) highlightSelection('lighting', data.decoration.lighting);
                    if (data.decoration.centerpieces) highlightSelection('centerpieces', data.decoration.centerpieces);

                    document.getElementById('userNotes').value = data.decoration.custom_notes || '';
                }
            } catch (error) {
                console.error('Error loading preferences:', error);
            }
        }

        function highlightSelection(type, value) {
            // Find button with specific onclick content involves regex or data attributes. 
            // For simplicity, we loop through all buttons in the relevant grid.
            let gridId = type + '-grid';
            if (type === 'flowers') gridId = 'flowers-grid'; // Match ID

            const grid = document.getElementById(gridId);
            if (!grid) return;

            const buttons = grid.querySelectorAll('.choose-btn');
            buttons.forEach(btn => {
                // Check if onclick attribute contains the value
                // onclick="savePreference('theme', 'Classic White', this)"
                if (btn.getAttribute('onclick').includes(`'${value}'`)) {
                    btn.style.backgroundColor = 'var(--green-dark)';
                    btn.textContent = 'Selected';
                }
            });
        }

        async function savePreference(type, value, btnElement) {
            // Reset buttons in current grid
            const grid = btnElement.closest('.themes-grid');
            grid.querySelectorAll('.choose-btn').forEach(b => {
                b.style.backgroundColor = 'var(--green-light)';
                b.textContent = b.getAttribute('onclick').split("'")[3] === 'theme' ? 'Choose Theme' : (b.getAttribute('onclick').split("'")[3] === 'flowers' ? 'Choose Flowers' : 'Choose');
                // Simplified text reset logic
                if (type === 'theme') b.textContent = 'Choose Theme';
                else if (type === 'flowers') b.textContent = 'Choose Flowers';
                else if (type === 'lighting') b.textContent = 'Choose Lighting';
                else if (type === 'centerpieces') b.textContent = 'Choose Centerpieces';
            });

            // Highlight clicked
            btnElement.style.backgroundColor = 'var(--green-dark)';
            btnElement.innerText = 'Saving...';

            try {
                const payload = { action: 'save_decoration' };
                payload[type] = value;

                const response = await fetch('save_decoration.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();

                if (data.success) {
                    btnElement.innerText = 'Selected';
                    showToast('Saved: ' + value, 'success');
                } else {
                    btnElement.innerText = 'Retry';
                    alert('Error saving: ' + data.message);
                }
            } catch (error) {
                console.error(error);
                showToast('Network error', 'error');
            }
        }

        async function saveNotes() {
            const notes = document.getElementById('userNotes').value;
            const btn = document.querySelector('.save-notes');
            const originalText = btn.textContent;
            btn.textContent = 'Saving...';

            try {
                const response = await fetch('save_decoration.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'save_decoration', custom_notes: notes })
                });
                const data = await response.json();

                if (data.success) {
                    showToast('Custom notes saved!', 'success');
                } else {
                    showToast('Error saving notes', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            } finally {
                btn.textContent = originalText;
            }
        }

        // PALETTE CLICK TO CHANGE IMAGE
        const colorBoxes = document.querySelectorAll(".theme-card .color-box");
        colorBoxes.forEach(box => {
            box.addEventListener("click", function () {
                const imageName = this.getAttribute("data-image");
                if (!imageName) return;
                const card = this.closest(".theme-card");
                const imgTag = card.querySelector("img");
                imgTag.src = "imgs/" + imageName;
            });
        });

        function showToast(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.textContent = msg;
            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

</body>

</html>