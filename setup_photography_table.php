<?php
require_once 'config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS user_photography (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_date DATE DEFAULT NULL,
        session_time TIME DEFAULT NULL,
        location VARCHAR(255) DEFAULT NULL,
        notes TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_photo (user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Table 'user_photography' created or already exists successfully.";

} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}
?>