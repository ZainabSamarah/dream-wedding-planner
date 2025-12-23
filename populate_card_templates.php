<?php
/**
 * Populate card_templates table with default templates
 */

require_once 'config.php';

echo "===========================================\n";
echo "Populating card_templates Table\n";
echo "===========================================\n\n";

try {
    // Check if templates already exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM card_templates");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "✓ Card templates already exist ({$result['count']} templates)\n";
        echo "Showing existing templates:\n\n";

        $stmt = $pdo->query("SELECT * FROM card_templates");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($templates as $template) {
            echo "  ID: {$template['id']} - {$template['template_name']} ({$template['package_type']})\n";
        }

        echo "\n===========================================\n";
        echo "✓ No changes needed\n";
        echo "===========================================\n";
        exit(0);
    }

    // Insert default templates
    echo "Step 1: Inserting default card templates...\n";

    $templates = [
        [1, 'regular', 'Classic Elegance'],
        [2, 'medium', 'Modern Grace'],
        [3, 'luxury', 'Royal Luxury']
    ];

    $stmt = $pdo->prepare("
        INSERT INTO card_templates (id, package_type, template_name) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE template_name = VALUES(template_name)
    ");

    foreach ($templates as $template) {
        $stmt->execute($template);
        echo "  ✓ Added template: {$template[2]} (ID: {$template[0]}, Package: {$template[1]})\n";
    }

    echo "\n✓ Successfully added " . count($templates) . " card templates\n\n";

    // Show final result
    echo "Step 2: Verifying templates:\n";
    $stmt = $pdo->query("SELECT * FROM card_templates");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($templates as $template) {
        echo "  - ID: {$template['id']} | {$template['template_name']} | Package: {$template['package_type']}\n";
    }

    echo "\n===========================================\n";
    echo "✓✓✓ CARD TEMPLATES POPULATED!\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>