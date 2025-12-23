<?php
/**
 * Setup Music Tables
 * Creates the database tables for music and entertainment selections
 */

require_once 'config.php';

echo "<h2>Setting up Music & Entertainment Tables</h2>";

try {
    // Create user_music_preferences table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_music_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_time VARCHAR(100) COMMENT 'When music will play: Ceremony, Cocktail Hour, Dinner, Reception/Dancing, All Day',
            vibe VARCHAR(100) COMMENT 'Wedding vibe: Romantic & Elegant, Fun & Energetic, etc.',
            duration INT DEFAULT 0 COMMENT 'Event duration in hours',
            special_requests TEXT COMMENT 'Special song requests or preferences',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created user_music_preferences table</p>";

    // Create user_music_selections table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_music_selections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_name VARCHAR(255) NOT NULL COMMENT 'Name of the genre or entertainment service',
            item_type VARCHAR(100) NOT NULL COMMENT 'Type: Music Genre or Entertainment',
            price_range VARCHAR(100) COMMENT 'Price range for entertainment services',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_item_type (item_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created user_music_selections table</p>";

    // Create entertainment_services table (master data)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS entertainment_services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price_min DECIMAL(10, 2) DEFAULT 0,
            price_max DECIMAL(10, 2) DEFAULT 0,
            category VARCHAR(100) DEFAULT 'Entertainment',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created entertainment_services table</p>";

    // Create music_genres table (master data)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS music_genres (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            icon VARCHAR(50) DEFAULT '🎵',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created music_genres table</p>";

    // Insert default entertainment services
    $entertainmentServices = [
        ['Live Band', 'Professional live band with vocalist and musicians. Perfect for ceremony and reception.', 800, 1500],
        ['DJ Service', 'Professional DJ with state-of-the-art sound system and lighting. Customizable music selection.', 600, 1200],
        ['Acoustic Duo', 'Intimate acoustic guitar and vocals duo. Ideal for ceremony and dinner.', 400, 800],
        ['Piano Player', 'Elegant piano performance for ceremony, cocktail hour, or dinner ambiance.', 300, 600],
        ['Live Vocalist', 'Professional singer with backing tracks. Can perform your favorite songs.', 350, 700],
        ['Entertainment Package', 'Complete package with DJ, live band, and special performances throughout the event.', 1500, 2500]
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO entertainment_services (name, description, price_min, price_max) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($entertainmentServices as $service) {
        $stmt->execute($service);
    }
    echo "<p style='color:green;'>✓ Inserted default entertainment services</p>";

    // Insert default music genres
    $musicGenres = [
        ['Classical', 'Elegant orchestral pieces and timeless classics for ceremony and cocktail hour', '🎼'],
        ['Jazz & Soul', 'Smooth jazz and soulful melodies for a sophisticated atmosphere', '🎹'],
        ['Acoustic', 'Intimate acoustic performances with guitar and live vocals', '🎸'],
        ['Pop & Hits', 'Modern pop hits and crowd favorites to keep the party alive', '🎤'],
        ['Bollywood & Desi', 'Vibrant Bollywood tracks and traditional desi music', '🎶'],
        ['DJ & Electronic', 'High-energy DJ sets and electronic beats for dancing', '🎧']
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO music_genres (name, description, icon) 
        VALUES (?, ?, ?)
    ");

    foreach ($musicGenres as $genre) {
        $stmt->execute($genre);
    }
    echo "<p style='color:green;'>✓ Inserted default music genres</p>";

    echo "<br><h3 style='color:green;'>✓ All music tables created successfully!</h3>";
    echo "<p><a href='music.php'>Go to Music & Entertainment Page</a></p>";
    echo "<p><a href='services.php'>Go to Services</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>