<?php
// app/models/SupportMessage.php
require_once __DIR__ . '/../../config/database.php';

class SupportMessage
{
    // Create a new support message
    public static function create($userId, $subject, $message, $guestName = null, $guestEmail = null)
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "INSERT INTO support_messages (user_id, guest_name, guest_email, subject, message, status, created_at) 
                VALUES (:user_id, :guest_name, :guest_email, :subject, :message, 'open', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'subject' => $subject,
            'message' => $message
        ]);
        return $pdo->lastInsertId();
    }

    // Get messages for a user
    public static function getForUser($userId = null)
    {
        $pdo = Database::getInstance()->getConnection();
        if ($userId) {
            $sql = "SELECT * FROM support_messages WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
        } else {
            $sql = "SELECT * FROM support_messages ORDER BY created_at DESC";
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
