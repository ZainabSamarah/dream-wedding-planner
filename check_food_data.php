<?php
require_once 'config.php';

if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT package_type, COUNT(*) as count FROM food_menu GROUP BY package_type");
        $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Food Menu Counts:\n";
        print_r($counts);

        $stmt = $pdo->query("SELECT * FROM food_menu LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nSample Data:\n";
        print_r($rows);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>