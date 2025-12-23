<?php
/**
 * Complete Database Setup Script
 * Creates database and all necessary tables for the wedding planner application
 */

// Database configuration
$host = 'localhost';
$dbname = 'wedding_db_local';
$username = 'root';
$password = '';

echo "===========================================\n";
echo "Wedding Planner Database Setup\n";
echo "===========================================\n\n";

try {
    // First, connect without specifying database to create it
    echo "Step 1: Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to MySQL server\n\n";

    // Create database if it doesn't exist
    echo "Step 2: Creating database '$dbname'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbname' created/verified\n\n";

    // Now connect to the specific database
    echo "Step 3: Connecting to database '$dbname'...\n";
    $pdo->exec("USE `$dbname`");
    echo "✓ Connected to database '$dbname'\n\n";

    // 1. Create packages table
    echo "Step 4: Creating 'packages' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'packages' created\n\n";

    // Insert default packages (reg, med, lux)
    echo "Step 5: Populating 'packages' table with default values...\n";
    $packages = ['reg', 'med', 'lux'];
    $insertedCount = 0;
    foreach ($packages as $package) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO packages (name) VALUES (?)");
        $stmt->execute([$package]);
        if ($stmt->rowCount() > 0) {
            $insertedCount++;
            echo "  → Inserted package: $package\n";
        }
    }
    if ($insertedCount === 0) {
        echo "  → All packages already exist\n";
    }
    echo "✓ Packages table populated (reg, med, lux)\n\n";

    // 2. Create services table
    echo "Step 6: Creating 'services' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) DEFAULT 0.00,
            category ENUM('food', 'decoration', 'venue', 'photography', 'entertainment', 'other') DEFAULT 'other',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'services' created\n\n";

    // 3. Create user_packages table
    echo "Step 7: Creating 'user_packages' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            package_id INT NOT NULL,
            full_name VARCHAR(200),
            email VARCHAR(150),
            wedding_date DATE,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_package_id (package_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'user_packages' created\n\n";

    // 4. Create ceremonies table
    echo "Step 8: Creating 'ceremonies' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ceremonies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ceremony_date DATE,
            location VARCHAR(255),
            layout TEXT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'ceremonies' created\n\n";

    // 5. Create ceremony_photos table
    echo "Step 9: Creating 'ceremony_photos' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ceremony_photos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ceremony_id INT NOT NULL,
            photo_url VARCHAR(255),
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ceremony_id) REFERENCES ceremonies(id) ON DELETE CASCADE,
            INDEX idx_ceremony_id (ceremony_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'ceremony_photos' created\n\n";

    // 6. Create gallery table
    echo "Step 10: Creating 'gallery' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            photo_url VARCHAR(255),
            description TEXT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'gallery' created\n\n";

    // 7. Create guests table
    echo "Step 11: Creating 'guests' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS guests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255),
            email VARCHAR(255),
            phone VARCHAR(50),
            status ENUM('pending', 'attending', 'not_attending') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'guests' created\n\n";

    // 8. Create invitations table
    echo "Step 12: Creating 'invitations' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS invitations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            design TEXT,
            content TEXT,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'invitations' created\n\n";

    // 9. Create messages table
    echo "Step 13: Creating 'messages' table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_user_id INT NOT NULL,
            to_user_id INT NOT NULL,
            content TEXT,
            is_read TINYINT(1) DEFAULT 0,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_from_user (from_user_id),
            INDEX idx_to_user (to_user_id),
            INDEX idx_is_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'messages' created\n\n";

    echo "===========================================\n";
    echo "✓✓✓ DATABASE SETUP COMPLETED SUCCESSFULLY!\n";
    echo "===========================================\n\n";

    // Display summary
    echo "SUMMARY:\n";
    echo "--------\n";
    echo "Database: $dbname\n";
    echo "Tables created: 9\n";
    echo "  1. packages (3 default entries: reg, med, lux)\n";
    echo "  2. services\n";
    echo "  3. user_packages\n";
    echo "  4. ceremonies\n";
    echo "  5. ceremony_photos\n";
    echo "  6. gallery\n";
    echo "  7. guests\n";
    echo "  8. invitations\n";
    echo "  9. messages\n\n";

    echo "You can now use these tables in your application!\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nPlease ensure:\n";
    echo "1. MySQL is running in XAMPP\n";
    echo "2. The credentials are correct (default: root with no password)\n";
    echo "3. The 'users' table exists (required for foreign keys)\n";
    exit(1);
}
?>