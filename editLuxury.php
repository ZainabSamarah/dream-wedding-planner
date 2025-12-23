<?php
require_once 'config.php';
// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design Your Card – WEDÉ</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --green-dark: #3a5a40;
            --green-mid: #588157;
            --green-light: #a3b18a;
            --bg: #f5f8f4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f7f2, #eaf2e8);
            min-height: 100vh;
            padding: 30px 20px;
        }

        header {
            text-align: center;
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 38px;
            color: var(--green-dark);
        }

        header p {
            color: #666;
            margin-top: 8px;
            font-size: 15px;
        }

        .editor-container {
            display: flex;
            width: 100%;
            max-width: 1400px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 auto;
        }

        .card-section {
            flex: 1;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .card-preview {
            width: 400px;
            height: 600px;
            border-radius: 18px;
            position: relative;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 3px solid var(--green-light);
            overflow: hidden;
            background: #fff;
        }

        .card-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            user-select: none;
        }

        .overlay-text {
            position: absolute;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            cursor: grab;
            user-select: none;
            white-space: pre-wrap;
            font-family: 'Playfair Display', serif;
            color: var(--green-dark);
        }

        .overlay-text.selected {
            outline: 2px dashed var(--green-light);
            border-radius: 6px;
        }

        .qr-code-container {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 8px;
            padding: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code-container canvas,
        .qr-code-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .controls {
            flex: 1;
            background: #f9faf9;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            max-height: 700px;
        }

        .controls h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--green-dark);
            font-size: 24px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            font-size: 13px;
            color: #444;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #d8e3d6;
            font-size: 14px;
            font-family: inherit;
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .col {
            flex: 1;
        }

        .form-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .section-title {
            font-weight: 600;
            color: var(--green-dark);
            margin-bottom: 10px;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 120px;
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            background: var(--green-mid);
            color: white;
            transition: 0.3s;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #999;
            color: white;
        }

        .btn-secondary:hover {
            background: #777;
        }

        .note {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        @media(max-width:900px) {
            .editor-container {
                flex-direction: column;
            }

            .card-section,
            .controls {
                width: 100%;
                flex: none;
            }

            .controls {
                max-height: none;
            }
        }
    </style>
</head>

<body>

    <header>
        <h1>Design Your Invitation Card</h1>
        <p>Customize your luxury wedding invitation with QR code</p>
    </header>

    <div class="editor-container">

        <div class="card-section">
            <div class="card-preview" id="card-area">
                <img id="card-bg" src="imgs/LX1b.png" alt="Luxury Card" />
                <div class="overlay-text" style="top:15%;font-size:28px;">Save The Date</div>
                <div class="overlay-text" style="top:35%;font-size:16px;">FOR THE WEDDING OF</div>
                <div class="overlay-text" style="top:50%;font-family:'Great Vibes', cursive;font-size:32px;">Layla &
                    Adam</div>
                <div class="overlay-text" style="top:65%;font-size:16px;">SATURDAY, JUNE 14, 2026 | 6:00 PM</div>
                <div class="overlay-text" style="top:78%;font-size:15px;">Royal Garden, London</div>
                <div class="qr-code-container" id="qrContainer"></div>
            </div>
        </div>

        <div class="controls">
            <h2>Edit Your Card</h2>

            <div class="form-section">
                <div class="section-title">Wedding Details</div>
                <div class="row">
                    <div class="col">
                        <label for="brideName">Bride Name</label>
                        <input type="text" id="brideName" placeholder="Bride's name">
                    </div>
                    <div class="col">
                        <label for="groomName">Groom Name</label>
                        <input type="text" id="groomName" placeholder="Groom's name">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="weddingDate">Wedding Date</label>
                        <input type="date" id="weddingDate">
                    </div>
                    <div class="col">
                        <label for="location">Location</label>
                        <input type="text" id="location" placeholder="Venue location">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">Edit Text</div>
                <label for="text-input">Selected Text</label>
                <textarea id="text-input" placeholder="Click any text on the card to edit it"></textarea>

                <div class="row">
                    <div class="col">
                        <label for="font-select">Font</label>
                        <select id="font-select">
                            <option value="'Playfair Display', serif">Playfair Display</option>
                            <option value="'Great Vibes', cursive">Great Vibes</option>
                            <option value="'Poppins', sans-serif">Poppins</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="size-input">Font Size (px)</label>
                        <input type="text" id="size-input" placeholder="e.g. 18">
                    </div>
                </div>

                <label for="color-input">Text Color</label>
                <input type="color" id="color-input" value="#3a5a40">
            </div>

            <div class="actions">
                <button class="btn" id="save-wedding-btn">Save Wedding</button>
                <button class="btn btn-secondary" id="generate-qr-btn">Generate QR</button>
                <button class="btn" id="download-btn">Download Card</button>
            </div>

            <p class="note">Click any text to edit, move, resize, or change color. Generate QR code to link guests to
                RSVP tracking.</p>
        </div>
    </div>

    <script>
        let weddingData = JSON.parse(localStorage.getItem('wede_wedding_LX')) || {
            id: Date.now(),
            brideName: 'Layla',
            groomName: 'Adam',
            weddingDate: '2026-06-14',
            location: 'Royal Garden, London'
        };

        const RSVP_API_URL = 'save_rsvp.php';
        let rsvpUrl = '';
        let activeEl = null;

        window.addEventListener("DOMContentLoaded", () => {
            const selectedCard = localStorage.getItem("selected_card_LX");
            const bg = document.getElementById("card-bg");
            if (selectedCard) {
                bg.src = "imgs/" + selectedCard.replace(".png", "b.png");
            }

            document.getElementById('brideName').value = weddingData.brideName;
            document.getElementById('groomName').value = weddingData.groomName;
            document.getElementById('weddingDate').value = weddingData.weddingDate;
            document.getElementById('location').value = weddingData.location;

            fetchRsvpDetails();
        });

        async function fetchRsvpDetails() {
            try {
                // Get User's RSVP Code and URL
                const response = await fetch(RSVP_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_user_rsvp_code' })
                });
                const data = await response.json();

                if (data.success) {
                    rsvpUrl = data.rsvp_url;
                    generateQRCode();
                } else {
                    console.error('Failed to get RSVP code:', data.message);
                }
            } catch (error) {
                console.error('Network error fetching RSVP details:', error);
            }
        }

        const cardArea = document.getElementById("card-area");
        const textInput = document.getElementById("text-input");
        const fontSelect = document.getElementById("font-select");
        const sizeInput = document.getElementById("size-input");
        const colorInput = document.getElementById("color-input");
        const downloadBtn = document.getElementById("download-btn");
        const generateQRBtn = document.getElementById("generate-qr-btn");
        const saveWeddingBtn = document.getElementById("save-wedding-btn");

        cardArea.querySelectorAll(".overlay-text").forEach(el => {
            makeDraggable(el);
            el.addEventListener("click", () => {
                cardArea.querySelectorAll(".overlay-text").forEach(t => t.classList.remove("selected"));
                el.classList.add("selected");
                activeEl = el;
                textInput.value = el.textContent;
                fontSelect.value = el.style.fontFamily;
                sizeInput.value = parseInt(el.style.fontSize);
                colorInput.value = el.style.color || "#3a5a40";
            });
        });

        function makeDraggable(el) {
            let dragging = false, startX = 0, startY = 0, origX = 0, origY = 0;
            el.addEventListener("pointerdown", e => {
                dragging = true; startX = e.clientX; startY = e.clientY;
                const rect = el.getBoundingClientRect();
                const parent = cardArea.getBoundingClientRect();
                origX = rect.left - parent.left;
                origY = rect.top - parent.top;
                el.setPointerCapture(e.pointerId);
            });
            el.addEventListener("pointermove", e => {
                if (!dragging) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                el.style.left = ((origX + dx) / cardArea.clientWidth) * 100 + "%";
                el.style.top = ((origY + dy) / cardArea.clientHeight) * 100 + "%";
            });
            el.addEventListener("pointerup", e => { dragging = false; el.releasePointerCapture(e.pointerId); });
        }

        textInput.addEventListener("input", () => { if (activeEl) activeEl.textContent = textInput.value; });
        fontSelect.addEventListener("change", () => { if (activeEl) activeEl.style.fontFamily = fontSelect.value; });
        sizeInput.addEventListener("input", () => { if (activeEl) activeEl.style.fontSize = sizeInput.value + "px"; });
        colorInput.addEventListener("input", () => { if (activeEl) activeEl.style.color = colorInput.value; });

        saveWeddingBtn.addEventListener("click", async () => {
            const brideName = document.getElementById('brideName').value.trim();
            const groomName = document.getElementById('groomName').value.trim();
            const weddingDate = document.getElementById('weddingDate').value;
            const location = document.getElementById('location').value.trim();

            if (!brideName || !groomName) {
                showToast('Please enter bride and groom names', 'error');
                return;
            }

            // Update local storage
            weddingData.brideName = brideName;
            weddingData.groomName = groomName;
            weddingData.weddingDate = weddingDate;
            weddingData.location = location;
            localStorage.setItem('wede_wedding_LX', JSON.stringify(weddingData));

            // Collect card customizations
            const cardElements = [];
            document.querySelectorAll('.overlay-text').forEach(el => {
                cardElements.push({
                    text: el.textContent,
                    top: el.style.top,
                    left: el.style.left,
                    fontSize: el.style.fontSize,
                    fontFamily: el.style.fontFamily,
                    color: el.style.color
                });
            });

            const selectedCard = localStorage.getItem("selected_card_LX") || 'LX1.png';

            try {
                // 1. Save Card Design
                const response = await fetch('save_card.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        brideName, groomName, weddingDate, location,
                        cardTemplateId: 3,
                        customText: selectedCard,
                        cardDesign: { template: selectedCard, elements: cardElements }
                    })
                });
                const data = await response.json();

                if (data.success) {
                    showToast('Card saved successfully!', 'success');
                    // 2. Sync Event Details to RSVP System
                    const rsvpResponse = await fetch(RSVP_API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'update_event',
                            event_name: 'Wedding of ' + brideName + ' & ' + groomName,
                            event_date: weddingDate,
                            event_location: location
                        })
                    });
                    await rsvpResponse.json();

                    fetchRsvpDetails(); // Refresh QR
                } else { showToast(data.message || 'Error saving', 'error'); }
            } catch (error) { showToast('Connection error', 'error'); }
        });

        function generateQRCode() {
            const qrContainer = document.getElementById("qrContainer");
            qrContainer.innerHTML = '';

            if (rsvpUrl) {
                new QRCode(qrContainer, {
                    text: rsvpUrl,
                    width: 70,
                    height: 70,
                    colorDark: '#3a5a40',
                    colorLight: '#FFFFFF',
                    correctLevel: QRCode.CorrectLevel.L
                });
            } else {
                qrContainer.innerHTML = '<span style="font-size:10px;text-align:center;">QR Loading...</span>';
            }
        }

        document.getElementById("generate-qr-btn").addEventListener("click", () => {
            if (rsvpUrl) generateQRCode();
            else fetchRsvpDetails();
        });

        downloadBtn.addEventListener("click", () => {
            cardArea.querySelectorAll(".overlay-text").forEach(x => x.classList.remove("selected"));
            html2canvas(cardArea, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
                const a = document.createElement("a");
                a.href = canvas.toDataURL("image/png");
                a.download = "WEDE_Luxury_Invitation.png";
                a.click();
                showToast('Card downloaded!', 'success');
            });
        });

        function showToast(msg, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#4CAF50' : '#f44336';
            toast.style.cssText = `position:fixed;bottom:20px;right:20px;background:${bgColor};color:white;padding:15px 20px;border-radius:8px;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.3);`;
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>

</html>