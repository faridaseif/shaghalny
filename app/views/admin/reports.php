<?php 
ob_start(); 
// Ensure variables exist to prevent errors if loaded directly or data fetch fails
$reports = $reports ?? [];
$messages = $messages ?? [];
$pendingReports = []; // Initialize to avoid warning later
$pendingMessages = [];
?>

<!-- Stats Overview -->
<div class="dashboard-metrics">
    <div class="metric-card">
        <div class="metric-info">
            <h3>Total Reports</h3>
            <div class="value"><?php echo count($reports); ?></div>
            <div class="trend up">
                <i class="fas fa-file-contract"></i> Since launch
            </div>
        </div>
        <div class="metric-icon icon-bg-red">
            <i class="fas fa-exclamation-circle" style="color:#ef4444"></i>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3>Support Messages</h3>
            <div class="value"><?php echo count($messages); ?></div>
            <div class="trend up">
                <i class="fas fa-envelope"></i> Total received
            </div>
        </div>
        <div class="metric-icon icon-bg-blue">
            <i class="fas fa-comment-dots"></i>
        </div>
    </div>
    
    <div class="metric-card">
        <div class="metric-info">
            <h3>Pending Actions</h3>
            <div class="value">
                <?php 
                $pendingReports = array_filter($reports, fn($r) => $r['status'] === 'pending');
                $pendingMessages = array_filter($messages, fn($m) => $m['status'] === 'open');
                echo count($pendingReports) + count($pendingMessages);
                ?>
            </div>
            <div class="trend up">
                <span class="status-badge status-pending">Needs Review</span>
            </div>
        </div>
        <div class="metric-icon icon-bg-orange">
            <i class="fas fa-clock"></i>
        </div>
    </div>
</div>

