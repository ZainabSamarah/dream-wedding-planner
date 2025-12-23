<?php
require_once 'config.php';

// 1. Create Tables
$queries = [
    "CREATE TABLE IF NOT EXISTS `cakes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `package_type` enum('luxury','regular','medium') NOT NULL,
        `name` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `image_url` varchar(255) DEFAULT NULL,
        `category` varchar(50) DEFAULT 'Cakes',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `user_cake_selections` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `cake_id` int(11) NOT NULL,
        `selected_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `cake_id` (`cake_id`),
        CONSTRAINT `fk_user_cake` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_cake` FOREIGN KEY (`cake_id`) REFERENCES `cakes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `invitation_cards` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `package_type` enum('luxury','regular','medium') NOT NULL,
        `name` varchar(255) NOT NULL,
        `image_url` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `user_card_customizations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `card_template_id` int(11) NOT NULL,
        `bride_name` varchar(255) NOT NULL,
        `groom_name` varchar(255) NOT NULL,
        `wedding_date` date DEFAULT NULL,
        `location` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `card_template_id` (`card_template_id`),
        CONSTRAINT `fk_user_card` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_card_template` FOREIGN KEY (`card_template_id`) REFERENCES `invitation_cards` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "Table created/verified.\n";
    } catch (PDOException $e) {
        echo "Error creating table: " . $e->getMessage() . "\n";
    }
}

// 2. Populate Cakes and Drinks
function parseAndInsertCakes($pdo, $filePath, $packageType)
{
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
        $header = $xpath->query(".//h2", $section)->item(0);
        $category = 'Cakes';
        if ($header && stripos($header->nodeValue, 'Drink') !== false) {
            $category = 'Drinks';
        }

        $cards = $xpath->query(".//div[@class='dish-card']", $section);
        foreach ($cards as $card) {
            $nameNode = $xpath->query(".//h3", $card)->item(0);
            $descNode = $xpath->query(".//p", $card)->item(0);
            $imgNode = $xpath->query(".//img", $card)->item(0);

            $name = $nameNode ? trim($nameNode->nodeValue) : '';
            $description = $descNode ? trim($descNode->nodeValue) : '';
            $imgUrl = $imgNode ? $imgNode->getAttribute('src') : '';

            if ($name) {
                // Check if exists
                $stmt = $pdo->prepare("SELECT id FROM cakes WHERE name = ? AND package_type = ?");
                $stmt->execute([$name, $packageType]);
                if (!$stmt->fetch()) {
                    $insertParams = [$packageType, $name, $description, $imgUrl, $category];
                    $stmtIns = $pdo->prepare("INSERT INTO cakes (package_type, name, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
                    $stmtIns->execute($insertParams);
                    echo "Inserted $category: $name ($packageType)\n";
                }
            }
        }
    }
}

parseAndInsertCakes($pdo, 'regCake.html', 'regular');
parseAndInsertCakes($pdo, 'medCake.html', 'medium');
parseAndInsertCakes($pdo, 'LuxCake.php', 'luxury'); // LuxCake.php is the source for luxury

// 3. Populate Invitation Cards
function parseAndInsertCards($pdo, $filePath, $packageType)
{
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return;
    }

    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $cards = $xpath->query("//div[contains(@class, 'card-option')]");

    foreach ($cards as $card) {
        $nameNode = $xpath->query(".//h3", $card)->item(0);
        $imgNode = $xpath->query(".//img", $card)->item(0);

        $name = $nameNode ? trim($nameNode->nodeValue) : 'Unknown Card';
        $imgUrl = $imgNode ? $imgNode->getAttribute('src') : '';

        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM invitation_cards WHERE name = ? AND package_type = ?");
        $stmt->execute([$name, $packageType]);
        if (!$stmt->fetch()) {
            $stmtIns = $pdo->prepare("INSERT INTO invitation_cards (package_type, name, image_url) VALUES (?, ?, ?)");
            $stmtIns->execute([$packageType, $name, $imgUrl]);
            echo "Inserted Card: $name ($packageType)\n";
        }
    }
}

parseAndInsertCards($pdo, 'invCardLux.html', 'luxury');
// Assuming invCardMed.html and invCardReg.html exist, if not I will handle or verify existence.
// Based on user request "invCardLux,med,reg", they imply they exist or should be treated similar.
// I'll check existence before parsing.
if (file_exists('invCardMed.html'))
    parseAndInsertCards($pdo, 'invCardMed.html', 'medium');
if (file_exists('invCardReg.html'))
    parseAndInsertCards($pdo, 'invCardReg.html', 'regular');

echo "Database setup and population complete.";
?>