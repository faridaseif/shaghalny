<?php
if (!defined('ASSET_ROOT')) {
    define('ASSET_ROOT', '../../public');
}
if (!isset($conversations)) $conversations = [];
if (!isset($messages)) $messages = [];
if (!isset($selectedConversationId)) $selectedConversationId = null;
$currentUserId = $_SESSION['user_id'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Messages - Shaghalny</title>
    <link rel="stylesheet" href="assets/css/inbox.css">
    <link rel="stylesheet" href="assets/css/Header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- React Dependencies -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

</head>
<body>

<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>
<div class="messages-wrapper">
    <div class="conversations" id="conversations">
        <div class="header">Messages</div>
        <?php if (empty($conversations) || !is_array($conversations)): ?>
            <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                No conversations yet. Start a conversation to begin messaging.
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $c): ?>
            <div class="conv-item" data-cid="<?=htmlspecialchars($c['conversation_id'])?>">
                <div class="avatar">
                    <?= isset($c['other_user_name'][0]) ? htmlspecialchars(strtoupper($c['other_user_name'][0])) : 'U' ?>
                </div>
                <div class="meta">
                    <div style="display:flex;justify-content:space-between">
                        <div class="name"><?=htmlspecialchars($c['other_user_name'])?></div>
                        <div class="time">
                            <?= $c['last_message_at'] ? date('g:ia', strtotime($c['last_message_at'])) : '' ?>
                        </div>
                    </div>
                    <div class="preview">
                        <?= htmlspecialchars(mb_strimwidth($c['last_message'] ?? '', 0, 50, '...')) ?>
                    </div>
                </div>
                <?php if ($c['unread_count'] > 0): ?>
                    <div class="unread"><?= (int)$c['unread_count'] ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="chat" id="chat">
        <?php if (empty($selectedConversationId)): ?>
            <div style="padding:24px;color:var(--text-secondary)">Select a conversation to start chatting.</div>
        <?php else: ?>
            <div class="header" id="chat-header">
                <?php
                    $otherName = 'User';
                    if (!empty($conversations) && is_array($conversations)) {
                        foreach ($conversations as $c) {
                            if ($c['conversation_id'] == $selectedConversationId) {
                                $otherName = $c['other_user_name'] ?? 'User';
                                $otherName = $c['other_user_name'] ?? 'User';
                                break;
                            }
                        }
                    }
                ?>
                <div class="avatar" style="width: 32px; height: 32px; font-size: 0.9rem; margin-right: 10px;">
                    <?= strtoupper(substr($otherName, 0, 1)) ?>
                </div>
                Chat with <?= htmlspecialchars($otherName) ?>
            </div>
            <div class="messages-list" id="messages-list" data-cid="<?= (int)$selectedConversationId ?>">
                <?php if (empty($messages) || !is_array($messages)): ?>
                    <div style="padding: 20px; text-align: center; color: var(--text-secondary);">
                        No messages yet. Start the conversation!
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                    <?php $out = ($m['sender_id'] == $currentUserId); ?>
                    <div class="msg <?= $out ? 'out' : 'in' ?>" data-mid="<?= (int)$m['id'] ?>">
                        <div class="msg-text"><?= nl2br(htmlspecialchars($m['content'])) ?></div>
                        <div class="msg-time"><?= date('M j, g:ia', strtotime($m['created_at'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="composer">
                <textarea id="message-input" placeholder="Type your message..."></textarea>
                <button class="btn-send" id="send-btn">
                     <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    window.ASSET_ROOT_JS = "<?= ASSET_ROOT ?>";
    const CURRENT_USER_ID = <?= json_encode($currentUserId) ?>;
</script>
<script src="assets/js/modal-ui.js"></script>
<script src="assets/js/messages.js"></script>
</body>
</html>
