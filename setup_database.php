<?php
// setup_database.php
define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Checking and creating tables...\n";

    // 1. Posts Table (Missing dependency for Reports)
    $sqlPosts = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content TEXT,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB;";
    $db->exec($sqlPosts);
    echo "Verified 'posts' table.\n";

    // 2. Reports Table
    $sqlReports = "CREATE TABLE IF NOT EXISTS reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        post_id INT DEFAULT NULL,
        report_type ENUM('payment_issue', 'theft', 'harassment', 'safety_concern', 'other') NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        status ENUM('pending', 'under_review', 'resolved', 'closed') DEFAULT 'pending',
        admin_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        resolved_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;";
    $db->exec($sqlReports);
    echo "Verified 'reports' table.\n";

    // 3. Support Messages Table
    $sqlMessages = "CREATE TABLE IF NOT EXISTS support_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        guest_name VARCHAR(100),
        guest_email VARCHAR(100),
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('open', 'read', 'replied', 'closed') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
    ) ENGINE=InnoDB;";
    $db->exec($sqlMessages);
    echo "Verified 'support_messages' table.\n";

    echo "Database setup completed successfully.\n";

} catch (Exception $e) {
    die("Setup Error: " . $e->getMessage() . "\n");
}
