<?php 
ob_start(); 
$posts = $posts ?? [];
$page = $page ?? 1;
?>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-users me-2"></i> Community Feed
        </div>
        <div>
            <!-- Pagination Controls could go here -->
            <span class="status-badge" style="background:#e0e7ff; color:#3730a3">Page <?php echo $page; ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Author</th>
                    <th>Content</th>
                    <th>Engagement</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="6" style="text-align:center;">No posts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>#<?php echo $post['id']; ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem">
                                    <div style="width:32px; height:32px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; overflow:hidden">
                                        <?php if ($post['author_avatar']): ?>
                                            <img src="/shaghalny8/shaghalny/public/<?php echo $post['author_avatar']; ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover">
                                        <?php else: ?>
                                            <span style="font-weight:bold; color:white"><?php echo strtoupper(substr($post['author_name'], 0, 1)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:500"><?php echo htmlspecialchars($post['author_name']); ?></div>
                                        <div style="font-size:0.75rem; color:var(--text-secondary)">User #<?php echo $post['user_id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="max-width:300px;">
                                    <?php if ($post['post_type'] === 'photo'): ?>
                                        <div style="margin-bottom:0.25rem">
                                            <span class="status-badge status-active"><i class="fas fa-image"></i> Photo</span>
                                        </div>
                                    <?php endif; ?>
                                    <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis">
                                        <?php echo htmlspecialchars(mb_strimwidth($post['content'], 0, 80, '...')); ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; gap:1rem; font-size:0.85rem">
                                    <span title="Likes"><i class="fas fa-heart text-danger"></i> <?php echo $post['likes_count']; ?></span>
                                    <span title="Comments"><i class="fas fa-comment text-primary"></i> <?php echo $post['comments_count']; ?></span>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y H:i', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="/shaghalny/index.php?controller=admin&action=post_details&id=<?php echo $post['id']; ?>" class="icon-btn" title="View & Moderate">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="icon-btn" style="color:red" title="Delete Post" onclick="deletePost(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Simple Pagination -->
    <div style="padding: 1rem; display:flex; justify-content:center; gap:1rem">
        <?php if ($page > 1): ?>
            <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=community&page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
        <?php endif; ?>
        <?php if (count($posts) >= 20): ?>
            <a href="/shaghalny8/shaghalny/public/index.php?controller=admin&action=community&page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
        <?php endif; ?>
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
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
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
</script>
