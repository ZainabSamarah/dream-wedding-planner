<?php
require_once 'config.php';

try {
    // 1. Create packages table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS packages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        price DECIMAL(10,2) NOT NULL,
        description TEXT
    )");
    echo "Table 'packages' created or already exists.<br>";

    // 2. Insert default packages
    $stmt = $pdo->prepare("INSERT IGNORE INTO packages (name, price, description) VALUES (?, ?, ?)");
    $packages = [
        ['Regular Package', 5000.00, 'Essential coordination'],
        ['Medium Bouquet', 6500.00, 'Enhanced elegance'],
        ['Luxury Bouquet', 8000.00, 'Full luxury experience']
    ];

    foreach ($packages as $pkg) {
        $stmt->execute($pkg);
        echo "Inserted/Ignored: " . $pkg[0] . "<br>";
    }

    // 3. Create user_packages table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_packages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        package_id INT NOT NULL,
        full_name VARCHAR(255),
        email VARCHAR(255),
        wedding_date DATE,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (package_id) REFERENCES packages(id)
    )");
    echo "Table 'user_packages' created or already exists.<br>";

    // 4. Verify existing data
    $stmt = $pdo->query("SELECT * FROM packages");
    echo "<h3>Current Packages:</h3><pre>";
    print_r($stmt->fetchAll());
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>