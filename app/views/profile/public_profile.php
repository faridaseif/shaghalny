<?php
$user = $viewData['user'] ?? [];
$publicInfo = $viewData['publicInfo'] ?? [];
$achievements = $viewData['achievements'] ?? [];
$skills = $viewData['skills'] ?? [];
$languages = $viewData['languages'] ?? [];
$jobCats = $viewData['jobCats'] ?? [];
$education = $viewData['education'] ?? [];
$experience = $viewData['experience'] ?? [];
$activities = $viewData['activities'] ?? [];
$reviews = $viewData['reviews'] ?? [];
$career = $viewData['career'] ?? [];
$personal = $viewData['personal'] ?? [];

// Age Calculation
$age = '';
if (!empty($personal['birthdate'])) {
    $dob = new DateTime($personal['birthdate']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

// Initials Calculation
$initials = '';
if (!empty($user['first_name'])) {
    $initials .= strtoupper(substr($user['first_name'], 0, 1));
}
if (!empty($user['last_name'])) {
    $initials .= strtoupper(substr($user['last_name'], 0, 1));
}

function safeVal($val) { return htmlspecialchars($val ?? ''); }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/shaghalny/public/assets/css/styles.css">
    <link rel="stylesheet" href="/shaghalny/public/assets/css/profile.css">
    <link rel="stylesheet" href="/shaghalny/public/assets/css/Header.css">
    <link rel="stylesheet" href="/shaghalny/public/assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- React & Babel -->
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <!-- Custom Components -->
    <script type="text/babel" src="assets/js/components/Header.js?v=<?php echo time(); ?>"></script>
</head>
<body>

    <div id="header-root"></div>
    <script type="text/babel">
        const Header = window.Header;
        const userName = "<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest'; ?>";
        const root = ReactDOM.createRoot(document.getElementById('header-root'));
        root.render(<Header username={userName} />);
    </script>

    <div class="profile-cover"></div>

    <div class="container mt-5">

        <!-- Profile Header -->
        <div class="profile-card shadow-lg p-4 mb-4 d-flex align-items-center">

            <?php if (!empty($personal['profile_picture'])): ?>
                <img src="<?php echo safeVal($personal['profile_picture']); ?>" class="profile-img me-4" id="profilePic">
            <?php else: ?>
                <div class="profile-img me-4 d-flex align-items-center justify-content-center bg-primary text-white" style="font-size: 2.5rem; font-weight: bold;">
                    <?php echo $initials; ?>
                </div>
            <?php endif; ?>

            <div class="flex-grow-1">
                <h2 class="profile-name mb-1"><?php echo safeVal(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></h2>
                <div class="text-secondary">
                    <?php echo $age ? safeVal($age) . ' years old' : 'Age not set'; ?> • <?php echo safeVal($career['career_level'] ?? 'No Level'); ?>
                </div>

                <div class="d-flex align-items-center mt-1">
                    <span class="rating-star">⭐</span>
                    <span class="rating-text ms-2">
                        <?php echo safeVal($publicInfo['average_rating'] ?? '0'); ?> Rating • <?php echo safeVal($publicInfo['total_reviews'] ?? '0'); ?> Reviews
                    </span>
                </div>

                <div class="mt-2">
                    <span class="badge bg-primary-custom me-2">Verified</span>
                    <span class="badge bg-secondary-custom">Top Rated</span>
                </div>
                
                <?php $currentUserId = $_SESSION['user_id'] ?? null; ?>
                <?php if ($currentUserId && isset($user['user_id']) && $currentUserId == $user['user_id']): ?>
                    <a href="index.php?action=private_profile" class="edit-btn" id="editBtn">Edit My Profile</a>
                <?php elseif ($currentUserId && isset($user['user_id'])): ?>
                    <a href="index.php?controller=Message&action=startConversation&recipient_id=<?php echo $user['user_id']; ?>" 
                       class="btn btn-primary mt-3" 
                       style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                       <i class="fas fa-envelope me-2"></i> Message
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <!-- Stats -->
        <div class="row mb-4 g-4">

            <div class="col-md-4">
                <div class="stats-card text-center p-4 shadow-sm">
                    <h6 class="label">Total Earnings</h6>
                    <h2 class="value">$<?php echo safeVal($user['total_earnings'] ?? '0'); ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stats-card text-center p-4 shadow-sm">
                    <h6 class="label">Jobs Completed</h6>
                    <h2 class="value"><?php echo safeVal($publicInfo['recent_activity'] ?? '0'); ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stats-card text-center p-4 shadow-sm">
                    <h6 class="label">Rating</h6>
                    <h2 class="value"><?php echo safeVal($publicInfo['average_rating'] ?? '0'); ?> ⭐</h2>
                </div>
            </div>

        </div>

        <!-- Skills + About Me -->
        <div class="row mb-4 g-4">

            <!-- SKILLS -->
            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">🧰 Skills & Interests</h5>

                    <div class="skills-container mt-3">
                        <?php foreach ($skills as $s): ?>
                            <span class="skill-badge"><?php echo safeVal($s['skill_name']); ?></span>
                        <?php endforeach; ?>
                        <?php foreach ($jobCats as $c): ?>
                            <span class="skill-badge bg-warning"><?php echo safeVal($c['category_name']); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <h5 class="section-title mt-4">📌 About Me</h5>
                    <p class="text-secondary-custom">
                        <?php echo safeVal($viewData['personal']['about_me'] ?? ''); ?>
                    </p>
                </div>
            </div>

            <!-- RECENT ACTIVITY -->
            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">🧾 Recent Activity</h5>
                    <?php if (!$activities): ?>
                        <div class="text-muted">No recent activity yet.</div>
                    <?php endif; ?>
                    <?php foreach ($activities as $act): ?>
                    <div class="activity-item d-flex justify-content-between mb-3">
                        <div>
                            <strong><?php echo safeVal($act['job_title']); ?></strong>
                            <div class="subtext">
                                <?php echo safeVal($act['company_name']); ?>
                                <?php if (!empty($act['end_date'])): ?>
                                    • Completed <?php echo safeVal($act['end_date']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="price">✔</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Experience + Education -->
        <div class="row mb-4 g-4">
            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">💼 Work Experience</h5>
                    <?php if (!$experience): ?>
                        <div class="text-muted">No experience added.</div>
                    <?php endif; ?>
                    <?php foreach ($experience as $exp): ?>
                        <div class="mb-3">
                            <strong><?php echo safeVal($exp['job_title']); ?></strong> @ <?php echo safeVal($exp['company_name']); ?><br>
                            <span class="subtext"><?php echo safeVal($exp['job_category']); ?> • <?php echo safeVal($exp['experience_type']); ?></span><br>
                            <span class="subtext"><?php echo safeVal($exp['start_date']); ?> → <?php echo safeVal($exp['end_date']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">🎓 Education</h5>
                    <?php if (!$education): ?>
                        <div class="text-muted">No education added.</div>
                    <?php else: ?>
                        <div class="mb-2"><strong><?php echo safeVal($education['education_level']); ?></strong></div>
                        <div class="subtext"><?php echo safeVal($education['school_name']); ?></div>
                        <?php if(!empty($education['certificate_name'])): ?>
                            <div class="subtext text-info"><?php echo safeVal($education['certificate_name']); ?></div>
                        <?php endif; ?>
                        <div class="subtext"><?php echo safeVal($education['language_of_study']); ?></div>
                        <div class="subtext">Graduation Year: <?php echo safeVal($education['graduation_year']); ?></div>
                        <div class="subtext">Grade/GPA: <?php echo safeVal($education['grade']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Achievements + Reviews -->
        <div class="row g-4">

            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">🏆 Achievements</h5>
                    <?php if (!$achievements): ?>
                        <div class="text-muted">No achievements yet.</div>
                    <?php endif; ?>
                    <?php foreach ($achievements as $a): ?>
                        <div class="achievement-badge"><?php echo safeVal($a['achievement_name']); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- REVIEWS -->
            <div class="col-md-6">
                <div class="section-card shadow-sm p-4">
                    <h5 class="section-title">⭐ Recent Reviews</h5>
                    <?php if (!$reviews): ?>
                        <div class="text-muted">No reviews yet.</div>
                    <?php endif; ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card d-flex mb-3">
                            <div class="review-avatar avatar-blue"><?php echo strtoupper(substr(safeVal($rev['reviewer_id']),0,1)); ?></div>
                            <div>
                                <strong>Reviewer #<?php echo safeVal($rev['reviewer_id']); ?></strong>
                                <div class="stars"><?php echo str_repeat('⭐', (int)$rev['rating']); ?></div>
                                <p><?php echo safeVal($rev['review_text']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>
<script src="assets/js/profile.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>
