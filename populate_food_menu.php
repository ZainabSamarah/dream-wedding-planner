<?php
require_once 'config.php';

function parseAndInsert($pdo, $filePath, $packageType)
{
    echo "Processing $filePath for $packageType...\n";
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return;
    }

    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $sections = $xpath->query("//section[@class='menu-section']");

    foreach ($sections as $section) {
        $categoryNode = $xpath->query(".//h2", $section)->item(0);
        $category = $categoryNode ? trim($categoryNode->nodeValue) : 'Main Dishes';

        $dishCards = $xpath->query(".//div[@class='dish-card']", $section);

        foreach ($dishCards as $card) {
            $imgNode = $xpath->query(".//img", $card)->item(0);
            $imgUrl = $imgNode ? $imgNode->getAttribute('src') : '';

            $titleNode = $xpath->query(".//h3", $card)->item(0);
            $name = $titleNode ? trim($titleNode->nodeValue) : '';

            $descNode = $xpath->query(".//p", $card)->item(0);
            $description = $descNode ? trim($descNode->nodeValue) : '';

            if ($name) {
                // Check if exists
                $stmt = $pdo->prepare("SELECT id FROM food_menu WHERE name = ? AND package_type = ?");
                $stmt->execute([$name, $packageType]);

                if ($stmt->fetch()) {
                    // Update if exists (optional, but good for idempotency)
                    $stmt = $pdo->prepare("UPDATE food_menu SET description = ?, image_url = ?, category = ? WHERE name = ? AND package_type = ?");
                    $stmt->execute([$description, $imgUrl, $category, $name, $packageType]);
                    // echo "Updated: $name\n";
                } else {
                    // Insert
                    $stmt = $pdo->prepare("INSERT INTO food_menu (package_type, name, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$packageType, $name, $description, $imgUrl, $category]);
                    echo "Inserted: $name ($category)\n";
                }
            }
        }
    }
}

try {
    // Clear existing data to avoid duplicates if re-run (optional, or handle upsert)
    // $pdo->exec("TRUNCATE TABLE food_menu"); // Be careful with this! Better to use upsert logic above.

    parseAndInsert($pdo, 'servicesListLux.html', 'luxury');
    parseAndInsert($pdo, 'servicesListMed.html', 'medium');
    parseAndInsert($pdo, 'servicesListReg.html', 'regular');

    echo "Data population complete!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>