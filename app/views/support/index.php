<?php
// app/views/support/index.php
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$currentUserName = null;
if ($currentUserId) {
    try {
        global $pdo;
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as name, profile_picture FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $currentUserId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $currentUserName = $user['name'];
            }
        }
    } catch (Exception $e) {
        error_log("Error loading user: " . $e->getMessage());
    }
}

// Standalone Mode Support
if (!defined('ASSET_ROOT')) {
    // Adapted for integration: Assuming running from public/index.php
    define('ASSET_ROOT', '.');
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
    <title>Safety & Support Center - Shaghalny</title>
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/global.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/Header.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/support.css?v=2">

    <?php
    // Calculate dynamic base URL
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = preg_replace('#/app/.*#', '', $scriptDir);
    $baseUrl = rtrim($baseUrl, '/');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

    <!-- Main Banner -->
    <section class="support-banner">
        <div class="banner-content">
            <h1>Safety & Support Center</h1>
            <p>We're here to help keep you safe and resolve any issues. Report problems immediately.</p>
        </div>
    </section>

    <!-- Interactive Cards -->
    <section class="support-cards">
        <div class="card report-card">
            <div class="card-icon">📋</div>
            <h3>Report Issue</h3>
            <p>Report payment issues, theft, harassment, or safety concerns immediately.</p>
            <button class="card-btn report-btn" onclick="openReportModal()">Report Problem</button>
        </div>
        <div class="card help-card">
            <div class="card-icon">💬</div>
            <h3>Get Help</h3>
            <p>Chat with our support team for assistance with any questions or concerns.</p>
            <button class="card-btn help-btn" onclick="openHelpModal()">Chat with Support</button>
        </div>
        <div class="card tips-card">
            <div class="card-icon">🛡️</div>
            <h3>Safety Tips</h3>
            <p>Learn how to stay safe while working and what to do in different situations.</p>
            <a href="<?= $baseUrl ?>/index.php?action=safety_tips" class="card-btn tips-btn">View Safety Guide</a>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="support-main">
        <!-- My Reports Section -->
        <div class="reports-section">
            <div class="section-header">
                <span class="section-icon">📄</span>
                <h2>My Reports</h2>
            </div>
            <div id="reports-list" class="reports-list">
                <?php if ($currentUserId && !empty($userReports)): ?>
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
                <?php elseif ($currentUserId): ?>
                    <p class="no-reports">You haven't submitted any reports yet.</p>
                <?php else: ?>
                    <p class="no-reports">Please <a href="<?= $baseUrl ?>/index.php?page=login">sign in</a> to view your reports.</p>
                <?php endif; ?>
            </div>
            <?php if ($currentUserId): ?>
                <a href="#" class="new-report-link" onclick="openReportModal(); return false;">+ Report New Issue</a>
            <?php endif; ?>
        </div>

        <!-- Safety Resources Section -->
        <div class="resources-section">
            <div class="section-header">
                <span class="section-icon">🛡️</span>
                <h2>Safety Resources</h2>
            </div>
            <div class="resources-list">
                <div class="resource-item">
                    <div class="resource-icon">💰</div>
                    <div class="resource-content">
                        <h3>Payment Protection</h3>
                        <p>All payments are secured through our platform. If you don't get paid, we'll help recover your money.</p>
                    </div>
                </div>
                <div class="resource-item">
                    <div class="resource-icon">📞</div>
                    <div class="resource-content">
                        <h3>Emergency Contacts</h3>
                        <p>Always share your location with trusted contacts when working.</p>
                        <?php if ($currentUserId): ?>
                            <a href="#" class="resource-link" onclick="openEmergencyContacts(); return false;">Set Up Emergency Contacts</a>
                        <?php else: ?>
                            <a href="<?= $baseUrl ?>/index.php?page=login" class="resource-link">Sign in to set up contacts</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="resource-item">
                    <div class="resource-icon">⭐</div>
                    <div class="resource-content">
                        <h3>Trust & Ratings</h3>
                        <p>Check employer ratings and reviews before accepting jobs. Report suspicious behavior.</p>
                    </div>
                </div>
                <div class="resource-item">
                    <div class="resource-icon">📞</div>
                    <div class="resource-content">
                        <h3>24/7 Support Hotline</h3>
                        <p>Available around the clock for urgent assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section-header">
            <span class="section-icon">❓</span>
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-question">How do I report a payment issue?</div>
                <div class="faq-answer">
                    Go to the "Report Issue" section, select "Payment Issue" as the type, and provide details about the transaction, including the amount and date. Our team will investigate immediately.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Is my personal information safe?</div>
                <div class="faq-answer">
                    Yes, we use industry-standard encryption to protect your data. We never share your personal contact details with other users without your explicit permission.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">What should I do if I feel unsafe during a job?</div>
                <div class="faq-answer">
                    Your safety is our priority. If you feel unsafe, leave the location immediately. Call emergency services (911) if necessary. Then, report the incident to us using the "Safety Concern" option.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">How can I verify an employer?</div>
                <div class="faq-answer">
                    Check the employer's profile for verified badges, read reviews from other workers, and ensure their job posting details are clear and professional.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Can I cancel a job I've accepted?</div>
                <div class="faq-answer">
                    Yes, you can cancel a job if necessary. However, frequent cancellations may affect your reliability rating. Please inform the employer as soon as possible.
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
                <?php if (!$currentUserId): ?>
                <div class="form-group">
                    <label for="help-name">Your Name</label>
                    <input type="text" id="help-name" name="guest_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="help-email">Your Email</label>
                    <input type="email" id="help-email" name="guest_email" placeholder="We'll use this to reply" required>
                </div>
                <?php endif; ?>
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
            <div id="report-details-content">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
    <!-- End of Report Details Modal -->

    <script>
        window.ASSET_ROOT_JS = "<?= ASSET_ROOT ?>";
        // USE PHP Base URL to ensure accuracy in integrated env
        window.APP_BASE_URL = "<?= $baseUrl ?>";
        const CURRENT_USER_ID = "<?= $currentUserId ?? '' ?>";
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
