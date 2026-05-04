<?php
// public/fix_schema_column.php
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Attempting to add missing 'post_id' column to 'reports' table...<br>";

    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM reports LIKE 'post_id'");
    if ($stmt->fetch()) {
        echo "Column 'post_id' already exists.<br>";
    } else {
        $sql = "ALTER TABLE reports ADD COLUMN post_id INT DEFAULT NULL AFTER user_id";
        $db->exec($sql);
        echo "Successfully added 'post_id' column.<br>";
        
        // Add FK if possible
        try {
            $db->exec("ALTER TABLE reports ADD CONSTRAINT fk_reports_posts FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL");
            echo "Successfully added Foreign Key constraint.<br>";
        } catch (PDOException $fkErr) {
            echo "Could not add FK (might depend on posts table existence or existing keys): " . $fkErr->getMessage() . "<br>";
        }
    }

    echo "Schema repair finished.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
