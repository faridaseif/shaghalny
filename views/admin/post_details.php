<?php 
ob_start(); 
$post = $post ?? null;
$comments = $comments ?? [];

if (!$post) {
    echo "Post not found or deleted.";
    $content = ob_get_clean();
    include 'layout.php';
    exit;
}
?>

<div style="margin-bottom: 1rem;">
    <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=community" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Feed
    </a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-newspaper me-2"></i> Post Content
        </div>
        <div style="display:flex; gap:0.5rem">
            <button class="btn btn-danger" onclick="deletePost(<?php echo $post['id']; ?>)">
                <i class="fas fa-trash me-2"></i> Delete Post
            </button>
        </div>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem">
            <div style="width:48px; height:48px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; overflow:hidden">
                <?php if (!empty($post['author_avatar'])): ?>
                    <img src="/shaghalny8/shaghalny/public/<?php echo $post['author_avatar']; ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover">
                <?php else: ?>
                    <span style="font-weight:bold; color:white; font-size:1.2rem"><?php echo strtoupper(substr($post['author_name'], 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            <div>
                <h4 style="margin:0; font-size:1.1rem;"><?php echo htmlspecialchars($post['author_name']); ?></h4>
                <div style="color:var(--text-secondary); font-size:0.9rem">User #<?php echo $post['user_id']; ?> • <?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></div>
            </div>
        </div>
        
        <div style="font-size:1rem; line-height:1.6; margin-bottom:1rem; white-space:pre-wrap; background:#f9fafb; padding:1rem; border-radius:8px; border:1px solid #e5e7eb">
            <?php echo htmlspecialchars($post['content']); ?>
        </div>

        <?php if (!empty($post['image_url'])): ?>
            <div style="margin-bottom:1rem">
                <img src="/shaghalny8/shaghalny/public/<?php echo $post['image_url']; ?>" alt="Post Image" style="max-width:100%; max-height:400px; border-radius:8px; border:1px solid #e5e7eb">
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:2rem; padding-top:1rem; border-top:1px solid #e5e7eb; color:var(--text-secondary)">
            <span><i class="fas fa-heart text-danger"></i> <?php echo $post['likes_count']; ?> Likes</span>
            <span><i class="fas fa-comment text-primary"></i> <?php echo count($comments); ?> Comments</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-comments me-2"></i> Comments (<?php echo count($comments); ?>)
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-secondary)">No comments on this post.</td></tr>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr id="comment-row-<?php echo $comment['id']; ?>">
                            <td>#<?php echo $comment['id']; ?></td>
                            <td>
                                <div style="font-weight:600"><?php echo htmlspecialchars($comment['author_name']); ?></div>
                                <div style="font-size:0.75rem; color:var(--text-secondary)">User #<?php echo $comment['user_id']; ?></div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($comment['content']); ?>
                            </td>
                            <td><?php echo date('M j, g:i a', strtotime($comment['created_at'])); ?></td>
                            <td>
                                <button class="icon-btn" style="color:red" title="Delete Comment" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>

<script>
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=deletePost', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/shaghalny8/shaghalny/public/index.php?controller=admin&action=community';
            } else {
                alert('Error: ' + (data.error || 'Failed to delete post'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=deleteComment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_id: commentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row
                const row = document.getElementById('comment-row-' + commentId);
                if (row) row.remove();
            } else {
                alert('Error: ' + (data.error || 'Failed to delete comment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
