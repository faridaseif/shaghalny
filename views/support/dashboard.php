<?php
// app/views/support/dashboard.php
// This view assumes the user is ALREADY logged in (enforced by Controller)

// Standalone Mode Support
if (!defined('ASSET_ROOT')) {
    define('ASSET_ROOT', '../../public');
}
if (!isset($userReports)) {
    $userReports = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Dashboard - Shaghalny</title>
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/support.css?v=2">
    <?php
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = preg_replace('#/app/.*#', '', $scriptDir);
    $baseUrl = rtrim($baseUrl, '/');
    ?>
</head>
<body>
    <!-- Header -->
    <header class="support-header">
        <div class="header-content">
            <div class="logo">
                <h1>Shaghalny Support</h1>
            </div>
            <div class="header-actions">
                <a href="index.php?page=home" class="support-btn" style="background-color: transparent; color: #666;">Back to Feed</a>
                <div class="user-avatar">
                   <?= strtoupper(substr($currentUserName ?? 'U', 0, 1)); ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Banner -->
    <section class="support-banner">
        <div class="banner-content">
            <h1>Your Support Dashboard</h1>
            <p>Track your reports and get help quickly.</p>
        </div>
    </section>

    <!-- Interactive Cards -->
    <section class="support-cards">
        <div class="card report-card">
            <div class="card-icon">📋</div>
            <h3>Report Issue</h3>
            <p>Submit a new report about payment, safety, or other concerns.</p>
            <button class="card-btn report-btn" onclick="openReportModal()">Report Problem</button>
        </div>
        <div class="card help-card">
            <div class="card-icon">💬</div>
            <h3>Get Help</h3>
            <p>Send a message to our support team.</p>
            <button class="card-btn help-btn" onclick="openHelpModal()">Chat with Support</button>
        </div>
        <div class="card tips-card">
            <div class="card-icon">🛡️</div>
            <h3>Safety Guide</h3>
            <p>Review safety tips and community guidelines.</p>
            <a href="index.php?action=safety_tips" class="card-btn tips-btn">View Guide</a>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="support-main">
        <!-- My Reports Section -->
        <div class="reports-section">
            <div class="section-header">
                <span class="section-icon">📄</span>
                <h2>My Active Reports</h2>
            </div>
            <div id="reports-list" class="reports-list">
                <?php if (!empty($userReports)): ?>
                    <?php foreach ($userReports as $report): ?>
                        <div class="report-item">
                            <div class="report-header">
                                <h3><?php echo htmlspecialchars($report['title']); ?></h3>
                                <span class="status-badge status-<?php echo htmlspecialchars($report['status']); ?>">
                                    <?php 
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'under_review' => 'Under Review',
                                        'resolved' => 'Resolved',
                                        'closed' => 'Closed'
                                    ];
                                    echo $statusLabels[$report['status']] ?? ucfirst($report['status']);
                                    ?>
                                </span>
                            </div>
                            <p class="report-description"><?php echo htmlspecialchars($report['description']); ?></p>
                            <div class="report-footer">
                                <span class="report-date">Reported <?php echo timeAgo($report['created_at']); ?></span>
                                <a href="#" class="view-details" onclick="viewReportDetails(<?php echo $report['id']; ?>); return false;">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-reports">You have no active reports.</p>
                <?php endif; ?>
            </div>
            <a href="#" class="new-report-link" onclick="openReportModal(); return false;">+ Report New Issue</a>
        </div>

        <!-- Safety Resources Section -->
        <div class="resources-section">
            <div class="section-header">
                <span class="section-icon">🛡️</span>
                <h2>Quick Resources</h2>
            </div>
            <div class="resources-list">
                <div class="resource-item">
                    <div class="resource-icon">📞</div>
                    <div class="resource-content">
                        <h3>Emergency Contacts</h3>
                        <p>Manage your trusted contacts for emergencies.</p>
                        <a href="#" class="resource-link" onclick="openEmergencyContacts(); return false;">Manage Contacts</a>
                    </div>
                </div>
                <div class="resource-item">
                    <div class="resource-icon">⭐</div>
                    <div class="resource-content">
                        <h3>Trust & Ratings</h3>
                        <p>Learn how our rating system works.</p>
                        <a href="#" class="resource-link" onclick="openRatingsModal(); return false;">View Rating System</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section-header">
            <span class="section-icon">❓</span>
            <h2>Common Questions</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-question">How long does a review take?</div>
                <div class="faq-answer">
                    Most reports are reviewed within 24 hours. Urgent safety concerns are prioritized immediately.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Can I edit my report?</div>
                <div class="faq-answer">
                    Once submitted, reports cannot be edited to preserve the record. You can add more details by contacting support directly with your Report ID.
                </div>
            </div>
        </div>
    </section>

    <!-- Report Modal -->
    <div id="report-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReportModal()">&times;</span>
            <h2>Report an Issue</h2>
            <form id="report-form">
                <div class="form-group">
                    <label for="report-type">Issue Type</label>
                    <select id="report-type" name="report_type" required>
                        <option value="payment_issue">Payment Issue</option>
                        <option value="theft">Theft</option>
                        <option value="harassment">Harassment</option>
                        <option value="safety_concern">Safety Concern</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="report-title">Title</label>
                    <input type="text" id="report-title" name="title" placeholder="Brief description of the issue" required>
                </div>
                <div class="form-group">
                    <label for="report-description">Description</label>
                    <textarea id="report-description" name="description" rows="5" placeholder="Provide detailed information about the issue..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="report-priority">Priority</label>
                    <select id="report-priority" name="priority">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeReportModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help/Support Chat Modal -->
    <div id="help-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeHelpModal()">&times;</span>
            <h2>Chat with Support</h2>
            <form id="help-form">
                <div class="form-group">
                    <label for="help-subject">Subject</label>
                    <input type="text" id="help-subject" name="subject" placeholder="What can we help you with?" required>
                </div>
                <div class="form-group">
                    <label for="help-message">Message</label>
                    <textarea id="help-message" name="message" rows="6" placeholder="Describe your question or concern..." required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeHelpModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Details Modal -->
    <div id="report-details-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReportDetailsModal()">&times;</span>
            <div id="report-details-content">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Trust & Ratings Modal -->
    <div id="ratings-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRatingsModal()">&times;</span>
            <h2>Trust & Ratings</h2>
            <div class="modal-body">
                <p>Our rating system helps keep the community safe and reliable. Here's how it works:</p>
                <div class="rating-explanation">
                    <div class="rating-item">
                        <div class="rating-stars">⭐⭐⭐⭐⭐</div>
                        <p><strong>5 Stars:</strong> Excellent experience. Highly recommended.</p>
                    </div>
                    <div class="rating-item">
                        <div class="rating-stars">⭐⭐⭐</div>
                        <p><strong>3 Stars:</strong> Average experience. Met expectations but room for improvement.</p>
                    </div>
                    <div class="rating-item">
                        <div class="rating-stars">⭐</div>
                        <p><strong>1 Star:</strong> Poor experience. Significant issues reported.</p>
                    </div>
                </div>
                <p>Both workers and employers start with no rating. Ratings are visible on profiles after the first completed job.</p>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-submit" onclick="closeRatingsModal()">Got it</button>
            </div>
        </div>
    </div>

    <!-- Emergency Contacts Modal -->
    <div id="emergency-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEmergencyContactsModal()">&times;</span>
            <h2>Emergency Contacts</h2>
            <div class="modal-body">
                <p>Add trusted contacts who Shaghalny can notify if you press the emergency button.</p>
                <div class="emergency-list">
                    <!-- Placeholder for no contacts -->
                    <div class="no-contacts">
                        <p>You haven't added any emergency contacts yet.</p>
                    </div>
                </div>
                <button class="btn-cancel add-contact-btn" style="width: 100%; margin-top: 1rem;">+ Add New Contact</button>
            </div>
        </div>
    </div>

    <script>
        window.ASSET_ROOT_JS = "<?= ASSET_ROOT ?>";
        const path = window.location.pathname;
        const appIndex = path.indexOf('/app/');
        if (appIndex !== -1) {
            window.APP_BASE_URL = path.substring(0, appIndex);
        } else {
             window.APP_BASE_URL = path.split('/support')[0];
        }
        const CURRENT_USER_ID = "<?= $_SESSION['user_id'] ?? '' ?>";
    </script>
    <script src="<?= ASSET_ROOT ?>/assets/js/modal-ui.js"></script>
    <script src="<?= ASSET_ROOT ?>/assets/js/support.js"></script>
    <script>
        // FAQ Accordion Logic
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentNode;
                const isActive = item.classList.contains('active');
                
                // Close all other items
                document.querySelectorAll('.faq-item').forEach(i => {
                    i.classList.remove('active');
                });

                // Toggle current item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
    return floor($diff / 31536000) . ' years ago';
}
?>
