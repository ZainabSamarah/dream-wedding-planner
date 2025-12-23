<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT count(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Successfully connected to database 'wedding_db'. User count: $count</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Query failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ \$pdo variable is not set.</p>";
}
?>