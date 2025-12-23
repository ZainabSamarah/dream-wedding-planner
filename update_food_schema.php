<?php
require_once 'config.php';

try {
    $sql = "ALTER TABLE food_menu ADD COLUMN category VARCHAR(100) DEFAULT 'Main Dishes'";
    $pdo->exec($sql);
    echo "Column 'category' added successfully to 'food_menu' table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'category' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>