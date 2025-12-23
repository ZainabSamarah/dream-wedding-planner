<?php
require_once 'config.php';

echo "<h2>Updating Messages Table for Two-Way Messaging</h2>";

try {
    // Add reply_to_id column if it doesn't exist
    $columns = $pdo->query("DESCRIBE messages")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('reply_to_id', $columns)) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN reply_to_id INT NULL AFTER sent_at");
        echo "<p style='color:green;'>✓ Added reply_to_id column</p>";
    } else {
        echo "<p style='color:green;'>✓ reply_to_id column already exists</p>";
    }

    // Show updated structure
    echo "<h3>Updated Messages Table Structure:</h3>";
    $cols = $pdo->query("DESCRIBE messages")->fetchAll();
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($cols as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";

    echo "<br><p><a href='admin.php'>Go to Admin Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>