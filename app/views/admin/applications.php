<?php ob_start(); ?>

<div class="card">
    <div class="card-header">
        <div class="card-title">Manage Applications</div>
    </div>
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: var(--admin-text-light);">
                            No applications found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($app['job_id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar-small">
                                        <?php echo strtoupper(substr($app['first_name'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($app['status']); ?>">
                                    <?php echo ucfirst($app['status']); ?>
                                </span>
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

