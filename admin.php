<?php
require_once 'config.php';

// Check if user is logged in and has admin/owner privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'owner')) {
    header("Location: login.php");
    exit();
}

// Fetch basic stats
try {
    $stats = [
        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'bookings' => $pdo->query("SELECT (SELECT COUNT(*) FROM user_food_selections) + (SELECT COUNT(*) FROM user_cake_selections)")->fetchColumn(),
        'services' => $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    ];
} catch (PDOException $e) {
    $stats = ['users' => 0, 'bookings' => 0, 'services' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEDÉ Management Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #4B5945;
            --secondary: #66785F;
            --accent: #91AC8F;
            --light: #B2C9AD;
            --glass: rgba(255, 255, 255, 0.7);
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #E8EFE8;
            color: var(--primary);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid #ddd;
            height: 100vh;
            position: fixed;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .logo {
            font-family: 'Great Vibes', cursive;
            font-size: 40px;
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary);
        }

        .nav-links {
            list-style: none;
            flex-grow: 1;
        }

        .nav-group {
            margin-bottom: 25px;
        }

        .nav-group-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #999;
            margin: 0 0 10px 10px;
            letter-spacing: 1px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            text-decoration: none;
            color: #666;
            border-radius: 12px;
            transition: 0.3s;
            cursor: pointer;
            font-size: 15px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #f0f7f0;
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 40px;
            width: calc(100% - var(--sidebar-width));
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }

        .stat-card .val {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }

        /* Sections */
        .section {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            transition: 0.3s;
            font-size: 14px;
        }

        .btn-add {
            background: var(--primary);
            color: white;
        }

        .btn-add:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-edit {
            background: #f0f0f0;
            color: var(--primary);
            padding: 5px 10px;
            margin-right: 5px;
        }

        .btn-del {
            background: #ffebee;
            color: #d32f2f;
            padding: 5px 10px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f8f8f8;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:hover {
            background: #fafafa;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-reg {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-med {
            background: #fff3e0;
            color: #ef6c00;
        }

        .badge-lux {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 500px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-title {
            margin-bottom: 25px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="logo">WEDÉ</div>
        <nav class="nav-links">
            <div class="nav-group">
                <div class="nav-group-title">Overview</div>
                <a class="nav-link active" onclick="showTab('dashboard')"><i class="fas fa-th-large"></i> Dashboard</a>
                <a class="nav-link" onclick="showTab('selections')"><i class="fas fa-clipboard-check"></i> Customer
                    Selections</a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">System</div>
                <a class="nav-link" onclick="showTab('users')"><i class="fas fa-users"></i> Users</a>
                <a class="nav-link" onclick="showTab('messages')"><i class="fas fa-envelope"></i> Messages</a>
                <a class="nav-link" onclick="showTab('packages')"><i class="fas fa-cube"></i> Packages</a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">Marketplace</div>
                <a class="nav-link" onclick="showTab('services')"><i class="fas fa-concierge-bell"></i> Services</a>
                <a class="nav-link" onclick="showTab('food')"><i class="fas fa-utensils"></i> Food Menu</a>
                <a class="nav-link" onclick="showTab('cakes')"><i class="fas fa-birthday-cake"></i> Cake Menu</a>
                <a class="nav-link" onclick="showTab('cards')"><i class="fas fa-id-card"></i> Card Templates</a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">Content</div>
                <a class="nav-link" onclick="showTab('tips')"><i class="fas fa-lightbulb"></i> Planning Tips</a>
                <a class="nav-link" onclick="showTab('vendors')"><i class="fas fa-store"></i> Vendors</a>
            </div>
        </nav>
        <button class="btn" style="background:#f0f0f0; color:#d32f2f;"
            onclick="location.href='logout.php'">Logout</button>
    </aside>

    <main class="main">
        <header>
            <h2 id="page-title">Dashboard</h2>
            <div style="text-align: right;">
                <p style="font-size: 14px; color:#888;">Welcome, <strong><?php echo $_SESSION['first_name']; ?></strong>
                </p>
            </div>
        </header>

        <!-- DASHBOARD -->
        <div id="dashboard" class="section active">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="val"><?php echo $stats['users']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Package Selections</h3>
                    <div class="val"><?php echo $stats['bookings']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Services</h3>
                    <div class="val"><?php echo $stats['services']; ?></div>
                </div>
            </div>
            <div class="card">
                <h3>Quick Actions</h3>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button class="btn btn-add" onclick="openModal('user')">Add New User</button>
                    <button class="btn btn-add" onclick="openModal('service')">Add New Service</button>
                    <button class="btn btn-add" style="background:var(--secondary)" onclick="showTab('email')">Send
                        Broadcast</button>
                </div>
            </div>
        </div>

        <!-- SHARED TABLE VIEW -->
        <div id="table-view" class="section">
            <div class="card">
                <div class="card-header">
                    <h3 id="table-title">Management</h3>
                    <button class="btn btn-add" id="table-add-btn">Add New</button>
                </div>
                <table>
                    <thead id="table-head"></thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>
        </div>

        <!-- EMAIL VIEW -->
        <div id="email" class="section">
            <div class="card">
                <h3>Broadcast Email</h3>
                <div style="margin-top:20px;">
                    <div class="form-group"><label>Recipient</label><input type="text" id="mail-to" value="All Users">
                    </div>
                    <div class="form-group"><label>Subject</label><input type="text" id="mail-subj"
                            placeholder="Enter subject"></div>
                    <div class="form-group"><label>Message</label><textarea id="mail-msg" rows="10"
                            placeholder="Type your message..."></textarea></div>
                    <button class="btn btn-add" onclick="sendEmail()">Send Now</button>
                </div>
            </div>
        </div>

        <!-- MESSAGES VIEW -->
        <div id="messages" class="section">
            <div class="card">
                <div class="card-header">
                    <h3>Incoming Messages</h3>
                    <span id="unread-count"
                        style="background:var(--accent); color:white; padding:5px 12px; border-radius:20px; font-size:13px;">Loading...</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="messages-body">
                        <!-- Messages will be loaded here automatically -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- FORM MODAL -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <h3 class="modal-title" id="modal-title">Edit Entry</h3>
            <form id="entry-form">
                <input type="hidden" name="id" id="f-id">
                <input type="hidden" name="action" id="f-action">
                <div id="form-fields"></div>
                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-add" style="flex:1">Save Changes</button>
                    <button type="button" class="btn" style="flex:1; background:#f0f0f0;"
                        onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REPLY MODAL -->
    <div class="modal" id="reply-modal">
        <div class="modal-content" style="max-width:600px;">
            <h3 class="modal-title">Reply to Message</h3>
            <div id="original-message"
                style="background:#f8f8f8; padding:15px; border-radius:10px; margin-bottom:20px;">
                <p style="font-size:12px; color:#888; margin-bottom:5px;">Original message from <strong
                        id="msg-sender"></strong>:</p>
                <p id="msg-content" style="font-size:14px; line-height:1.6;"></p>
            </div>
            <form id="reply-form">
                <input type="hidden" name="message_id" id="reply-msg-id">
                <div class="form-group">
                    <label>Your Reply</label>
                    <textarea name="reply" id="reply-content" rows="5" placeholder="Type your reply..." required
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px; font-family:inherit;"></textarea>
                </div>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-add" style="flex:1"><i class="fas fa-paper-plane"></i> Send
                        Reply</button>
                    <button type="button" class="btn" style="flex:1; background:#f0f0f0;"
                        onclick="closeReplyModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentTab = 'dashboard';

        function showTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            event?.currentTarget?.classList?.add('active');

            if (tab === 'dashboard' || tab === 'email') {
                document.getElementById(tab).classList.add('active');
                document.getElementById('page-title').innerText = tab.charAt(0).toUpperCase() + tab.slice(1);
            }
            else if (tab === 'messages') {
                document.getElementById('messages').classList.add('active');
                document.getElementById('page-title').innerText = 'Messages';
                loadMessages();
            }
            else {
                document.getElementById('table-view').classList.add('active');
                document.getElementById('page-title').innerText = tab.charAt(0).toUpperCase() + tab.slice(1) + ' Management';
                loadTableData(tab);
            }
        }

        async function loadTableData(type) {
            const head = document.getElementById('table-head');
            const btn = document.getElementById('table-add-btn');

            if (type === 'selections') btn.style.display = 'none';
            else {
                btn.style.display = 'block';
                btn.onclick = () => openModal(type);
            }

            const res = await fetch(`admin_api.php?action=get_${type}`);
            const json = await res.json();
            if (!json.success) return;

            let html = '<tr>';
            if (type === 'users') html += '<th>Name</th><th>Email</th><th>Role</th><th>Actions</th>';
            else if (type === 'packages') html += '<th>ID</th><th>Package Name</th><th>Created</th><th>Actions</th>';
            else if (type === 'services') html += '<th>Name</th><th>Category</th><th>Price</th><th>Actions</th>';
            else if (type === 'food') html += '<th>Type</th><th>Name</th><th>Category</th><th>Actions</th>';
            else if (type === 'cakes') html += '<th>Type</th><th>Name</th><th>Category</th><th>Actions</th>';
            else if (type === 'cards') html += '<th>Type</th><th>Template Name</th><th>Actions</th>';
            else if (type === 'tips') html += '<th>Title</th><th>Content Snippet</th><th>Actions</th>';
            else if (type === 'vendors') html += '<th>Name</th><th>Category</th><th>Actions</th>';
            else if (type === 'selections') html += '<th>Customer</th><th>Package</th><th>Menu Selections</th><th>Actions</th>';
            html += '</tr>';
            head.innerHTML = html;

            const tbody = document.getElementById('table-body');
            tbody.innerHTML = json.data.map(item => {
                let row = '<tr>';
                if (type === 'users') row += `<td>${item.first_name} ${item.last_name}</td><td>${item.email}</td><td><span class="badge badge-reg">${item.role}</span></td>`;
                else if (type === 'packages') row += `<td>#${item.id}</td><td><strong>${item.name}</strong></td><td>${item.created_at}</td>`;
                else if (type === 'services') row += `<td>${item.name}</td><td>${item.category}</td><td>$${item.price}</td>`;
                else if (type === 'food') row += `<td><span class="badge badge-${item.package_type.substring(0, 3)}">${item.package_type}</span></td><td>${item.name}</td><td>${item.category}</td>`;
                else if (type === 'cakes') row += `<td><span class="badge badge-${item.package_type.substring(0, 3)}">${item.package_type}</span></td><td>${item.name}</td><td>${item.category}</td>`;
                else if (type === 'cards') row += `<td><span class="badge badge-${item.package_type.substring(0, 3)}">${item.package_type}</span></td><td>${item.template_name}</td>`;
                else if (type === 'tips') row += `<td>${item.title}</td><td>${item.content.substring(0, 50)}...</td>`;
                else if (type === 'vendors') row += `<td>${item.name}</td><td>${item.category}</td>`;
                else if (type === 'selections') row += `
                    <td><strong>${item.full_name}</strong><br><small>${item.email}</small><br><span style="font-size:11px; color:#888;">Wedding: ${item.wedding_date}</span></td>
                    <td><span class="badge badge-${item.package_type.substring(0, 3)}">${item.package_type}</span></td>
                    <td style="font-size:12px;">
                        <div style="margin-bottom:5px;"><strong>Food:</strong> ${item.selected_food || 'None'}</div>
                        <div><strong>Cakes:</strong> ${item.selected_cakes || 'None'}</div>
                    </td>`;

                row += `<td>
                    <button class="btn btn-edit" onclick='editEntry("${type}", ${JSON.stringify(item).replace(/'/g, "&apos;")})'><i class="fas fa-edit"></i></button>
                    <button class="btn btn-del" onclick='deleteEntry("${type}", ${item.id})'><i class="fas fa-trash"></i></button>
                </td></tr>`;
                return row;
            }).join('');
        }

        function openModal(type, data = null) {
            document.getElementById('modal').style.display = 'flex';
            document.getElementById('modal-title').innerText = (data ? 'Edit ' : 'Add ') + type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('f-id').value = data ? data.id : 0;
            document.getElementById('f-action').value = `save_${type.replace(/s$/, '')}`; // singularize

            let fields = '';
            if (type === 'users') {
                fields = `
                    <div class="form-group"><label>First Name</label><input name="first_name" required value="${data?.first_name || ''}"></div>
                    <div class="form-group"><label>Last Name</label><input name="last_name" required value="${data?.last_name || ''}"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" required value="${data?.email || ''}"></div>
                    <div class="form-group"><label>Role</label><select name="role"><option value="user" ${data?.role === 'user' ? 'selected' : ''}>User</option><option value="admin" ${data?.role === 'admin' ? 'selected' : ''}>Admin</option></select></div>
                    <div class="form-group"><label>Password (leave blank to keep same)</label><input type="password" name="password"></div>
                `;
            } else if (type === 'packages') {
                fields = `<div class="form-group"><label>Package Name</label><input name="name" required value="${data?.name || ''}"></div>`;
            } else if (type === 'services') {
                fields = `
                    <div class="form-group"><label>Service Name</label><input name="name" required value="${data?.name || ''}"></div>
                    <div class="form-group"><label>Category</label><select name="category"><option value="food">Food</option><option value="decoration">Decoration</option><option value="photography">Photography</option><option value="other">Other</option></select></div>
                    <div class="form-group"><label>Price</label><input type="number" name="price" value="${data?.price || 0}"></div>
                `;
            } else if (type === 'food' || type === 'cakes') {
                fields = `
                    <div class="form-group"><label>Package Type</label><select name="package_type"><option value="regular">Regular</option><option value="medium">Medium</option><option value="luxury">Luxury</option></select></div>
                    <div class="form-group"><label>Name</label><input name="name" required value="${data?.name || ''}"></div>
                    <div class="form-group"><label>Description</label><textarea name="description">${data?.description || ''}</textarea></div>
                    <div class="form-group"><label>Image URL</label><input name="image_url" value="${data?.image_url || ''}"></div>
                    <div class="form-group"><label>Category</label><input name="category" value="${data?.category || ''}"></div>
                `;
            } else if (type === 'cards') {
                fields = `
                    <div class="form-group"><label>Package Type</label><select name="package_type"><option value="regular">Regular</option><option value="medium">Medium</option><option value="luxury">Luxury</option></select></div>
                    <div class="form-group"><label>Template Name</label><input name="template_name" required value="${data?.template_name || ''}"></div>
                    <div class="form-group"><label>Preview Image URL</label><input name="preview_image" value="${data?.preview_image || ''}"></div>
                    <div class="form-group"><label>Design JSON</label><textarea name="design_json" rows="5">${data?.design_json || ''}</textarea></div>
                `;
            } else if (type === 'tips') {
                fields = `
                    <div class="form-group"><label>Title</label><input name="title" required value="${data?.title || ''}"></div>
                    <div class="form-group"><label>Content</label><textarea name="content" required rows="10">${data?.content || ''}</textarea></div>
                `;
            } else if (type === 'vendors') {
                fields = `
                    <div class="form-group"><label>Vendor Name</label><input name="name" required value="${data?.name || ''}"></div>
                    <div class="form-group"><label>Category</label><input name="category" value="${data?.category || ''}"></div>
                    <div class="form-group"><label>Contact Info</label><input name="contact_info" value="${data?.contact_info || ''}"></div>
                    <div class="form-group"><label>Description</label><textarea name="description">${data?.description || ''}</textarea></div>
                `;
            }

            document.getElementById('form-fields').innerHTML = fields;
        }

        function closeModal() { document.getElementById('modal').style.display = 'none'; }

        document.getElementById('entry-form').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            const res = await fetch('admin_api.php', { method: 'POST', body: fd });
            const json = await res.json();
            if (json.success) {
                closeModal();
                showTab(currentTab);
            } else alert(json.message);
        };

        async function deleteEntry(type, id) {
            if (!confirm("Are you sure?")) return;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('action', `delete_${type.replace(/s$/, '')}`);
            await fetch('admin_api.php', { method: 'POST', body: fd });
            showTab(currentTab);
        }

        function editEntry(type, data) { openModal(type, data); }

        async function sendEmail() {
            const fd = new FormData();
            fd.append('action', 'send_email');
            fd.append('to', document.getElementById('mail-to').value);
            fd.append('subject', document.getElementById('mail-subj').value);
            fd.append('message', document.getElementById('mail-msg').value);
            const res = await fetch('admin_api.php', { method: 'POST', body: fd });
            const json = await res.json();
            alert(json.message);
        }

        /* Messages Functions */
        async function loadMessages() {
            const res = await fetch('admin_api.php?action=get_messages');
            const json = await res.json();

            if (!json.success) {
                document.getElementById('messages-body').innerHTML = '<tr><td colspan="5" style="text-align:center;">Failed to load messages</td></tr>';
                document.getElementById('unread-count').innerText = 'Error';
                return;
            }

            const tbody = document.getElementById('messages-body');
            let unread = 0;

            if (json.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">No messages yet</td></tr>';
                document.getElementById('unread-count').innerText = 'No new messages';
                return;
            }

            tbody.innerHTML = json.data.map(msg => {
                if (msg.is_read == 0) unread++;

                const sender = msg.guest_name
                    ? `${msg.guest_name}<br><small>${msg.guest_email || ''}</small>`
                    : `<strong>${msg.sender_name || 'Registered User'}</strong>`;

                const preview = msg.content.length > 120
                    ? msg.content.substring(0, 120) + '...'
                    : msg.content;

                return `
                    <tr style="${msg.is_read == 0 ? 'background:#f8fff8;' : ''}">
                        <td>${sender}</td>
                        <td style="max-width:350px; word-wrap:break-word;">${preview}</td>
                        <td>${msg.sent_at}</td>
                        <td>
                            <span class="badge ${msg.is_read == 1 ? 'badge-reg' : 'badge-med'}">
                                ${msg.is_read == 1 ? 'Read' : 'New'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-edit" onclick='openReplyModal(${JSON.stringify(msg).replace(/'/g, "&apos;")})' title="Reply">
                                <i class="fas fa-reply"></i>
                            </button>
                            <button class="btn btn-edit" onclick="markAsRead(${msg.id})" title="Mark as Read">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-del" onclick="deleteMessage(${msg.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            }).join('');

            document.getElementById('unread-count').innerText =
                unread > 0 ? `${unread} new message${unread > 1 ? 's' : ''}` : 'No new messages';
        }

        async function markAsRead(id) {
            const fd = new FormData();
            fd.append('action', 'mark_read');
            fd.append('id', id);
            await fetch('admin_api.php', { method: 'POST', body: fd });
            loadMessages();
        }

        async function deleteMessage(id) {
            if (!confirm('Are you sure you want to delete this message?')) return;
            const fd = new FormData();
            fd.append('action', 'delete_message');
            fd.append('id', id);
            await fetch('admin_api.php', { method: 'POST', body: fd });
            loadMessages();
        }

        /* Reply Functions */
        function openReplyModal(msg) {
            document.getElementById('reply-modal').style.display = 'flex';
            document.getElementById('reply-msg-id').value = msg.id;
            document.getElementById('msg-sender').innerText = msg.guest_name || msg.sender_name || 'Registered User';
            document.getElementById('msg-content').innerText = msg.content;
            document.getElementById('reply-content').value = '';

            // Check if this is a guest message
            if (!msg.from_user_id && msg.guest_email) {
                document.getElementById('reply-content').placeholder = `Reply will be noted (Guest: ${msg.guest_email})`;
            } else {
                document.getElementById('reply-content').placeholder = 'Type your reply... (will be sent to user inbox)';
            }
        }

        function closeReplyModal() {
            document.getElementById('reply-modal').style.display = 'none';
        }

        document.getElementById('reply-form').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('action', 'send_reply');
            const res = await fetch('admin_api.php', { method: 'POST', body: fd });
            const json = await res.json();
            if (json.success) {
                closeReplyModal();
                loadMessages();
                alert('Reply sent successfully!');
            } else {
                alert(json.message || 'Failed to send reply');
            }
        };
    </script>
</body>

</html>