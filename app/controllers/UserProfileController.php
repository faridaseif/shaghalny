<?php

class UserProfileController
{
    private UserProfile $model;

    public function __construct(UserProfile $model)
    {
        $this->model = $model;
    }

    private function requireLogin(): int
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }
        return (int)$_SESSION['user_id'];
    }
//finds the correct view file, passes data to it, shows it in the browser
    private function render(string $view, array $data = [], array $errors = [], bool $success = false): void
    {
        $viewFile = __DIR__ . '/../views/profile/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View not found";
            return;
        }
        $viewData = $data;
        $viewErrors = $errors;
        $viewSuccess = $success;
        include $viewFile;
    }

    /* ------------ Personal Info ------------ */
    public function personalInfo(): void
    {
        $userId = $this->requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first = trim($_POST['first-name'] ?? '');
            $last = trim($_POST['last-name'] ?? '');
            $birthdate = $_POST['birthdate'] ?? $this->buildDate($_POST['year'] ?? '', $_POST['month'] ?? '', $_POST['day'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $nationality = $_POST['nationality'] ?? '';
            $country = $_POST['country'] ?? '';
            $city = $_POST['city'] ?? '';
            $area = $_POST['area'] ?? '';
            $mobile = trim($_POST['mobile'] ?? '');
            $about = trim($_POST['about_me'] ?? '');

            $required = [
                'First name' => $first,
                'Last name' => $last,
                'Birthdate' => $birthdate,
                'Gender' => $gender,
                'Nationality' => $nationality,
                'Country' => $country,
                'City' => $city,
                'Area' => $area,
                'Mobile' => $mobile
            ];
            foreach ($required as $label => $value) {
                if ($value === '' || $value === null) {
                    $errors[] = "$label is required";
                }
            }

            if (!$errors) {
                $this->model->updateUserCore($userId, $first, $last, $mobile);
                $this->model->savePersonalInfo($userId, [
                    'birthdate' => $birthdate,
                    'gender' => $gender,
                    'nationality' => $nationality,
                    'country' => $country,
                    'city' => $city,
                    'area' => $area,
                    'about_me' => $about
                ]);
                $data = array_merge(
                    $this->model->getPersonalInfo($userId),
                    ['first_name' => $first, 'last_name' => $last, 'mobile' => $mobile]
                );
                $this->render('personal_info', $data, [], true);
                return;
            }

            $data = array_merge(
                $this->model->getPersonalInfo($userId),
                ['first_name' => $first, 'last_name' => $last, 'mobile' => $mobile]
            );
            $this->render('personal_info', $data, $errors);
            return;
        }

        $user = $this->model->getUser($userId);
        $data = $this->model->getPersonalInfo($userId);
        if ($user) {
            $data['first_name'] = $user['first_name'] ?? '';
            $data['last_name'] = $user['last_name'] ?? '';
            $data['mobile'] = $user['phone'] ?? '';
        }
        $this->render('personal_info', $data);
    }

    /* ------------ Education ------------ */
    public function education(): void
    {
        $userId = $this->requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $level = $_POST['edu-level'] ?? '';
            $school = '';
            $language = '';
            $gradYear = '';
            $grade = '';

            if ($level === 'Highschool') {
                $school = trim($_POST['school-name'] ?? '');
                $language = $_POST['language'] ?? '';
                $gradYear = $_POST['grad-year'] ?? '';
                $grade = trim($_POST['school-grade'] ?? '');
                $required = [$level, $school, $language, $gradYear, $grade];
            } else {
                $school = trim($_POST['university'] ?? '');
                $language = $_POST['field-study'] ?? '';
                $gradYear = $_POST['degree-year'] ?? '';
                $grade = trim($_POST['degree-grade'] ?? '');
                $required = [$level, $school, $language, $gradYear, $grade];
            }

            if (in_array('', $required, true)) {
                $errors[] = 'All required fields must be filled.';
            }

            if (!$errors) {
                $this->model->saveEducation($userId, [
                    'education_level' => $level,
                    'school_name' => $school,
                    'language_of_study' => $language,
                    'graduation_year' => $gradYear,
                    'grade' => $grade
                ]);
                $data = $this->model->getEducation($userId);
                $this->render('education', $data, [], true);
                return;
            }
            $data = $this->model->getEducation($userId);
            $this->render('education', $data, $errors);
            return;
        }

        $data = $this->model->getEducation($userId);
        $this->render('education', $data);
    }

    /* ------------ Experience ------------ */
    public function experience(): void
    {
        $userId = $this->requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $years = $_POST['years-experience'] ?? '';
            $working = !empty($_POST['working']) ? 1 : 0;

            $experiences = [];
            if ($years && $years !== 'No') {
                $jobTitle = trim($_POST['job-title'] ?? '');
                $company = trim($_POST['company'] ?? '');
                $jobCategory = $_POST['job-category'] ?? '';
                $experienceType = $_POST['experience-type'] ?? '';
                $start = $_POST['start-date'] ?? '';
                $end = $_POST['end-date'] ?? '';

                // Fix for YYYY-MM format from type="month" input
                if ($start && strlen($start) === 7) $start .= '-01';
                if ($end && strlen($end) === 7) $end .= '-01';

                $required = [$jobTitle, $company, $jobCategory, $experienceType, $start, $end];
                if (in_array('', $required, true)) {
                    $errors[] = 'Please complete all experience details.';
                }

                if ($start && $end && $start > $end) {
                    $errors[] = 'Start date cannot be after end date.';
                }

                if (!$errors) {
                    $experiences[] = [
                        'years_experience' => $years,
                        'job_title' => $jobTitle,
                        'company' => $company,
                        'job_category' => $jobCategory,
                        'experience_type' => $experienceType,
                        'start_date' => $start,
                        'end_date' => $end,
                        'working' => $working
                    ];
                }
            }

            if (!$errors) {
                $this->model->replaceExperiences($userId, $experiences);
                $experiences = $this->model->getExperiences($userId);
                foreach ($experiences as &$exp) {
                    if (!empty($exp['start_date'])) $exp['start_date'] = substr($exp['start_date'], 0, 7);
                    if (!empty($exp['end_date'])) $exp['end_date'] = substr($exp['end_date'], 0, 7);
                }
                $data = ['experiences' => $experiences];
                $this->render('experience', $data, [], true);
                return;
            }
        }

        $experiences = $this->model->getExperiences($userId);
        // Format dates for YYYY-MM input
        foreach ($experiences as &$exp) {
            if (!empty($exp['start_date'])) $exp['start_date'] = substr($exp['start_date'], 0, 7);
            if (!empty($exp['end_date'])) $exp['end_date'] = substr($exp['end_date'], 0, 7);
        }
        $data = ['experiences' => $experiences];
        $this->render('experience', $data, $errors);
    }

    /* ------------ Expertise ------------ */
    public function expertise(): void
    {
        $userId = $this->requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $languages = $_POST['languages'] ?? [];
            $proficiency = $_POST['proficiency'] ?? [];
            $skills = $_POST['skills'] ?? [];
            $cvPath = null;

            if (!empty($_FILES['cvFile']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $safeName = $userId . '-' . basename($_FILES['cvFile']['name']);
                $target = $uploadDir . $safeName;
                if (move_uploaded_file($_FILES['cvFile']['tmp_name'], $target)) {
                    $cvPath = 'public/uploads/' . $safeName;
                    $this->model->saveCv($userId, $cvPath);
                } else {
                    $errors[] = 'Could not upload CV.';
                }
            }

            if (!$errors) {
                $this->model->replaceLanguages($userId, $languages, $proficiency);
                $this->model->replaceSkills($userId, $skills);
                $data = [];
                $this->render('expertise', $data, [], true);
                return;
            }
        }

        $data = [];
        $this->render('expertise', $data, $errors);
    }

    /* ------------ Career Interest ------------ */
    public function careerInterest(): void
    {
        $userId = $this->requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $careerLevel = $_POST['career_level'] ?? '';
            $jobType = $_POST['job_type'] ?? '';
            $workplace = $_POST['workplace'] ?? '';
            $categories = $_POST['job_categories'] ?? [];
            $minSalary = $_POST['min_salary'] ?? '';
            $publicProfile = !empty($_POST['public_profile']) ? 1 : 0;
            $push = !empty($_POST['push_notifications']) ? 1 : 0;

            if (!$careerLevel || !$jobType) {
                $errors[] = 'Career level and job type are required.';
            }

            if (!$errors) {
                $this->model->saveCareerInterest($userId, [
                    'career_level' => $careerLevel,
                    'job_type' => $jobType,
                    'expected_salary' => $minSalary,
                    'workplace' => $workplace,
                    'public_profile' => $publicProfile,
                    'push_notifications' => $push
                ]);
                $_SESSION['public_profile'] = $publicProfile;
                $_SESSION['push_notifications'] = $push;

                $data = $this->model->getCareerInterest($userId);
                $this->render('career_interest', $data, [], true);
                return;
            }
        }

        $data = $this->model->getCareerInterest($userId);
        $this->render('career_interest', $data, $errors);
    }

    /* ------------ Landing ------------ */
    public function landing(): void
    {
        $userId = $this->requireLogin();
        $data = ['user' => $this->model->getUser($userId)];
        $this->render('success', $data);
    }

    /* ------------ Settings ------------ */
    public function settings(): void
    {
        $userId = $this->requireLogin();
        $user = $this->model->getUser($userId);
        $career = $this->model->getCareerInterest($userId);
        
        $data = [
            'user' => $user,
            'career' => $career
        ];
        
        $success = isset($_GET['success']) && $_GET['success'] == '1';
        $this->render('settings', $data, [], $success);
    }

    public function updateEmail(): void
    {
        $userId = $this->requireLogin();
        $email = trim($_POST['email'] ?? '');
        
        $user = $this->model->getUser($userId);
        $career = $this->model->getCareerInterest($userId);
        $data = ['user' => $user, 'career' => $career];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
             $this->render('settings', $data, ['Invalid email format']); 
             return;
        }
        
        try {
            // Check if email is same as current (optimization)
            if (isset($user['email']) && $email === $user['email']) {
                 header('Location: index.php?action=settings'); // No change
                 exit;
            }

            if ($this->model->updateEmail($userId, $email)) {
                $_SESSION['user_email'] = $email; // Update session
                header('Location: index.php?action=settings&success=1#general');
                exit;
            } else {
                 $this->render('settings', $data, ['Database update failed']);
            }

        } catch (PDOException $e) {
            // 23000 is SQLSTATE for integrity violation (duplicate entry)
            if ($e->getCode() == '23000' || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                 $this->render('settings', $data, ['This email is already in use by another account.']);
            } else {
                 $this->render('settings', $data, ['Database error: ' . $e->getMessage()]);
            }
        }
    }

    public function changePassword(): void
    {
        $userId = $this->requireLogin();
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = $this->model->getUser($userId);
        
        $errors = [];
        if (!password_verify($current, $user['password'])) {
            $errors[] = "Incorrect current password";
        }
        if (strlen($new) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        if ($new !== $confirm) {
            $errors[] = "New passwords do not match";
        }

        if ($errors) {
            $career = $this->model->getCareerInterest($userId);
            $this->render('settings', ['user' => $user, 'career' => $career], $errors);
            return;
        }

        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $this->model->updatePassword($userId, $hashed);
        
        header('Location: index.php?action=settings&success=1#security');
        exit;
    }

    public function deleteAccount(): void
    {
        $userId = $this->requireLogin();
        // The confirmation is handled by frontend modal. If we are here, user confirmed.
        $this->model->deleteUser($userId);
        
        // Logout and redirect
        session_destroy();
        header('Location: index.php'); // Redirect to Landing (Home)
        exit;
    }



    /* ------------ Public Profile ------------ */
    public function publicProfile(): void
    {
        $userId = isset($_GET['uid']) ? (int)$_GET['uid'] : null;
        if (!$userId) {
            // fallback to logged-in user
            if (empty($_SESSION['user_id'])) {
                header('Location: /login.php');
                exit;
            }
            $userId = (int)$_SESSION['user_id'];
        }

        $user = $this->model->getUser($userId);
        if (!$user) {
            http_response_code(404);
            echo "User not found";
            return;
        }

        $publicInfo = $this->model->getUserPublicInfo($userId);
        $achievements = $this->model->getAchievements($userId);
        $skills = $this->model->getSkills($userId);
        $languages = $this->model->getLanguagesWithProf($userId);
        $jobCats = $this->model->getJobCategories($userId);
        $personal = $this->model->getPersonalInfo($userId);
        $education = $this->model->getEducation($userId);
        $experience = $this->model->getExperiences($userId);
        $activities = $this->model->getRecentActivities($userId, 3);
        $reviews = $this->model->getReviews($userId, 3);
        $career = $this->model->getCareerInterest($userId);

        $data = compact(
            'user',
            'publicInfo',
            'achievements',
            'skills',
            'languages',
            'jobCats',
            'personal',
            'education',
            'experience',
            'activities',
            'reviews',
            'career'
        );
        $this->render('public_profile', $data);
    }

    /* ------------ Private Profile (edit) ------------ */
    public function privateProfile(): void
    {
        $userId = $this->requireLogin();
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $section = $_POST['section'] ?? '';

            if ($section === 'personal') {
                $first = trim($_POST['first_name'] ?? '');
                $last = trim($_POST['last_name'] ?? '');
                $birthdate = $_POST['birthdate'] ?? null;
                $gender = $_POST['gender'] ?? '';
                $nationality = $_POST['nationality'] ?? '';
                $country = $_POST['country'] ?? '';
                $city = $_POST['city'] ?? '';
                $area = $_POST['area'] ?? '';
                $mobile = trim($_POST['phone'] ?? '');
                $about = trim($_POST['about_me'] ?? '');
                $picPath = null;

                // Handle Profile Picture Upload
                if (!empty($_FILES['profile_picture']['name'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($ext, $allowed)) {
                        $safeName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                        $target = $uploadDir . $safeName;
                        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
                            $picPath = 'uploads/avatars/' . $safeName;
                        } else {
                            $errors[] = 'Failed to upload image';
                        }
                    } else {
                        $errors[] = 'Invalid image format (allowed: jpg, jpeg, png)';
                    }
                }

                foreach (['first','last','birthdate','gender','nationality','country','city','area','mobile'] as $fld) {
                    if (empty($$fld)) {
                        $errors[] = ucfirst($fld) . ' is required';
                    }
                }

                if (!$errors) {
                    $this->model->updateUserCore($userId, $first, $last, $mobile);
                    
                    // Prepare data for personal info with ucfirst Gender to match DB/View expectation
                    $pData = [
                        'birthdate' => $birthdate,
                        'gender' => ucfirst(strtolower($gender)), 
                        'nationality' => $nationality,
                        'country' => $country,
                        'city' => $city,
                        'area' => $area,
                        'about_me' => $about
                    ];
                    if ($picPath) {
                        $pData['profile_picture'] = $picPath;
                    }

                    $this->model->savePersonalInfo($userId, $pData);
                    $success = true;
                }
            } elseif ($section === 'education') {
                $level = $_POST['education_level'] ?? '';
                $school = trim($_POST['school_name'] ?? '');
                $langStudy = $_POST['language_of_study'] ?? '';
                $grad = $_POST['graduation_year'] ?? '';
                $grade = trim($_POST['grade'] ?? '');
                if (in_array('', [$level, $school, $langStudy, $grad, $grade], true)) {
                    $errors[] = 'All education fields are required';
                }
                if (!$errors) {
                    $this->model->saveEducation($userId, [
                        'education_level' => $level,
                        'school_name' => $school,
                        'language_of_study' => $langStudy,
                        'graduation_year' => $grad,
                        'grade' => $grade
                    ]);
                    $success = true;
                }
            } elseif ($section === 'experience') {
                $years = $_POST['years_of_experience'] ?? '';
                $jobTitle = trim($_POST['job_title'] ?? '');
                $company = trim($_POST['company_name'] ?? '');
                $jobCategory = $_POST['job_category'] ?? '';
                $experienceType = $_POST['experience_type'] ?? '';
                $start = $_POST['start_date'] ?? '';
                $end = $_POST['end_date'] ?? '';
                $working = !empty($_POST['working']) ? 1 : 0;

                if ($years && $years !== 'No' && in_array('', [$jobTitle, $company, $jobCategory, $experienceType, $start, $end], true)) {
                    $errors[] = 'Complete all experience fields';
                }
                if ($start && $end && $start > $end) {
                    $errors[] = 'Start date cannot be after end date';
                }
                if (!$errors) {
                    $this->model->replaceExperiences($userId, [[
                        'years_experience' => $years,
                        'job_title' => $jobTitle,
                        'company' => $company,
                        'job_category' => $jobCategory,
                        'experience_type' => $experienceType,
                        'start_date' => $start,
                        'end_date' => $end,
                        'working' => $working
                    ]]);
                    $success = true;
                }
            } elseif ($section === 'skills') {
                $langRaw = $_POST['languages'][0] ?? '';
                $skillRaw = $_POST['skills'][0] ?? '';
                $catsRaw = $_POST['job_categories'][0] ?? '';

                $languages = [];
                $proficiency = [];
                if ($langRaw) {
                    $pairs = array_map('trim', explode(',', $langRaw));
                    foreach ($pairs as $p) {
                        if (!$p) continue;
                        [$lname, $lprof] = array_pad(explode(':', $p, 2), 2, '');
                        $languages[] = trim($lname);
                        $proficiency[] = trim($lprof);
                    }
                }

                // Fix parsing: handle array correctly if multiple inputs or comma string
                 // Logic: explode by comma, trim, filter empty
                $skills = $skillRaw ? array_filter(array_map('trim', explode(',', $skillRaw))) : [];
                $categories = $catsRaw ? array_filter(array_map('trim', explode(',', $catsRaw))) : [];

                $this->model->replaceLanguages($userId, $languages, $proficiency);
                $this->model->replaceSkills($userId, $skills);
                $this->model->replaceJobCategories($userId, $categories);
                $success = true;
            } elseif ($section === 'career') {
                $careerLevel = $_POST['career_level'] ?? '';
                $jobType = $_POST['job_type'] ?? '';
                $expectedSalary = $_POST['expected_salary'] ?? '';
                $categories = $_POST['job_categories'] ?? [];
                if (!$careerLevel || !$jobType) {
                    $errors[] = 'Career level and job type are required';
                }
                if (!$errors) {
                    $this->model->saveCareerInterest($userId, [
                        'career_level' => $careerLevel,
                        'job_type' => $jobType,
                        'expected_salary' => $expectedSalary
                    ]);
                    $this->model->replaceJobCategories($userId, $categories);
                    $success = true;
                }
            }
        }

        $user = $this->model->getUser($userId);
        $personal = $this->model->getPersonalInfo($userId);
        $education = $this->model->getEducation($userId);
        $experience = $this->model->getExperiences($userId);
        $skills = $this->model->getSkills($userId);
        $languages = $this->model->getLanguagesWithProf($userId);
        $jobCats = $this->model->getJobCategories($userId);
        $career = $this->model->getCareerInterest($userId);
        $publicInfo = $this->model->getUserPublicInfo($userId);

        $data = compact('user','personal','education','experience','skills','languages','jobCats','career','publicInfo');
        $this->render('private_profile', $data, $errors, $success);
    }

    /* ------------ Settings ------------ */


    /* ------------ Helpers ------------ */
    public function getConnection() { return $this->model->getConnection(); } // Helper for above

    private function buildDate($year, $month, $day): ?string
    {
        if (!$year || !$month || !$day) {
            return null;
        }
        $monthNumber = $this->monthToNumber($month);
        if (!$monthNumber || !checkdate((int)$monthNumber, (int)$day, (int)$year)) {
            return null;
        }
        return sprintf('%04d-%02d-%02d', $year, $monthNumber, $day);
    }

    private function monthToNumber(string $month): ?int
    {
        $map = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
            'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];
        return $map[$month] ?? (is_numeric($month) ? (int)$month : null);
    }
}

