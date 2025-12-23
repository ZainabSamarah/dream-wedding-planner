<?php
/**
 * Setup RSVP Tables
 * Creates the database tables for RSVP and guest management
 */

require_once 'config.php';

echo "<h2>Setting up RSVP Tables</h2>";

try {
    // Create wedding_events table (users can have multiple weddings/events)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS wedding_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_name VARCHAR(255) NOT NULL DEFAULT 'My Wedding',
            event_date DATE,
            event_location TEXT,
            rsvp_code VARCHAR(32) NOT NULL UNIQUE,
            rsvp_deadline DATE,
            max_guests INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_rsvp_code (rsvp_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created wedding_events table</p>";

    // Create rsvp_guests table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rsvp_guests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_id INT,
            unique_code VARCHAR(32) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(50),
            status ENUM('pending', 'attending', 'not-attending') DEFAULT 'pending',
            party_size INT DEFAULT 1,
            dietary_restrictions TEXT,
            notes TEXT,
            responded_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES wedding_events(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_unique_code (unique_code),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created rsvp_guests table</p>";

    // Create guest_messages table (for guest comments/messages)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rsvp_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guest_id INT NOT NULL,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (guest_id) REFERENCES rsvp_guests(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_guest_id (guest_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✓ Created rsvp_messages table</p>";

    echo "<br><h3 style='color:green;'>✓ All RSVP tables created successfully!</h3>";
    echo "<p><a href='rsvp.php'>Go to RSVP Manager</a></p>";
    echo "<p><a href='services.php'>Go to Services</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>