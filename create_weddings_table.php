<?php
try {
    $db = new PDO("sqlite:wedding.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS weddings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        bride_name TEXT,
        groom_name TEXT,
        wedding_date TEXT,
        location TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");

    echo "!";
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage();
}
?>