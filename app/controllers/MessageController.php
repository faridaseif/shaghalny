<?php
// app/controllers/MessageController.php
// Improved send handler: accepts JSON or form data, validates participation, returns JSON.

require_once APP_ROOT . '/app/models/Conversation.php';
require_once APP_ROOT . '/app/models/Message.php';
require_once APP_ROOT . '/config/database.php';

class MessageController
{
    // Renders the inbox view (existing implementation)
    public function inbox()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $userId = (int)$_SESSION['user_id'];
        $currentUserId = $userId;
        
        // Initialize variables with defaults
        $conversations = [];
        $messages = [];
        $selectedConversationId = null;
        
        try {
            $conversations = Conversation::getForUser($userId);
            if (!is_array($conversations)) {
                $conversations = [];
            }
        } catch (Exception $e) {
            error_log("Error loading conversations: " . $e->getMessage());
            $conversations = [];
        }

        $selectedConversationId = isset($_GET['cid']) ? (int)$_GET['cid'] : (isset($conversations[0]) ? (int)$conversations[0]['conversation_id'] : null);
        
        if ($selectedConversationId) {
            try {
                $messages = Message::fetchByConversation($selectedConversationId);
                if (!is_array($messages)) {
                    $messages = [];
                }
                Message::markAsRead($selectedConversationId, $userId);
            } catch (Exception $e) {
                error_log("Error loading messages: " . $e->getMessage());
                $messages = [];
            }
        }

        require_once APP_ROOT . '/app/views/message/inbox.php';
    }

    // Start a conversation with a specific user (or open existing)
    public function startConversation()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $userId = (int)$_SESSION['user_id'];
        $recipientId = isset($_GET['recipient_id']) ? (int)$_GET['recipient_id'] : null;
        
        if (!$recipientId || $recipientId === $userId) {
             header('Location: index.php?controller=Message&action=inbox');
             exit;
        }
        
        try {
            $conversationId = Conversation::getOrCreateBetween($userId, $recipientId);
            header('Location: index.php?controller=Message&action=inbox&cid=' . $conversationId);
            exit;
        } catch (Exception $e) {
            error_log("Error starting conversation: " . $e->getMessage());
            header('Location: index.php?controller=Message&action=inbox');
            exit;
        }
    }

    // AJAX send message
    public function send()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Accept both JSON body or form-encoded POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $input = [];
        if (stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            // Support form POST (e.g., Content-Type: application/x-www-form-urlencoded or multipart/form-data)
            $input = $_POST;
        }

        $userId = (int)$_SESSION['user_id'];
        $conversationId = isset($input['conversation_id']) ? (int)$input['conversation_id'] : null;
        $recipientId = isset($input['recipient_id']) ? (int)$input['recipient_id'] : null;
        $content = trim($input['content'] ?? '');

        if (empty($content) || (empty($conversationId) && empty($recipientId))) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            exit;
        }

        // If conversationId provided, validate that user is a participant
        if ($conversationId) {
            if (!Conversation::isParticipant($conversationId, $userId)) {
                http_response_code(403);
                echo json_encode(['error' => 'You are not a participant in this conversation']);
                exit;
            }
        } else {
            // No conversation given -> create (or get) by recipient
            if (!$recipientId) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing recipient']);
                exit;
            }
            // Prevent messaging self
            if ($recipientId === $userId) {
                http_response_code(400);
                echo json_encode(['error' => 'Cannot message yourself']);
                exit;
            }
            $conversationId = Conversation::getOrCreateBetween($userId, $recipientId);
        }

        // Create message
        $messageId = Message::create($conversationId, $userId, $content);

        // Fetch the newly created message(s) to return to client
        $messages = Message::fetchByConversation($conversationId, $messageId - 1);

        echo json_encode(['success' => true, 'message' => $messages]);
    }

    // AJAX fetch messages for a conversation (optionally after a message id)
    public function fetch()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $userId = (int)$_SESSION['user_id'];
        $conversationId = isset($_GET['cid']) ? (int)$_GET['cid'] : null;
        $after = isset($_GET['after']) ? (int)$_GET['after'] : 0;
        if (!$conversationId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing conversation id']);
            exit;
        }

        // Validate participation
        if (!Conversation::isParticipant($conversationId, $userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        $messages = Message::fetchByConversation($conversationId, $after);
        Message::markAsRead($conversationId, $userId);

        echo json_encode(['messages' => $messages]);
    }

    // AJAX fetch updated conversations list
    public function conversations()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $userId = (int)$_SESSION['user_id'];
        $conversations = Conversation::getForUser($userId);
        echo json_encode(['conversations' => $conversations]);
    }
}
?>