<?php
/**
 * Database Setup Script
 * Creates all necessary tables for the wedding planner application
 */

// Database configuration
$host = 'localhost';
$dbname = 'wedding_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully!\n\n";

    // 1. Create packages table
    echo "Creating packages table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Packages table created\n\n";

    // Insert default packages (reg, med, lux)
    echo "Inserting default packages...\n";
    $packages = ['reg', 'med', 'lux'];
    foreach ($packages as $package) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO packages (name) VALUES (?)");
        $stmt->execute([$package]);
    }
    echo "✓ Default packages inserted (reg, med, lux)\n\n";

    // 2. Create services table
    echo "Creating services table...\n";
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
    echo "✓ Services table created\n\n";

    // 3. Create user_packages table
    echo "Creating user_packages table...\n";
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
    echo "✓ User_packages table created\n\n";

    // 4. Create ceremonies table
    echo "Creating ceremonies table...\n";
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
    echo "✓ Ceremonies table created\n\n";

    // 5. Create ceremony_photos table
    echo "Creating ceremony_photos table...\n";
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
    echo "✓ Ceremony_photos table created\n\n";

    // 6. Create gallery table
    echo "Creating gallery table...\n";
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
    echo "✓ Gallery table created\n\n";

    // 7. Create guests table
    echo "Creating guests table...\n";
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
    echo "✓ Guests table created\n\n";

    // 8. Create invitations table
    echo "Creating invitations table...\n";
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
    echo "✓ Invitations table created\n\n";

    // 9. Create messages table
    echo "Creating messages table...\n";
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
    echo "✓ Messages table created\n\n";

    // 10. Create photography_sessions table
    echo "Creating photography_sessions table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS photography_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_date DATE,
            session_time TIME,
            location VARCHAR(255),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Photography_sessions table created\n\n";

    echo "========================================\n";
    echo "✓ All tables created successfully!\n";
    echo "========================================\n\n";

    // Display table summary
    echo "Tables created:\n";
    echo "1. packages (id, name)\n";
    echo "2. services (id, name, description, price, category)\n";
    echo "3. user_packages (id, user_id, package_id, full_name, email, wedding_date, notes)\n";
    echo "4. ceremonies (id, user_id, ceremony_date, location, layout, description)\n";
    echo "5. ceremony_photos (id, ceremony_id, photo_url)\n";
    echo "6. gallery (id, user_id, photo_url, description)\n";
    echo "7. guests (id, user_id, name, email, phone, status)\n";
    echo "8. invitations (id, user_id, design, content, image_url)\n";
    echo "9. messages (id, from_user_id, to_user_id, content, is_read)\n\n";

    echo "Default packages: reg, med, lux\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>