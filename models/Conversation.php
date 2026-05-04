<?php
// app/models/Conversation.php
require_once APP_ROOT . '/config/database.php';

class Conversation
{
    // returns conversation id between two users (creates if missing)
    public static function getOrCreateBetween($userA, $userB)
    {
        global $pdo;
        $u1 = min($userA, $userB);
        $u2 = max($userA, $userB);

        $stmt = $pdo->prepare("SELECT c.* FROM conversations c
            JOIN conversation_participants p1 ON p1.conversation_id = c.id AND p1.user_id = :u1
            JOIN conversation_participants p2 ON p2.conversation_id = c.id AND p2.user_id = :u2
            LIMIT 1");
        $stmt->execute(['u1' => $u1, 'u2' => $u2]);
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conv) {
            return (int)$conv['id'];
        }

        // create new conversation
        $pdo->beginTransaction();
        $now = date('Y-m-d H:i:s');
        $insert = $pdo->prepare("INSERT INTO conversations (created_at, updated_at) VALUES (:created, :updated)");
        $insert->execute(['created' => $now, 'updated' => $now]);
        $conversationId = (int)$pdo->lastInsertId();

        $p = $pdo->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:cid, :uid)");
        $p->execute(['cid' => $conversationId, 'uid' => $u1]);
        $p->execute(['cid' => $conversationId, 'uid' => $u2]);

        $pdo->commit();
        return $conversationId;
    }

    // returns list of conversations for a user with last message and unread count
    public static function getForUser($userId)
    {
        global $pdo;
        $sql = "
        SELECT c.id as conversation_id,
               (SELECT id FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_id,
               (SELECT content FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
               (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at,
               u.user_id as other_user_id,
               CONCAT(u.first_name, ' ', u.last_name) as other_user_name,
               u.profile_picture as other_user_avatar,
               (SELECT COUNT(*) 
                FROM messages msg 
                WHERE msg.conversation_id = c.id 
                  AND msg.is_read = 0 
                  AND msg.sender_id != :uid) as unread_count
        FROM conversations c
        JOIN conversation_participants cp ON cp.conversation_id = c.id
        JOIN conversation_participants cp2 ON cp2.conversation_id = c.id AND cp2.user_id != cp.user_id
        JOIN users u ON u.user_id = cp2.user_id
        WHERE cp.user_id = :uid
        ORDER BY COALESCE(
            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1),
            c.updated_at
        ) DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if user is participant in a conversation
    public static function isParticipant($conversationId, $userId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT 1 FROM conversation_participants WHERE conversation_id = :cid AND user_id = :uid LIMIT 1");
        $stmt->execute(['cid' => $conversationId, 'uid' => $userId]);
        return (bool)$stmt->fetchColumn();
    }
}
?>