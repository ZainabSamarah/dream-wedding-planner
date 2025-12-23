<?php

function parseAndGenerateSQL($filePath, $packageType)
{
    if (!file_exists($filePath)) {
        return "";
    }

    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $sections = $xpath->query("//section[@class='menu-section']");
    $sqlValues = [];

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
                // Escape simple quotes for SQL
                $name = addslashes($name);
                $description = addslashes($description);
                $imgUrl = addslashes($imgUrl);
                $category = addslashes($category);
                $packageType = addslashes($packageType);

                $sqlValues[] = "('$packageType', '$name', '$description', '$imgUrl', '$category')";
            }
        }
    }
    return $sqlValues;
}

$sqlOutput = "-- SQL Dump for Food Service Tables\n";
$sqlOutput .= "-- Generated offline from HTML files\n\n";

// Table: food_menu
$sqlOutput .= "DROP TABLE IF EXISTS `food_menu`;\n";
$sqlOutput .= "CREATE TABLE `food_menu` (\n";
$sqlOutput .= "  `id` int(11) NOT NULL AUTO_INCREMENT,\n";
$sqlOutput .= "  `package_type` enum('luxury','regular','medium') NOT NULL,\n";
$sqlOutput .= "  `name` varchar(255) NOT NULL,\n";
$sqlOutput .= "  `description` text DEFAULT NULL,\n";
$sqlOutput .= "  `image_url` varchar(255) DEFAULT NULL,\n";
$sqlOutput .= "  `category` varchar(100) DEFAULT 'Main Dishes',\n";
$sqlOutput .= "  PRIMARY KEY (`id`)\n";
$sqlOutput .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Data: food_menu
$valuesLux = parseAndGenerateSQL('servicesListLux.html', 'luxury');
$valuesMed = parseAndGenerateSQL('servicesListMed.html', 'medium');
$valuesReg = parseAndGenerateSQL('servicesListReg.html', 'regular');

$allValues = array_merge($valuesLux, $valuesMed, $valuesReg);

if (!empty($allValues)) {
    $sqlOutput .= "INSERT INTO `food_menu` (`package_type`, `name`, `description`, `image_url`, `category`) VALUES \n";
    $sqlOutput .= implode(",\n", $allValues) . ";\n\n";
}

// Table: user_food_selections
$sqlOutput .= "DROP TABLE IF EXISTS `user_food_selections`;\n";
$sqlOutput .= "CREATE TABLE `user_food_selections` (\n";
$sqlOutput .= "  `id` int(11) NOT NULL AUTO_INCREMENT,\n";
$sqlOutput .= "  `user_id` int(11) NOT NULL,\n";
$sqlOutput .= "  `food_menu_id` int(11) NOT NULL,\n";
$sqlOutput .= "  `selected_at` timestamp NOT NULL DEFAULT current_timestamp(),\n";
$sqlOutput .= "  PRIMARY KEY (`id`),\n";
$sqlOutput .= "  KEY `user_id` (`user_id`),\n";
$sqlOutput .= "  KEY `food_menu_id` (`food_menu_id`),\n";
$sqlOutput .= "  CONSTRAINT `fk_user_selection` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,\n";
$sqlOutput .= "  CONSTRAINT `fk_food_menu` FOREIGN KEY (`food_menu_id`) REFERENCES `food_menu` (`id`) ON DELETE CASCADE\n";
$sqlOutput .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n";

file_put_contents('food_service_dump.sql', $sqlOutput);
echo "SQL dump created successfully at food_service_dump.sql";

?>