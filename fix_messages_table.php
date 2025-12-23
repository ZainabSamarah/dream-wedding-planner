<?php
require_once 'config.php';

echo "<h2>Fix Messages Table</h2>";

try {
    // Check current columns
    $columns = $pdo->query("DESCRIBE messages")->fetchAll(PDO::FETCH_COLUMN);

    echo "<p>Current columns: " . implode(', ', $columns) . "</p>";

    // Add guest_name if missing
    if (!in_array('guest_name', $columns)) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN guest_name VARCHAR(255) NULL AFTER content");
        echo "<p style='color:green;'>✓ Added guest_name column</p>";
    } else {
        echo "<p>✓ guest_name column already exists</p>";
    }

    // Add guest_email if missing
    if (!in_array('guest_email', $columns)) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN guest_email VARCHAR(255) NULL AFTER guest_name");
        echo "<p style='color:green;'>✓ Added guest_email column</p>";
    } else {
        echo "<p>✓ guest_email column already exists</p>";
    }

    // Check for owners
    $owners = $pdo->query("SELECT id, first_name, last_name, email FROM users WHERE role = 'owner'")->fetchAll();

    if (empty($owners)) {
        echo "<p style='color:orange;'>⚠ No owner accounts found. Creating default owner...</p>";
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'owner')");
        $stmt->execute(['Admin', 'Owner', 'owner@wede.com', password_hash('owner123', PASSWORD_DEFAULT)]);
        echo "<p style='color:green;'>✓ Created owner: owner@wede.com / password: owner123</p>";
    } else {
        echo "<p style='color:green;'>✓ Found " . count($owners) . " owner(s)</p>";
    }

    echo "<h3>Updated Table Structure:</h3>";
    $columns = $pdo->query("DESCRIBE messages")->fetchAll();
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table>";

    echo "<br><p style='color:green; font-size:18px;'><strong>✓ Table is now ready!</strong></p>";
    echo "<p><a href='contact.php'>Try Contact Form</a> | <a href='admin.php'>Go to Admin</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>