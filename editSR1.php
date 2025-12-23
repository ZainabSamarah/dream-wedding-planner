<?php
require_once 'config.php';
// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Edit Invitation Card – WEDÉ</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;700&family=Dancing+Script:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --card-width: 400px;
            --card-height: 600px;
            --bg: #fbfaf8;
            --accent: #d4a373;
            --dark: #222;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
            padding: 20px;
        }

        .editor-container {
            display: flex;
            justify-content: center;
            align-items: stretch;
            width: 100%;
            max-width: 1200px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex-wrap: wrap;
        }

        .card-section {
            flex: 1;
            background: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            min-width: 300px;
        }

        .card-preview {
            width: var(--card-width);
            height: var(--card-height);
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .15);
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .card-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            user-select: none;
            pointer-events: none;
        }

        .overlay-text {
            position: absolute;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: var(--dark);
            font-family: 'Playfair Display', serif;
            user-select: none;
            white-space: pre-wrap;
            cursor: grab;
        }

        .overlay-text.selected {
            outline: 2px dashed rgba(212, 163, 115, 0.6);
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
            background: #fff;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow-y: auto;
            max-height: 700px;
            min-width: 300px;
        }

        .controls h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--dark);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
            color: #444;
            font-size: 13px;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #e6e6e6;
            font-size: 14px;
            font-family: inherit;
        }

        textarea {
            min-height: 84px;
            resize: vertical;
        }

        .row {
            display: flex;
            gap: 8px;
        }

        .col {
            flex: 1;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 100px;
            padding: 10px 12px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            background: var(--accent);
            color: white;
            box-shadow: 0 6px 18px rgba(212, 163, 115, .16);
            transition: 0.3s;
            font-size: 13px;
        }

        .btn:hover {
            background: #c49563;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #999;
        }

        .btn-secondary:hover {
            background: #777;
        }

        .note {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }

        .form-section {
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="editor-container">
        <div class="card-section">
            <div class="card-preview" id="card-area">
                <img class="card-bg" id="card-bg" src="imgs/SR1b.png" alt="Card background" />
                <div class="overlay-text" style="top:25%;font-size:12px;">Save the Date</div>
                <div class="overlay-text" style="top:33%;font-size:13px;">FOR THE WEDDING OF</div>
                <div class="overlay-text"
                    style="top:45%;font-family:'Playfair Display', serif;font-size:20px;letter-spacing:1px;">CALEB
                    GOLDEN</div>
                <div class="overlay-text" style="top:50%;font-family:'Dancing Script', cursive;font-size:16px;">and
                </div>
                <div class="overlay-text"
                    style="top:55%;font-family:'Playfair Display', serif;font-size:20px;letter-spacing:1px;">BENJAMIN
                    JAFFE</div>
                <div class="overlay-text" style="top:65%;font-size:12px;">Sunday, the 27 of September, 2028</div>
                <div class="overlay-text" style="top:75%;font-size:12px;">San Francisco, California</div>
                <div class="overlay-text" style="top:80%;font-size:11px;">Invitation to follow</div>
                <div class="qr-code-container" id="qrContainer"></div>
            </div>
        </div>

        <div class="controls">
            <h2>Edit Invitation Card</h2>

            <!-- Added Form Section -->
            <div class="form-section">
                <div class="section-title">Wedding Details</div>
                <div class="row">
                    <div class="col"><label for="brideName">Name 1</label><input type="text" id="brideName"
                            placeholder="Name"></div>
                    <div class="col"><label for="groomName">Name 2</label><input type="text" id="groomName"
                            placeholder="Name"></div>
                </div>
                <div class="row">
                    <div class="col"><label for="weddingDate">Date</label><input type="date" id="weddingDate"></div>
                    <div class="col"><label for="location">Location</label><input type="text" id="location"
                            placeholder="City"></div>
                </div>
            </div>

            <label for="text-input">Edit Selected Text</label>
            <textarea id="text-input" placeholder="Click any text on the card to edit it"></textarea>

            <div class="row">
                <div class="col">
                    <label for="font-select">Font</label>
                    <select id="font-select">
                        <option value="'Playfair Display', serif">Playfair Display</option>
                        <option value="'Great Vibes', cursive">Great Vibes</option>
                        <option value="'Dancing Script', cursive">Dancing Script</option>
                        <option value="'Poppins', sans-serif">Poppins</option>
                    </select>
                </div>
                <div class="col">
                    <label for="size-input">Font size (px)</label>
                    <input type="text" id="size-input" placeholder="e.g. 16" />
                </div>
            </div>

            <label for="color-input">Text Color</label>
            <input type="color" id="color-input" value="#222222" />

            <div class="actions">
                <button class="btn" id="save-wedding-btn">Save Wedding</button>
                <button class="btn btn-secondary" id="generate-qr-btn">Generate QR</button>
                <button class="btn" id="download-btn">Download</button>
            </div>

            <p class="note">Tip: Click text to select, edit content, change font, size or color, then save or download.
            </p>
        </div>
    </div>

    <script>
        let weddingData = JSON.parse(localStorage.getItem('wede_wedding_RG')) || {
            id: Date.now(),
            brideName: 'Caleb',
            groomName: 'Benjamin',
            weddingDate: '2028-09-27',
            location: 'San Francisco'
        };
        const RSVP_API_URL = 'save_rsvp.php';
        let rsvpUrl = '';

        window.addEventListener("DOMContentLoaded", () => {
            const selectedCard = localStorage.getItem("selected_card_RG");
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

        let activeEl = null;

        cardArea.querySelectorAll(".overlay-text").forEach(el => {
            makeDraggable(el);
            el.addEventListener("click", () => {
                document.querySelectorAll(".overlay-text").forEach(t => t.classList.remove("selected"));
                el.classList.add("selected");
                activeEl = el;
                textInput.value = el.textContent;
                fontSelect.value = el.style.fontFamily;
                sizeInput.value = parseInt(el.style.fontSize);
                colorInput.value = el.style.color || "#222222";
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

            if (!brideName || !groomName) { showToast("Enter names first!"); return; }

            // Update local storage
            weddingData.brideName = brideName;
            weddingData.groomName = groomName;
            weddingData.weddingDate = weddingDate;
            weddingData.location = location;
            localStorage.setItem('wede_wedding_RG', JSON.stringify(weddingData));

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

            const selectedCard = localStorage.getItem("selected_card_RG") || 'SR1.png';

            try {
                // 1. Save Card Design
                const response = await fetch('save_card.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        brideName, groomName, weddingDate, location,
                        cardTemplateId: 1,
                        customText: selectedCard,
                        cardDesign: { template: selectedCard, elements: cardElements }
                    })
                });
                const data = await response.json();

                if (data.success) {
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
                    await rsvpResponse.json(); // Wait but don't strictly require success

                    showToast("Card and Event saved successfully!");
                    fetchRsvpDetails(); // Refresh QR in case it changed
                } else { showToast("Error: " + data.message); }
            } catch (e) { showToast("Network error"); }
        });

        function generateQRCode() {
            const qrContainer = document.getElementById("qrContainer");
            qrContainer.innerHTML = '';

            if (rsvpUrl) {
                new QRCode(qrContainer, {
                    text: rsvpUrl, width: 70, height: 70, colorDark: '#222', colorLight: '#FFFFFF', correctLevel: QRCode.CorrectLevel.L
                });
            } else {
                qrContainer.innerHTML = '<span style="font-size:10px;text-align:center;">QR Loading...</span>';
            }
        }

        generateQRBtn.addEventListener("click", () => {
            if (rsvpUrl) generateQRCode();
            else fetchRsvpDetails();
        });

        downloadBtn.addEventListener("click", () => {
            document.querySelectorAll(".overlay-text").forEach(x => x.classList.remove("selected"));
            html2canvas(cardArea, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
                const a = document.createElement("a");
                a.href = canvas.toDataURL("image/png");
                a.download = "WEDÉ_Invitation.png";
                a.click();
                showToast('Card downloaded!');
            });
        });

        function showToast(msg) {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#4CAF50;color:white;padding:15px 20px;border-radius:8px;z-index:1000;';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>

</html>