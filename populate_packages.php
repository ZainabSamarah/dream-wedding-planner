<?php
/**
 * Populate Packages Table
 * Adds default packages (reg, med, lux) to existing wedding_db database
 */

$host = 'localhost';
$dbname = 'wedding_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Populating packages table...\n\n";

    $packages = ['reg', 'med', 'lux'];
    $insertedCount = 0;

    foreach ($packages as $package) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO packages (name) VALUES (?)");
        $stmt->execute([$package]);
        if ($stmt->rowCount() > 0) {
            $insertedCount++;
            echo "✓ Inserted package: $package\n";
        } else {
            echo "  → Package '$package' already exists\n";
        }
    }

    echo "\n";
    if ($insertedCount > 0) {
        echo "✓ Successfully added $insertedCount package(s)!\n";
    } else {
        echo "✓ All packages already exist!\n";
    }

    // Display current packages
    echo "\nCurrent packages in database:\n";
    $stmt = $pdo->query("SELECT * FROM packages");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($packages as $pkg) {
        echo "  - ID: {$pkg['id']}, Name: {$pkg['name']}\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>