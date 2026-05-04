<?php
// app/models/Message.php
require_once APP_ROOT . '/config/database.php';

class Message
{
    public $id;
    public $conversation_id;
    public $sender_id;
    public $content;
    public $created_at;
    public $is_read;

    public static function create($conversationId, $senderId, $content)
    {
        global $pdo;
        $sql = "INSERT INTO messages (conversation_id, sender_id, content, created_at, is_read)
                VALUES (:cid, :sid, :content, :created, 0)";
        $stmt = $pdo->prepare($sql);
        $now = date('Y-m-d H:i:s');
        $stmt->execute([
            'cid' => $conversationId,
            'sid' => $senderId,
            'content' => $content,
            'created' => $now
        ]);

        // update conversation updated_at
        $upd = $pdo->prepare("UPDATE conversations SET updated_at = :now WHERE id = :cid");
        $upd->execute(['now' => $now, 'cid' => $conversationId]);

        return $pdo->lastInsertId();
    }

    public static function fetchByConversation($conversationId, $afterId = 0)
    {
        global $pdo;
        if ($afterId > 0) {
            $stmt = $pdo->prepare("SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.profile_picture as sender_avatar
                                   FROM messages m
                                   JOIN users u ON u.user_id = m.sender_id
                                   WHERE m.conversation_id = :cid AND m.id > :after
                                   ORDER BY m.created_at ASC");
            $stmt->execute(['cid' => $conversationId, 'after' => $afterId]);
        } else {
            $stmt = $pdo->prepare("SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) as sender_name, u.profile_picture as sender_avatar
                                   FROM messages m
                                   JOIN users u ON u.user_id = m.sender_id
                                   WHERE m.conversation_id = :cid
                                   ORDER BY m.created_at ASC");
            $stmt->execute(['cid' => $conversationId]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead($conversationId, $userId)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1
                               WHERE conversation_id = :cid AND sender_id != :uid AND is_read = 0");
        $stmt->execute(['cid' => $conversationId, 'uid' => $userId]);
        return $stmt->rowCount();
    }
}
?>