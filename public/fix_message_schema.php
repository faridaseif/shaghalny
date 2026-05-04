<?php
// public/fix_message_schema.php
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Attempting to add missing columns to 'support_messages' table...<br>";

    // Check if guest_name exists
    $stmt = $db->query("SHOW COLUMNS FROM support_messages LIKE 'guest_name'");
    if ($stmt->fetch()) {
        echo "Column 'guest_name' already exists.<br>";
    } else {
        $sql = "ALTER TABLE support_messages ADD COLUMN guest_name VARCHAR(100) DEFAULT NULL AFTER user_id";
        $db->exec($sql);
        echo "Successfully added 'guest_name' column.<br>";
    }

    // Check if guest_email exists
    $stmt = $db->query("SHOW COLUMNS FROM support_messages LIKE 'guest_email'");
    if ($stmt->fetch()) {
        echo "Column 'guest_email' already exists.<br>";
    } else {
        $sql = "ALTER TABLE support_messages ADD COLUMN guest_email VARCHAR(100) DEFAULT NULL AFTER guest_name";
        $db->exec($sql);
        echo "Successfully added 'guest_email' column.<br>";
    }

    echo "Schema repair finished.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
