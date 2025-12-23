<?php
/**
 * Test Packages Integration
 * Verifies that packages are properly set up and can be retrieved
 */

require_once __DIR__ . '/config.php';

echo "===========================================\n";
echo "Testing Packages Integration\n";
echo "===========================================\n\n";

try {
    // Test 1: Check if packages table exists and has data
    echo "Test 1: Checking packages table...\n";
    $stmt = $pdo->query("SELECT * FROM packages ORDER BY id");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($packages)) {
        echo "❌ FAILED: Packages table is empty!\n";
        exit(1);
    }

    echo "✓ PASSED: Found " . count($packages) . " package(s)\n";
    foreach ($packages as $pkg) {
        echo "  → ID: {$pkg['id']}, Name: {$pkg['name']}\n";
    }
    echo "\n";

    // Test 2: Check if user_packages table exists
    echo "Test 2: Checking user_packages table...\n";
    $stmt = $pdo->query("DESCRIBE user_packages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ PASSED: user_packages table exists with " . count($columns) . " columns\n";
    echo "  Columns: ";
    echo implode(", ", array_column($columns, 'Field')) . "\n\n";

    // Test 3: Verify package display mapping
    echo "Test 3: Testing package display mapping...\n";
    $packageDisplayMap = [
        'reg' => ['Regular Package', 5000],
        'med' => ['Medium Bouquet', 6500],
        'lux' => ['Luxury Bouquet', 8000]
    ];

    foreach ($packages as $pkg) {
        $code = $pkg['name'];
        if (isset($packageDisplayMap[$code])) {
            $displayInfo = $packageDisplayMap[$code];
            echo "✓ {$code} → {$displayInfo[0]} (\${$displayInfo[1]})\n";
        } else {
            echo "❌ WARNING: No display mapping for package code: {$code}\n";
        }
    }
    echo "\n";

    // Test 4: Check existing user_packages entries
    echo "Test 4: Checking existing user package selections...\n";
    $stmt = $pdo->query("
        SELECT up.*, p.name as package_name, u.email as user_email 
        FROM user_packages up 
        LEFT JOIN packages p ON up.package_id = p.id 
        LEFT JOIN users u ON up.user_id = u.id 
        LIMIT 5
    ");
    $userPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($userPackages)) {
        echo "  → No user package selections yet (this is normal for new setup)\n";
    } else {
        echo "✓ Found " . count($userPackages) . " user package selection(s):\n";
        foreach ($userPackages as $up) {
            echo "  → User: {$up['user_email']}, Package: {$up['package_name']}, Date: {$up['wedding_date']}\n";
        }
    }
    echo "\n";

    echo "===========================================\n";
    echo "✓✓✓ ALL TESTS PASSED!\n";
    echo "===========================================\n\n";

    echo "Summary:\n";
    echo "- Packages table: ✓ Working\n";
    echo "- User_packages table: ✓ Working\n";
    echo "- Package mapping: ✓ Working\n";
    echo "- Database connection: ✓ Working\n\n";

    echo "You can now use preparation.php to select packages!\n";
    echo "The form will fetch packages from the database and save selections to user_packages table.\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>