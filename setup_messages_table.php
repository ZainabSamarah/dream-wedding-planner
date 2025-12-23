<?php
require_once 'config.php';

echo "<h2>Messages Table Setup</h2>";

try {
    // Check if messages table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'messages'")->fetch();

    if (!$tableCheck) {
        echo "<p>Messages table does not exist. Creating...</p>";

        // Create the messages table
        $sql = "CREATE TABLE messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_user_id INT NULL,
            to_user_id INT NOT NULL,
            content TEXT NOT NULL,
            guest_name VARCHAR(255) NULL,
            guest_email VARCHAR(255) NULL,
            is_read TINYINT(1) DEFAULT 0,
            sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $pdo->exec($sql);
        echo "<p style='color:green;'>✓ Messages table created successfully!</p>";
    } else {
        echo "<p style='color:green;'>✓ Messages table already exists.</p>";
    }

    // Check for owners in the system
    $owners = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role = 'owner'")->fetchAll();

    if (empty($owners)) {
        echo "<p style='color:red;'>✗ No owner accounts found! Messages need an owner to be sent to.</p>";
        echo "<p>Creating a default owner account...</p>";

        // Create default owner
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'owner')");
        $stmt->execute(['Admin', 'Owner', 'owner@wede.com', password_hash('owner123', PASSWORD_DEFAULT)]);

        echo "<p style='color:green;'>✓ Default owner created: owner@wede.com / password: owner123</p>";
    } else {
        echo "<p style='color:green;'>✓ Found " . count($owners) . " owner(s):</p>";
        echo "<ul>";
        foreach ($owners as $owner) {
            echo "<li>{$owner['first_name']} {$owner['last_name']} ({$owner['email']}) - ID: {$owner['id']}</li>";
        }
        echo "</ul>";
    }

    // Show table structure
    echo "<h3>Messages Table Structure:</h3>";
    $columns = $pdo->query("DESCRIBE messages")->fetchAll();
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";

    echo "<br><p><a href='contact.php'>Go to Contact Page</a> | <a href='admin.php'>Go to Admin Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>