<?php
require 'config.php';

try {
    $tables = ['packages', 'user_packages'];
    $missing = [];

    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            $missing[] = $table;
        }
    }

    if (empty($missing)) {
        echo json_encode(['status' => 'success', 'message' => 'All tables exist']);
    } else {
        echo json_encode(['status' => 'error', 'missing' => $missing]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]);
}
?>