<?php
require_once 'config.php';

// 1. Add package column to users table
try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'package'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN package ENUM('regular', 'medium', 'luxury') DEFAULT 'regular'");
        echo "Added 'package' column to users table.<br>";
    } else {
        echo "'package' column already exists.<br>";
    }
} catch (PDOException $e) {
    echo "Error adding column: " . $e->getMessage() . "<br>";
}

// 2. Create menu_items table
try {
    $sql = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255),
        category ENUM('Main Dishes', 'Appetizers', 'Desserts') NOT NULL,
        package_level ENUM('regular', 'medium', 'luxury') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Created 'menu_items' table.<br>";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "<br>";
}

// 3. Parse and Insert Data
function parseAndInsert($filepath, $packageLevel, $pdo)
{
    if (!file_exists($filepath)) {
        echo "File not found: $filepath<br>";
        return;
    }

    $html = file_get_contents($filepath);

    // Suppress warnings for malformed HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Find sections
    $sections = $xpath->query("//section[contains(@class, 'menu-section')]");

    $count = 0;
    foreach ($sections as $section) {
        // Find Category Name (h2)
        $h2 = $xpath->query(".//h2", $section)->item(0);
        $category = $h2 ? trim($h2->textContent) : 'Main Dishes';

        // Normalize Category
        if (stripos($category, 'Main') !== false)
            $category = 'Main Dishes';
        elseif (stripos($category, 'Appetizer') !== false)
            $category = 'Appetizers';
        elseif (stripos($category, 'Dessert') !== false)
            $category = 'Desserts'; // If any

        // Find Cards
        $cards = $xpath->query(".//div[contains(@class, 'dish-card')]", $section);

        foreach ($cards as $card) {
            $img = $xpath->query(".//img", $card)->item(0);
            $imgSrc = $img ? $img->getAttribute('src') : '';

            $h3 = $xpath->query(".//h3", $card)->item(0);
            $name = $h3 ? trim($h3->textContent) : '';

            $p = $xpath->query(".//p", $card)->item(0);
            $desc = $p ? trim($p->textContent) : '';

            if ($name) {
                // Check duplication
                $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE name = ? AND package_level = ?");
                $stmt->execute([$name, $packageLevel]);
                if (!$stmt->fetch()) {
                    $insert = $pdo->prepare("INSERT INTO menu_items (name, description, image_path, category, package_level) VALUES (?, ?, ?, ?, ?)");
                    $insert->execute([$name, $desc, $imgSrc, $category, $packageLevel]);
                    $count++;
                }
            }
        }
    }
    echo "Inserted $count items for $packageLevel from " . basename($filepath) . "<br>";
}

// Run parser for each file
echo "<h3>Migrating Data...</h3>";
parseAndInsert('servicesListReg.html', 'regular', $pdo);
parseAndInsert('servicesListMed.html', 'medium', $pdo);
parseAndInsert('servicesListLux.html', 'luxury', $pdo);

echo "<h3>Migration Complete.</h3>";
?>