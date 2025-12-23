<?php
require_once 'config.php';

header('Content-Type: text/plain');

try {
    $tables = ['card_templates', 'user_card_selection'];

    foreach ($tables as $table) {
        echo "Structure of table: $table\n";
        echo "---------------------------\n";
        try {
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                echo "Field: {$column['Field']} | Type: {$column['Type']} | Null: {$column['Null']} | Key: {$column['Key']}\n";
            }
        } catch (PDOException $e) {
            echo "Error describing table $table: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>