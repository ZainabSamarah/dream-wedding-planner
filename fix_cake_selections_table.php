<?php
/**
 * Fix user_cake_selections Table
 * Renames cake_menu_id to cake_id for consistency
 */

require_once 'config.php';

echo "===========================================\n";
echo "Fixing user_cake_selections Table\n";
echo "===========================================\n\n";

try {
    // Check current structure
    echo "Step 1: Checking current table structure...\n";
    $stmt = $pdo->query("DESCRIBE user_cake_selections");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current columns: " . implode(", ", $columns) . "\n\n";

    // Check if we need to rename column
    if (in_array('cake_menu_id', $columns) && !in_array('cake_id', $columns)) {
        echo "Step 2: Renaming 'cake_menu_id' to 'cake_id'...\n";

        // Drop foreign key first
        try {
            $pdo->exec("ALTER TABLE user_cake_selections DROP FOREIGN KEY user_cake_selections_ibfk_2");
            echo "  ✓ Dropped old foreign key\n";
        } catch (PDOException $e) {
            echo "  → No foreign key to drop (or different name)\n";
        }

        // Rename column
        $pdo->exec("ALTER TABLE user_cake_selections CHANGE cake_menu_id cake_id INT(11) NOT NULL");
        echo "  ✓ Renamed column: cake_menu_id → cake_id\n";

        // Re-add foreign key (optional, depends on your schema)
        // Uncomment if you have a cakes table with id column
        // $pdo->exec("ALTER TABLE user_cake_selections ADD FOREIGN KEY (cake_id) REFERENCES cakes(id) ON DELETE CASCADE");
        // echo "  ✓ Added new foreign key\n";

        echo "\n✓ Column renamed successfully!\n\n";
    } else if (in_array('cake_id', $columns)) {
        echo "Step 2: Column 'cake_id' already exists\n";
        echo "✓ No changes needed!\n\n";
    } else {
        echo "⚠ WARNING: Neither 'cake_menu_id' nor 'cake_id' found!\n\n";
    }

    // Show updated structure
    echo "Step 3: Updated table structure:\n";
    $stmt = $pdo->query("DESCRIBE user_cake_selections");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($structure as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }

    echo "\n===========================================\n";
    echo "✓✓✓ TABLE FIX COMPLETED!\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>