<?php
// app/views/posts/feed.php
// Session is already started in index.php, so we don't need to start it again
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUserId = $_SESSION['user_id'] ?? null;

// Standalone Mode Support
if (!defined('ASSET_ROOT')) {
    // Assuming accessed as /shaghalny/app/views/posts/feed.php
    // We need to point to /shaghalny/app/public
    // This path adjustment depends on your exact URL structure.
    // Standalone Mode Support
    // Use relative path to avoid "shaghalny" folder name dependency
    define('ASSET_ROOT', '../../public');
}
if (!isset($posts)) {
    $posts = []; // Prevent error if accessed directly
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Feed - Shaghalny</title>
    <link rel="stylesheet" href="assets/css/feed.css">
    <link rel="stylesheet" href="assets/css/Header.css">
    <!-- React Dependencies -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <?php include APP_ROOT . '/app/views/layouts/header.php'; ?>


    <!-- Main Content -->
    <div class="container">
        <!-- Community Feed Post Creator -->
        <div class="feed-creator">
            <div class="creator-header">
                <span class="creator-icon">📝</span>
                <h2>Community Feed</h2>
            </div>
            <div class="creator-body">
                <?php if ($currentUserId): ?>
                    <form id="post-form" enctype="multipart/form-data">
                        <textarea id="post-content" name="content" placeholder="Share your latest achievement or job experience...." rows="4"></textarea>
                        <div id="image-preview" style="display: none; margin-bottom: 1rem;">
                            <img id="preview-img" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            <button type="button" id="remove-image" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer;">Remove Image</button>
                        </div>
                        <div class="creator-actions">
                            <div class="action-buttons">
                                <label for="photo-input" class="action-btn" style="cursor: pointer;">
                                    <span class="btn-icon">📷</span>
                                    <span>Photo</span>
                                </label>
                                <button type="button" class="action-btn" id="job-btn">
                                    <span class="btn-icon">💼</span>
                                    <span>Job Update</span>
                                </button>
                            </div>
                            <button type="submit" class="share-btn" id="share-btn">Share</button>
                        </div>
                        <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;">
                        <input type="hidden" id="post-type" name="post_type" value="text">
                    </form>
                <?php else: ?>
                    <div class="login-prompt">
                        <p>Want to share something? <a href="/login" class="login-link">Sign in</a> or <a href="/register" class="login-link">Sign up</a> to post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feed Posts -->
        <div class="feed-posts" id="feed-posts">
            <?php 
            // Debug output (remove in production)
            if (defined('APP_ENV') && APP_ENV === 'development') {
                echo "<!-- DEBUG: Posts count = " . count($posts ?? []) . " -->";
            }
            ?>
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <p>No posts yet. Be the first to share something!</p>
                    <?php if (defined('APP_ENV') && APP_ENV === 'development'): ?>
                        <p style="font-size: 0.8em; color: #999;">Debug: Posts array is empty. Check error logs.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card" data-post-id="<?= $post['id'] ?>">
                        <div class="post-header">
                            <div class="post-author">
                                <div class="author-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <?= strtoupper(substr($post['author_name'], 0, 1)) ?>
                                </div>
                                <div class="author-info">
                                    <div class="author-name"><?= htmlspecialchars($post['author_name']) ?></div>
                                    <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
                                </div>
                                <?php if ($currentUserId && (int)$post['user_id'] !== $currentUserId): ?>
                                    <a href="index.php?controller=Message&action=startConversation&recipient_id=<?= $post['user_id'] ?>" 
                                       class="btn-message" 
                                       style="margin-left: auto; margin-right: 10px; padding: 5px 12px; background: #e0e6f9; color: #0055d9; border-radius: 15px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                       Message
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if ($currentUserId): ?>
                                <div class="post-actions-menu">
                                    <button class="menu-btn" data-post-id="<?= $post['id'] ?>">⋯</button>
                                    <div class="menu-dropdown" id="menu-<?= $post['id'] ?>" style="display: none;">
                                        <?php if ((int)$post['user_id'] === $currentUserId): ?>
                                            <button class="menu-item edit-post" data-post-id="<?= $post['id'] ?>" data-content="<?= htmlspecialchars($post['content']) ?>">Edit</button>
                                            <button class="menu-item delete-post" data-post-id="<?= $post['id'] ?>">Delete</button>
                                        <?php else: ?>
                                            <button class="menu-item report-post" data-post-id="<?= $post['id'] ?>">🚩 Report Post</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="post-content">
                            <?php if ($post['post_type'] === 'photo' && $post['image_url']): ?>
                                <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Post image" class="post-image">
                            <?php endif; ?>
                            <p class="post-text" data-post-id="<?= $post['id'] ?>"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            <div class="post-edit-form" id="edit-form-<?= $post['id'] ?>" style="display: none;">
                                <textarea class="edit-textarea" data-post-id="<?= $post['id'] ?>"><?= htmlspecialchars($post['content']) ?></textarea>
                                <div class="edit-actions">
                                    <button class="save-edit" data-post-id="<?= $post['id'] ?>">Save</button>
                                    <button class="cancel-edit" data-post-id="<?= $post['id'] ?>">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <div class="post-actions">
                            <button class="action-like <?= $post['is_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
                                <span class="like-icon">❤️</span>
                                <span class="like-count"><?= (int)$post['likes_count'] ?></span>
                            </button>
                            <button class="action-comment" data-post-id="<?= $post['id'] ?>">
                                <span class="comment-icon">💬</span>
                                <span class="comment-count"><?= (int)$post['comments_count'] ?></span>
                            </button>
                            <button class="action-share">Share</button>
                        </div>
                        
                        <!-- Comments Section -->
                        <div class="comments-section" id="comments-<?= $post['id'] ?>" style="display: none;">
                            <div class="comments-list" id="comments-list-<?= $post['id'] ?>">
                                <!-- Comments will be loaded here -->
                            </div>
                            <?php if ($currentUserId): ?>
                                <div class="comment-form">
                                    <input type="text" class="comment-input" placeholder="Write a comment..." data-post-id="<?= $post['id'] ?>">
                                    <button class="comment-submit" data-post-id="<?= $post['id'] ?>">Post</button>
                                </div>
                            <?php else: ?>
                                <div class="comment-login-prompt">
                                    <p><a href="/login" class="login-link">Sign in</a> or <a href="/register" class="login-link">Sign up</a> to comment!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const CURRENT_USER_ID = <?= json_encode($currentUserId) ?>;
        window.ASSET_ROOT_JS = "<?= ASSET_ROOT ?>";
    </script>
    <script src="assets/js/modal-ui.js"></script>
    <script src="assets/js/feed.js"></script>
</body>
</html>

<?php
// Helper function to format time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('M j, Y', $timestamp);
}
?>