<!-- Tabs (Simple Implementation) -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-clipboard-list me-2"></i> User Reports
        </div>
        <div>
            <span class="status-badge status-pending">Pending: <?php echo count($pendingReports); ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Title & Description</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                    <tr><td colspan="8" style="text-align:center;">No reports found.</td></tr>
                <?php else: ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>#<?php echo $report['id']; ?></td>
                            <td>
                                <?php if ($report['user_id']): ?>
                                    User #<?php echo $report['user_id']; ?>
                                <?php else: ?>
                                    <span style="color:var(--text-secondary)">Guest</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo ucfirst(str_replace('_', ' ', $report['report_type'])); ?>
                                <?php if ($report['post_id']): ?>
                                    <div style="font-size:0.75rem; color:#6366f1;">
                                        <i class="fas fa-link"></i> Post #<?php echo $report['post_id']; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($report['title']); ?></strong>
                                <div style="font-size:0.85rem; color:var(--text-secondary); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($report['description']); ?>
                                </div>
                                <?php if (!empty($report['post_content'])): ?>
                                    <div style="margin-top:4px; padding:4px 8px; background:#f3f4f6; border-radius:4px; font-size:0.8rem; border-left: 2px solid #ccc;">
                                        <em>"<?php echo htmlspecialchars(mb_strimwidth($report['post_content'], 0, 30, '...')); ?>"</em>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $priorityColors = [
                                    'low' => 'gray',
                                    'medium' => 'orange',
                                    'high' => 'red',
                                    'urgent' => 'darkred'
                                ];
                                $pColor = $priorityColors[$report['priority']] ?? 'black';
                                ?>
                                <span style="font-weight:600; color:<?php echo $pColor; ?>">
                                    <?php echo ucfirst($report['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $report['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($report['created_at'])); ?></td>
                            <td>
                                <button class="icon-btn" title="View Details" onclick="fetchReportDetails(<?php echo $report['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn" title="Edit Status" onclick="openStatusModal(<?php echo $report['id']; ?>, '<?php echo $report['status']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="icon-btn" title="Delete Report" style="color:red;" onclick="deleteReport(<?php echo $report['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <?php if ($report['post_id']): ?>
                                    <button class="icon-btn delete-post-btn" title="Delete Reported Post" style="color:darkred;" data-post-id="<?php echo $report['post_id']; ?>" onclick="deleteReportedPost(<?php echo $report['post_id']; ?>)">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-comments me-2"></i> Support Messages (Get Help)
        </div>
        <div>
            <span class="status-badge status-pending">Open: <?php echo count($pendingMessages); ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sender</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr><td colspan="7" style="text-align:center;">No messages found.</td></tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td>#<?php echo $msg['id']; ?></td>
                            <td>
                                <?php if (!empty($msg['guest_name'])): ?>
                                    <div><?php echo htmlspecialchars($msg['guest_name']); ?></div>
                                    <div style="font-size:0.75rem; color:var(--text-secondary)"><?php echo htmlspecialchars($msg['guest_email']); ?></div>
                                    <span class="status-badge" style="background:#eee; color:#666; font-size:0.65rem">GUEST</span>
                                <?php elseif ($msg['user_id']): ?>
                                    User #<?php echo $msg['user_id']; ?>
                                    <span class="status-badge" style="background:#e0e7ff; color:#3730a3; font-size:0.65rem">USER</span>
                                <?php else: ?>
                                    Anonymous
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td title="<?php echo htmlspecialchars($msg['message']); ?>">
                                <?php echo htmlspecialchars(mb_strimwidth($msg['message'], 0, 50, '...')); ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $msg['status'] === 'open' ? 'pending' : 'active'; ?>">
                                    <?php echo ucfirst($msg['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, g:ia', strtotime($msg['created_at'])); ?></td>
                            <td>
                                <button class="icon-btn" title="Reply" onclick="openReplyModal(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars($msg['guest_email'] ?? $msg['user_email'] ?? ''); ?>', '<?php echo htmlspecialchars($msg['subject']); ?>')">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="icon-btn" title="Resolve (Close)" onclick="resolveMessage(<?php echo $msg['id']; ?>)">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button class="icon-btn" title="Delete Message" style="color:red;" onclick="deleteSupportMessage(<?php echo $msg['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Reply Modal -->
<div id="reply-modal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color:#fefefe; margin:10% auto; padding:20px; border:1px solid #888; width:500px; border-radius:8px;">
        <span class="close" onclick="closeReplyModal()" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
        <h2 style="margin-top:0;">Reply to User</h2>
        <form id="reply-form">
            <input type="hidden" id="reply-id" name="message_id">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">To:</label>
                <input type="email" id="reply-email" name="email" readonly style="width:100%; padding:8px; background:#f0f0f0; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Subject:</label>
                <input type="text" id="reply-subject" name="subject" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Message:</label>
                <textarea id="reply-message" name="message" rows="6" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;" required placeholder="Type your reply here..."></textarea>
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="closeReplyModal()" style="padding:8px 16px; background:#ccc; border:none; border-radius:4px; cursor:pointer; margin-right:10px;">Cancel</button>
                <button type="submit" style="padding:8px 16px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Send Reply</button>
            </div>
        </form>
    </div>
</div>

<!-- Status Edit Modal -->
<div id="status-modal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:300px; border-radius:8px;">
        <span class="close" onclick="closeStatusModal()" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
        <h3 style="margin-top:0;">Update Report Status</h3>
        <input type="hidden" id="status-report-id">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px;">Status:</label>
            <select id="status-select" style="width:100%; padding:8px;">
                <option value="pending">Pending</option>
                <option value="under_review">Under Review</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        <div style="text-align:right;">
             <button onclick="submitStatusUpdate()" style="padding:8px 16px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Update</button>
        </div>
    </div>
</div>

<!-- Report Details Modal -->
<div id="report-details-modal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color:#fefefe; margin:10% auto; padding:20px; border:1px solid #888; width:500px; border-radius:8px;">
        <span class="close" onclick="closeReportDetailsModal()" style="color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
        <h2 style="margin-top:0;">Report Details</h2>
        <div id="report-details-content">Loading...</div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="custom-confirm-modal" class="modal" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color:#fff; margin:20% auto; padding:25px; width:400px; border-radius:10px; text-align:center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 id="confirm-title" style="margin-top:0; color:#333;">Are you sure?</h3>
        <p id="confirm-message" style="color:#666; margin-bottom:25px;">Do you really want to perform this action?</p>
        <div style="display:flex; justify-content:center; gap:15px;">
            <button onclick="closeConfirmModal()" style="padding:10px 20px; background:#e0e0e0; border:none; border-radius:5px; cursor:pointer; font-weight:bold; color:#333;">Cancel</button>
            <button id="confirm-yes-btn" style="padding:10px 20px; background:#dc3545; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Yes, Proceed</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-notification" style="visibility:hidden; min-width:250px; background-color:#333; color:#fff; text-align:center; border-radius:5px; padding:16px; position:fixed; z-index:3000; left:50%; bottom:30px; transform:translateX(-50%); font-size:16px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
    <span id="toast-message">Notification</span>
</div>

<style>
    /* Toast Animation */
    #toast-notification.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    @-webkit-keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @-webkit-keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
</style>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>

<script>
// --- Custom UI Helpers ---
let confirmCallback = null;

function showConfirm(message, callback, title = 'Are you sure?', confirmBtnText = 'Yes, Proceed', confirmBtnColor = '#dc3545') {
    document.getElementById('confirm-title').innerText = title;
    document.getElementById('confirm-message').innerText = message;
    
    const yesBtn = document.getElementById('confirm-yes-btn');
    yesBtn.innerText = confirmBtnText;
    yesBtn.style.backgroundColor = confirmBtnColor;
    yesBtn.onclick = function() {
        closeConfirmModal();
        if (callback) callback();
    };
    
    document.getElementById('custom-confirm-modal').style.display = 'block';
}

function closeConfirmModal() {
    document.getElementById('custom-confirm-modal').style.display = 'none';
}

function showToast(message, isError = false) {
    const toast = document.getElementById("toast-notification");
    const msgSpan = document.getElementById("toast-message");
    
    msgSpan.innerText = message;
    toast.style.backgroundColor = isError ? "#dc3545" : "#28a745"; // Red for error, Green for success
    
    toast.className = "show";
    setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
}

// --- Report Functions ---

function fetchReportDetails(id) {
    document.getElementById('report-details-modal').style.display = 'block';
    fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=getReportDetails&id=' + id)
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                const r = data.report;
                const html = `
                    <p><strong>ID:</strong> #${r.id}</p>
                    <p><strong>Type:</strong> ${r.report_type}</p>
                    <p><strong>Title:</strong> ${r.title}</p>
                    <p><strong>Description:</strong> ${r.description}</p>
                    <p><strong>Status:</strong> ${r.status}</p>
                    <p><strong>Date:</strong> ${r.created_at}</p>
                    ${r.post_id ? `<p><strong>Post ID:</strong> ${r.post_id}</p>` : ''}
                `;
                document.getElementById('report-details-content').innerHTML = html;
            } else {
                document.getElementById('report-details-content').innerHTML = 'Error loading details.';
            }
        });
}
function closeReportDetailsModal() {
    document.getElementById('report-details-modal').style.display = 'none';
}

function deleteReport(id) {
    showConfirm('Do you really want to delete this report?', function() {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=deleteReport', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        }).then(r => r.json()).then(d => {
            if(d.success) {
                showToast('Report deleted successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error deleting report', true);
            }
        });
    });
}

