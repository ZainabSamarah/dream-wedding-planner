<?php
require_once 'config.php';

try {
    // Create user_decorations table
    $sql = "CREATE TABLE IF NOT EXISTS user_decorations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        theme VARCHAR(100) DEFAULT NULL,
        flowers VARCHAR(100) DEFAULT NULL,
        lighting VARCHAR(100) DEFAULT NULL,
        centerpieces VARCHAR(100) DEFAULT NULL,
        custom_notes TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "<h2>Setting up Decoration Tables</h2>";
    echo "<p style='color:green;'>✓ Created user_decorations table</p>";

    echo "<br><h3 style='color:green;'>✓ All Decoration tables created successfully!</h3>";
    echo "<p><a href='services.php'>Go to Services</a></p>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>