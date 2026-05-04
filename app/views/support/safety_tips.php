<?php
// app/views/support/safety_tips.php
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Ensure ASSET_ROOT is defined if not already (fallback)
if (!defined('ASSET_ROOT')) {
    define('ASSET_ROOT', '.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Tips - Shaghalny</title>
    <!-- Adapted CSS path for standard structure -->
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/global.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/Header.css">
    <link rel="stylesheet" href="<?= ASSET_ROOT ?>/assets/css/support.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    // Calculate dynamic base URL
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = preg_replace('#/app/.*#', '', $scriptDir);
    $baseUrl = rtrim($baseUrl, '/');
    ?>
</head>
<body>
    <?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

    <div class="safety-tips-container">
        <!-- Adapted link for router compatibility -->
        <a href="<?= $baseUrl ?>/index.php?action=support" class="back-link">← Back to Support Center</a>
        
        <h1 style="margin-bottom: 2rem; color: #2c3e50;">Safety Guide & Community Standards</h1>
        
        <div class="safety-tip">
            <h2><span class="safety-tip-icon">🔒</span> Before Accepting a Job</h2>
            <ul>
                <li>Check the employer's rating and reviews before accepting any job.</li>
                <li>Verify that the job details match what was posted in the feed.</li>
                <li>Ask questions if anything seems unclear or suspicious before committing.</li>
                <li>Share your live location with a trusted contact before starting work.</li>
                <li>Trust your instincts - if something feels wrong, do not proceed.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">🤝</span> Community Guidelines</h2>
            <ul>
                <li><strong>Respect Others:</strong> Treat all users with dignity and respect. Harassment, hate speech, and discrimination are strictly prohibited.</li>
                <li><strong>Honesty:</strong> Be truthful in your job postings, profile information, and communications. Misrepresentation undermines trust.</li>
                <li><strong>Professionalism:</strong> Maintain professional conduct during interactions and work performance.</li>
                <li><strong>Reliability:</strong> Honor your commitments. If you must cancel, communicate clearly and promptly.</li>
                <li><strong>Privacy:</strong> Respect the privacy of others. Do not share personal contact details of others without consent.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">💰</span> Payment Safety</h2>
            <ul>
                <li>All payments should be processed through our secure platform to ensure protection.</li>
                <li>Never accept cash payments or wire transfers outside the platform, as these are not covered by our guarantees.</li>
                <li>Report any payment issues immediately to our support team.</li>
                <li>Keep records of all completed work and communications for dispute resolution.</li>
                <li>If you don't get paid, contact support right away - we'll help recover your money.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">🌙</span> Working Hours & Location</h2>
            <ul>
                <li>Avoid accepting jobs at very late hours or in isolated locations unless verified.</li>
                <li>If you must work late, inform someone you trust about your location and expected return time.</li>
                <li>Meet in public places for initial discussions when possible.</li>
                <li>If a job location changes unexpectedly, be cautious and report it if suspicious.</li>
                <li>You have the right to refuse work that makes you feel unsafe.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">🚨</span> Emergency Situations</h2>
            <ul>
                <li>If you feel threatened or unsafe, leave the situation immediately.</li>
                <li>Call emergency services (911) if you're in immediate danger.</li>
                <li>Report any harassment, threats, or inappropriate behavior right away via the Support Center.</li>
                <li>Use our 24/7 support hotline for urgent assistance.</li>
                <li>Set up emergency contacts in your profile for quick access during emergencies.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">📱</span> Communication Safety</h2>
            <ul>
                <li>Keep all communication through the platform when possible for record-keeping.</li>
                <li>Don't share personal information like your home address or private phone number publicly.</li>
                <li>If someone asks for personal information irrelevantly, report it.</li>
                <li>Be cautious of requests to communicate outside the platform (e.g., WhatsApp, Telegram).</li>
                <li>Block and report users who make you uncomfortable.</li>
            </ul>
        </div>

        <div class="safety-tip">
            <h2><span class="safety-tip-icon">✅</span> Best Practices</h2>
            <ul>
                <li>Always read job descriptions carefully before accepting to avoid misunderstandings.</li>
                <li>Arrive on time and communicate clearly with employers about progress.</li>
                <li>Take photos of completed work when appropriate for proof of completion.</li>
                <li>Rate employers honestly to help keep the community safe and informed.</li>
                <li>Report suspicious behavior - you're helping protect others too.</li>
            </ul>
        </div>
    </div>
</body>
</html>