function openStatusModal(id, currentStatus) {
    document.getElementById('status-modal').style.display = 'block';
    document.getElementById('status-report-id').value = id;
    document.getElementById('status-select').value = currentStatus;
}
function closeStatusModal() {
    document.getElementById('status-modal').style.display = 'none';
}
function submitStatusUpdate() {
    const id = document.getElementById('status-report-id').value;
    const status = document.getElementById('status-select').value;
    
    fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=updateReportStatus', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id, status: status})
    }).then(r => r.json()).then(d => {
        if(d.success) {
            closeStatusModal();
            showToast('Status updated successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Error updating status', true);
        }
    });
}

// --- Support Message Functions ---

function deleteSupportMessage(id) {
    showConfirm('Delete this support message?', function() {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=deleteSupportMessage', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        }).then(r => r.json()).then(d => {
            if(d.success) {
                showToast('Message deleted successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error deleting message', true);
            }
        });
    }, 'Confirm Deletion', 'Delete', '#dc3545');
}

function resolveMessage(id) {
    showConfirm('Mark this message as Resolved (Closed)?', function() {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=resolveSupportMessage', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        }).then(r => r.json()).then(d => {
            if(d.success) {
                showToast('Message resolved');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error resolving message', true);
            }
        });
    }, 'Resolve Message', 'Resolve', '#28a745');
}

function deleteReportedPost(postId) {
    showConfirm('Delete this reported post? This cannot be undone.', function() {
        fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=deletePost', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({post_id: postId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Post deleted successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error deleting post: ' + (data.error || 'Unknown error'), true);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', true);
        });
    }, 'Delete Post', 'Delete Post', '#dc3545');
}

// Reply Modal Logic
function openReplyModal(id, email, subject) {
    document.getElementById('reply-modal').style.display = 'block';
    document.getElementById('reply-id').value = id;
    document.getElementById('reply-email').value = email;
    document.getElementById('reply-subject').value = 'Re: ' + subject;
    document.getElementById('reply-message').focus();
}

function closeReplyModal() {
    document.getElementById('reply-modal').style.display = 'none';
    document.getElementById('reply-form').reset();
}

document.getElementById('reply-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        message_id: document.getElementById('reply-id').value,
        email: document.getElementById('reply-email').value,
        subject: document.getElementById('reply-subject').value,
        reply_body: document.getElementById('reply-message').value
    };
    
    // Disable button
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Sending...';

    fetch('/shaghalny8/shaghalny/public/index.php?controller=admin&action=replyToSupportMessage', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            closeReplyModal();
            showToast('Reply sent successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Error: ' + (data.error || 'Failed to send reply'), true);
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        showToast('An error occurred', true);
        btn.disabled = false;
        btn.textContent = originalText;
    });
});
</script>
