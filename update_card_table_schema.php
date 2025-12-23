<?php
/**
 * Update user_card_selection Table Schema
 * Adds columns for storing card customizations
 */

require_once 'config.php';

echo "===========================================\n";
echo "Updating user_card_selection Table Schema\n";
echo "===========================================\n\n";

try {
    // Check current structure
    echo "Step 1: Checking current table structure...\n";
    $stmt = $pdo->query("DESCRIBE user_card_selection");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current columns: " . implode(", ", $columns) . "\n\n";

    // Add new columns if they don't exist
    echo "Step 2: Adding new columns for card customizations...\n";

    $columnsToAdd = [
        'bride_name' => "ALTER TABLE user_card_selection ADD COLUMN bride_name VARCHAR(200)",
        'groom_name' => "ALTER TABLE user_card_selection ADD COLUMN groom_name VARCHAR(200)",
        'wedding_date' => "ALTER TABLE user_card_selection ADD COLUMN wedding_date DATE",
        'location' => "ALTER TABLE user_card_selection ADD COLUMN location VARCHAR(255)",
        'custom_text' => "ALTER TABLE user_card_selection ADD COLUMN custom_text TEXT",
        'card_design_json' => "ALTER TABLE user_card_selection ADD COLUMN card_design_json TEXT COMMENT 'Stores full card customization as JSON'"
    ];

    $addedCount = 0;
    foreach ($columnsToAdd as $columnName => $sql) {
        if (!in_array($columnName, $columns)) {
            try {
                $pdo->exec($sql);
                echo "  ✓ Added column: $columnName\n";
                $addedCount++;
            } catch (PDOException $e) {
                echo "  ⚠ Could not add $columnName: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  → Column '$columnName' already exists\n";
        }
    }

    echo "\n";
    if ($addedCount > 0) {
        echo "✓ Added $addedCount new column(s)\n\n";
    } else {
        echo "✓ All columns already exist\n\n";
    }

    // Show updated structure
    echo "Step 3: Updated table structure:\n";
    $stmt = $pdo->query("DESCRIBE user_card_selection");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($structure as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }

    echo "\n===========================================\n";
    echo "✓✓✓ TABLE UPDATE COMPLETED!\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>