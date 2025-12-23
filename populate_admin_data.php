<?php
require_once "config.php";

$settings = [
    ["about_wede", "WEDE is your ultimate partner in creating the wedding of your dreams. We handle everything from planning to execution with elegance."],
    ["privacy_policy", "Your privacy is important to us. We only collect data necessary for your wedding planning process."],
    ["terms_of_service", "By using WEDE, you agree to our terms of providing premium wedding planning services."],
    ["contact_info", "Email: contact@wede.com | Phone: +1 234 567 890"],
    ["company_desc", "WEDE Luxury Wedding Planners - crafting unforgettable moments since 2020."]
];

$tips = [
    ["Choose Your Theme Early", "Setting a theme early helps in cohesive decision making for decor, attire, and venue."],
    ["Budget Buffer", "Always add a 10-15% buffer to your wedding budget for unexpected costs."],
    ["Guest List Strategy", "Start with your must-haves and expand from there to avoid overcrowding."]
];

$vendors = [
    ["Gourmet Delights", "Food", "contact@gourmet.com", "Premium catering service with international cuisines."],
    ["Floral Dreams", "Decoration", "info@floraldreams.com", "Bespoke floral arrangements for luxury weddings."],
    ["Shot with Love", "Photography", "hello@shotwithlove.com", "Candid wedding photography and cinematography."]
];

try {
    // Populate settings
    $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $s) { $stmt->execute($s); }

    // Populate tips
    $stmt = $pdo->prepare("INSERT IGNORE INTO planning_tips (title, content) VALUES (?, ?)");
    foreach ($tips as $t) { $stmt->execute($t); }

    // Populate vendors
    $stmt = $pdo->prepare("INSERT IGNORE INTO vendors (name, category, contact_info, description) VALUES (?, ?, ?, ?)");
    foreach ($vendors as $v) { $stmt->execute($v); }

    echo "Sample data populated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
