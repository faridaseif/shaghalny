<?php

session_start();
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$code = $_GET['code'] ?? '';

$stmt = $db->prepare("
    UPDATE users 
    SET email_verified = 1, 
        email_verified_at = NOW(),
        email_verification_code = NULL
    WHERE email_verification_code = ?
");
$stmt->execute([$code]);

if ($stmt->rowCount()) {
    echo "Email verified successfully!";
} else {
    echo "Invalid or expired verification code.";
}
?>