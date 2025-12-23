<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['first_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEDÉ - My Messages</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark: #4B5945;
            --green-medium: #66785F;
            --green-light: #91AC8F;
            --green-pale: #B2C9AD;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f5f0;
            color: var(--green-dark);
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
            background-color: var(--green-medium);
            transform: translateY(-2px);
        }

        .main-container {
            max-width: 1000px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 42px;
            color: var(--green-dark);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .tab-btn {
            padding: 12px 25px;
            border: none;
            background: white;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: inherit;
            color: var(--green-medium);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .tab-btn.active,
        .tab-btn:hover {
            background: var(--green-medium);
            color: white;
        }

        .tab-btn .badge {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 8px;
        }

        .messages-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .compose-box {
            background: linear-gradient(135deg, var(--green-pale) 0%, #d4e5d0 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .compose-box h3 {
            margin-bottom: 15px;
            color: var(--green-dark);
        }

        .compose-box textarea {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            min-height: 100px;
            resize: vertical;
            margin-bottom: 15px;
        }

        .compose-box textarea:focus {
            outline: 2px solid var(--green-medium);
        }

        .send-btn {
            background: var(--green-medium);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: inherit;
        }

        .send-btn:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }

        .send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .message-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 0;
            display: flex;
            gap: 15px;
            transition: background 0.3s;
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-item.unread {
            background: #f8fff8;
            margin: 0 -20px;
            padding: 20px;
            border-radius: 10px;
        }

        .message-item:hover {
            background: #fafafa;
        }

        .msg-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .msg-avatar.owner {
            background: var(--green-dark);
        }

        .msg-content {
            flex: 1;
        }

        .msg-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .msg-sender {
            font-weight: 600;
            color: var(--green-dark);
        }

        .msg-sender .role-badge {
            background: var(--green-light);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 8px;
            font-weight: 700;
        }

        .msg-date {
            font-size: 12px;
            color: #888;
        }

        .msg-text {
            color: #555;
            line-height: 1.6;
        }

        .msg-actions {
            margin-top: 10px;
        }

        .msg-actions button {
            background: #f0f0f0;
            border: none;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            margin-right: 10px;
            transition: 0.3s;
            font-family: inherit;
        }

        .msg-actions button:hover {
            background: var(--green-pale);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--green-pale);
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .alert.error {
            background: #ffebee;
            color: #d32f2f;
        }

        footer {
            background-color: var(--green-medium);
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <header>
        <h1>WEDÉ</h1>
        <nav>
            <a href="services.php">Services</a>
            <a href="profile.php">My Profile</a>
            <a href="my_messages.php" style="color:var(--green-pale);"><i class="fas fa-envelope"></i> Messages</a>
            <button class="nav-btn" onclick="location.href='logout.php'">Logout</button>
        </nav>
    </header>

    <div class="main-container">
        <div class="page-header">
            <h2>My Messages</h2>
            <span style="color:#888;">Welcome, <?php echo htmlspecialchars($userName); ?></span>
        </div>

        <div id="alert" class="alert"></div>

        <div class="compose-box">
            <h3><i class="fas fa-pen"></i> Send a Message to WEDÉ Team</h3>
            <form id="compose-form">
                <textarea id="compose-message"
                    placeholder="Write your message here... Our team will respond as soon as possible!"></textarea>
                <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('inbox')">
                <i class="fas fa-inbox"></i> Inbox <span id="unread-badge" class="badge" style="display:none;">0</span>
            </button>
            <button class="tab-btn" onclick="switchTab('sent')">
                <i class="fas fa-paper-plane"></i> Sent
            </button>
        </div>

        <div class="messages-card">
            <div id="inbox-content">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h3>Loading messages...</h3>
                </div>
            </div>
            <div id="sent-content" style="display:none;">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h3>Loading messages...</h3>
                </div>
            </div>
        </div>
    </div>

    <footer>
        © 2025 <span style="color: var(--green-pale);">WEDÉ</span> | Your Dream Wedding Awaits
    </footer>

    <script>
        let currentTab = 'inbox';

        function switchTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');

            document.getElementById('inbox-content').style.display = tab === 'inbox' ? 'block' : 'none';
            document.getElementById('sent-content').style.display = tab === 'sent' ? 'block' : 'none';

            if (tab === 'inbox') loadInbox();
            else loadSent();
        }

        async function loadInbox() {
            const container = document.getElementById('inbox-content');
            const res = await fetch('user_messages_api.php?action=get_messages');
            const json = await res.json();

            if (!json.success || json.data.length === 0) {
                container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-envelope-open"></i>
                <h3>No messages yet</h3>
                <p>Messages from the WEDÉ team will appear here</p>
            </div>`;
                return;
            }

            let unread = 0;
            container.innerHTML = json.data.map(msg => {
                if (msg.is_read == 0) unread++;
                const initial = msg.sender_name ? msg.sender_name.charAt(0).toUpperCase() : 'W';
                const isOwner = msg.sender_role === 'owner' || msg.sender_role === 'admin';
                return `
            <div class="message-item ${msg.is_read == 0 ? 'unread' : ''}">
                <div class="msg-avatar ${isOwner ? 'owner' : ''}">${initial}</div>
                <div class="msg-content">
                    <div class="msg-header">
                        <span class="msg-sender">
                            ${msg.sender_name || 'WEDÉ Team'}
                            ${isOwner ? '<span class="role-badge">WEDÉ Team</span>' : ''}
                        </span>
                        <span class="msg-date">${msg.sent_at}</span>
                    </div>
                    <div class="msg-text">${msg.content}</div>
                    ${msg.is_read == 0 ? `
                        <div class="msg-actions">
                            <button onclick="markRead(${msg.id})"><i class="fas fa-check"></i> Mark as Read</button>
                        </div>
                    ` : ''}
                </div>
            </div>`;
            }).join('');

            // Update badge
            const badge = document.getElementById('unread-badge');
            if (unread > 0) {
                badge.style.display = 'inline';
                badge.innerText = unread;
            } else {
                badge.style.display = 'none';
            }
        }

        async function loadSent() {
            const container = document.getElementById('sent-content');
            const res = await fetch('user_messages_api.php?action=get_sent_messages');
            const json = await res.json();

            if (!json.success || json.data.length === 0) {
                container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-paper-plane"></i>
                <h3>No sent messages</h3>
                <p>Messages you send will appear here</p>
            </div>`;
                return;
            }

            container.innerHTML = json.data.map(msg => {
                return `
            <div class="message-item">
                <div class="msg-avatar"><i class="fas fa-arrow-right"></i></div>
                <div class="msg-content">
                    <div class="msg-header">
                        <span class="msg-sender">To: ${msg.recipient_name || 'WEDÉ Team'}</span>
                        <span class="msg-date">${msg.sent_at}</span>
                    </div>
                    <div class="msg-text">${msg.content}</div>
                </div>
            </div>`;
            }).join('');
        }

        async function markRead(id) {
            const fd = new FormData();
            fd.append('action', 'mark_read');
            fd.append('id', id);
            await fetch('user_messages_api.php', { method: 'POST', body: fd });
            loadInbox();
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.className = 'alert ' + type;
            alert.innerText = message;
            alert.style.display = 'block';
            setTimeout(() => { alert.style.display = 'none'; }, 4000);
        }

        document.getElementById('compose-form').onsubmit = async (e) => {
            e.preventDefault();
            const textarea = document.getElementById('compose-message');
            const message = textarea.value.trim();

            if (!message) {
                showAlert('Please enter a message', 'error');
                return;
            }

            const fd = new FormData();
            fd.append('action', 'send_message');
            fd.append('message', message);

            const res = await fetch('user_messages_api.php', { method: 'POST', body: fd });
            const json = await res.json();

            if (json.success) {
                showAlert('Message sent successfully! The team will respond soon.', 'success');
                textarea.value = '';
                loadSent();
            } else {
                showAlert(json.message || 'Failed to send message', 'error');
            }
        };

        // Load inbox on page load
        loadInbox();
    </script>

</body>

</html>