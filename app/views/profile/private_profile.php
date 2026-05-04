<?php
$user = $viewData['user'] ?? [];
$personal = $viewData['personal'] ?? [];
$education = $viewData['education'] ?? [];
$experience = $viewData['experience'][0] ?? [];
$skills = $viewData['skills'] ?? [];
$languages = $viewData['languages'] ?? [];
$jobCats = $viewData['jobCats'] ?? [];
$career = $viewData['career'] ?? [];
$success = $viewSuccess ?? false;
$errors = $viewErrors ?? [];

function pf($v){return htmlspecialchars($v ?? '');}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
    <style>
        #header-root {
            position: relative;
            z-index: 9999; /* Ensure header is above profile cover */
        }
    </style>

    <div class="profile-cover"></div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mb-3">
                    <div class="success-icon-circle mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 60px; height: 60px; font-size: 30px;">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h4 class="mb-2">Success!</h4>
                <p class="text-muted">Your changes have been saved successfully.</p>
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Continue</button>
            </div>
        </div>
    </div>
    <?php if ($success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
            myModal.show();
        });
    </script>
    <?php endif; ?>

    <div class="container mt-5">
        <!-- Success handled by Toast now, removed static alert -->
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?><div><?php echo pf($e); ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="profile-card shadow-lg p-4 mb-4 d-flex align-items-center">
            <?php 
            $initials = '';
            $initials .= strtoupper(substr($user['first_name']??'',0,1));
            $initials .= strtoupper(substr($user['last_name']??'',0,1));
            ?>
            <?php if (!empty($personal['profile_picture'])): ?>
                <img src="<?php echo pf($personal['profile_picture']); ?>" class="profile-img me-4" id="profilePic">
            <?php else: ?>
                <div class="profile-img me-4 d-flex align-items-center justify-content-center bg-primary text-white" style="font-size: 2rem; font-weight: bold; width: 100px; height: 100px; border-radius: 50%;">
                    <?php echo $initials; ?>
                </div>
            <?php endif; ?>
            <div class="flex-grow-1">
                <h2 class="profile-name mb-1"><?php echo pf(($user['first_name'] ?? '').' '.($user['last_name'] ?? '')); ?></h2>
                <?php
                // Age Calculation
                $ageDisplay = 'Age not set';
                if (!empty($personal['birthdate'])) {
                    $dob = new DateTime($personal['birthdate']);
                    $now = new DateTime();
                    $ageDisplay = $now->diff($dob)->y . ' years old';
                }
                $levelDisplay = $career['career_level'] ?? 'No Level';
                ?>
                <div class="text-secondary">
                    <?php echo $ageDisplay; ?> • <?php echo pf($levelDisplay); ?>
                </div>
                <button class="btn btn-primary btn-sm mt-2" onclick="document.querySelector('input[name=\'profile_picture\']').click(); document.querySelector('input[name=\'profile_picture\']').scrollIntoView({behavior: 'smooth', block: 'center'});">Change Picture</button>
            </div>
        </div>

        <!-- Personal -->
        <div class="section-card shadow-sm p-4 mb-4">
            <h5 class="section-title">👤 Personal Info</h5>
            <form method="POST" action="index.php?action=private_profile" enctype="multipart/form-data">
                <input type="hidden" name="section" value="personal">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input class="form-control" name="first_name" value="<?php echo pf($user['first_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input class="form-control" name="last_name" value="<?php echo pf($user['last_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="birthdate" value="<?php echo pf($personal['birthdate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select</option>
                            <option value="male" <?php echo (strcasecmp($personal['gender'] ?? '', 'male') === 0) ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (strcasecmp($personal['gender'] ?? '', 'female') === 0) ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nationality</label>
                        <input class="form-control" name="nationality" value="<?php echo pf($personal['nationality'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <input class="form-control" name="country" value="<?php echo pf($personal['country'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input class="form-control" name="city" value="<?php echo pf($personal['city'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Area</label>
                        <input class="form-control" name="area" value="<?php echo pf($personal['area'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <div class="input-group">
                            <input class="form-control" name="phone" value="<?php echo pf($user['phone'] ?? ''); ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="alert('OTP sent to ' + document.querySelector('input[name=\'phone\']').value)">Verify</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">About Me</label>
                        <textarea class="form-control" name="about_me" rows="2"><?php echo pf($personal['about_me'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Profile Picture</label>
                        <?php if (!empty($personal['profile_picture'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo pf($personal['profile_picture']); ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                <span class="ms-2 text-muted">Current picture</span>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary">Save Personal</button>
                </div>
            </form>
        </div>

        <!-- Education -->
        <div class="section-card shadow-sm p-4 mb-4">
            <h5 class="section-title">🎓 Education</h5>
            <form method="POST" action="index.php?action=private_profile">
                <input type="hidden" name="section" value="education">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Level</label>
                        <input class="form-control" name="education_level" value="<?php echo pf($education['education_level'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">School/University</label>
                        <input class="form-control" name="school_name" value="<?php echo pf($education['school_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Certificate Name</label>
                        <input class="form-control" name="certificate_name" value="<?php echo pf($education['certificate_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Language of Study</label>
                        <input class="form-control" name="language_of_study" value="<?php echo pf($education['language_of_study'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Graduation Year</label>
                        <input class="form-control" name="graduation_year" value="<?php echo pf($education['graduation_year'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Grade/GPA</label>
                        <input class="form-control" name="grade" value="<?php echo pf($education['grade'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary">Save Education</button>
                </div>
            </form>
        </div>

        <!-- Experience -->
        <div class="section-card shadow-sm p-4 mb-4">
            <h5 class="section-title">💼 Work Experience</h5>
            <form method="POST" action="index.php?action=private_profile">
                <input type="hidden" name="section" value="experience">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Years of Experience</label>
                        <input class="form-control" name="years_of_experience" value="<?php echo pf($experience['years_of_experience'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Job Title</label>
                        <input class="form-control" name="job_title" value="<?php echo pf($experience['job_title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Company</label>
                        <input class="form-control" name="company_name" value="<?php echo pf($experience['company_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Job Category</label>
                        <input class="form-control" name="job_category" value="<?php echo pf($experience['job_category'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Experience Type</label>
                        <input class="form-control" name="experience_type" value="<?php echo pf($experience['experience_type'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="month" class="form-control" name="start_date" value="<?php echo pf($experience['start_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="month" class="form-control" name="end_date" value="<?php echo pf($experience['end_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="working" <?php echo !empty($experience['working'])?'checked':''; ?>>
                            <label class="form-check-label">Currently working</label>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary">Save Experience</button>
                </div>
            </form>
        </div>

        <!-- Skills & Languages -->
        <div class="section-card shadow-sm p-4 mb-4">
            <h5 class="section-title">🧰 Skills & Languages</h5>
            <form method="POST" action="index.php?action=private_profile">
                <input type="hidden" name="section" value="skills">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Skills (comma separated)</label>
                        <input class="form-control" name="skills[]" value="<?php echo pf(implode(', ', array_column($skills, 'skill_name'))); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Categories (comma separated)</label>
                        <input class="form-control" name="job_categories[]" value="<?php echo pf(implode(', ', array_column($jobCats, 'category_name'))); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Languages (name:proficiency, comma separated)</label>
                        <input class="form-control" name="languages[]" value="<?php
                            $pairs = array_map(fn($l)=> ($l['language_name']??'').':'.($l['proficiency']??''), $languages);
                            echo pf(implode(', ', $pairs));
                        ?>">
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary">Save Skills</button>
                </div>
            </form>
        </div>

        <!-- Career -->
        <div class="section-card shadow-sm p-4 mb-4">
            <h5 class="section-title">🎯 Career Interest</h5>
            <form method="POST" action="index.php?action=private_profile">
                <input type="hidden" name="section" value="career">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Career Level</label>
                        <input class="form-control" name="career_level" value="<?php echo pf($career['career_level'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Job Type</label>
                        <input class="form-control" name="job_type" value="<?php echo pf($career['job_type'] ?? ''); ?>">
                    </div>
                    <!-- Removed Minimum Salary as requested -->
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary">Save Career</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/profile.js"></script>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
