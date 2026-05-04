<?php



class AdminController
{
    private $db;

    public function __construct($db)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $this->db = $db;
        $this->checkAuth();
    }

    private function checkAuth()
    {
        $action = $_GET['action'] ?? 'index';
        // Allow login/auth without session
        if (in_array($action, ['login', 'authenticate'])) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in and is admin
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=login");
            exit;
        }
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['role'] === 'admin') {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = 'admin'; 
                    
                    header("Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=dashboard");
                    exit;
                } else {
                    $this->render('login', ['error' => 'Access Denied. Admins only.', 'pageTitle' => 'Admin Login']);
                }
            } else {
                $this->render('login', ['error' => 'Invalid email or password.', 'pageTitle' => 'Admin Login']);
            }
        }
    }

    private function render($view, $data = [])
    {
        $viewFile = __DIR__ . '/../views/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            extract($data);
            include $viewFile;
        } else {
            echo "Admin view '$view' not found.";
        }
    }

    public function login()
    {
        $this->render('login');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        // Models
        require_once APP_ROOT . '/app/models/User.php';
        require_once APP_ROOT . '/app/models/Job.php';
        require_once APP_ROOT . '/app/models/Application.php';

        $stats = [
            'users' => User::countAll(),
            'users_growth' => User::countNewThisMonth(),
            'jobs' => Job::countAll(),
            'jobs_growth' => Job::countNewThisMonth(),
            'applications' => Application::countAll(),
            'pending_apps' => Application::countPending()
        ];
        
        $this->render('dashboard', [
            'pageTitle' => 'Dashboard',
            'stats' => $stats
        ]);
    }

    public function users()
    {
        require_once APP_ROOT . '/app/models/User.php';
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $users = [];
        try {
            // Using the filtered method (will add to Model momentarily)
            $users = User::getAllUsers($filters);
        } catch (Exception $e) {
            error_log("Error fetching users: " . $e->getMessage());
        }

        $this->render('users', [
            'pageTitle' => 'Manage Users', 
            'users' => $users,
            'filters' => $filters
        ]);
    }

    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users');
            exit;
        }

        $userId = $_POST['user_id'] ?? 0;
        if ($userId) {
            require_once APP_ROOT . '/app/models/User.php';
            $userModel = new User();
            if ($userModel->delete($userId)) {
                header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&success=deleted');
            } else {
                header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&error=failed');
            }
        } else {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&error=invalid_id');
        }
        exit;
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users');
            exit;
        }

        $userId = $_POST['user_id'] ?? 0;
        if ($userId) {
            require_once APP_ROOT . '/app/models/User.php';
            $userModel = new User();
            
            $data = [
                'first_name' => $_POST['first_name'] ?? null,
                'last_name' => $_POST['last_name'] ?? null,
                'email' => $_POST['email'] ?? null,
                'role' => $_POST['role'] ?? null,
                'status' => $_POST['status'] ?? null,
            ];

            // Filter nulls
            $data = array_filter($data, function($v) { return !is_null($v); });

            if ($userModel->update($userId, $data)) {
                header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&success=updated');
            } else {
                header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&error=failed');
            }
        } else {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&error=invalid_id');
        }
        exit;
    }

    public function getUserDetails()
    {
        ob_clean(); // Clear buffer
        header('Content-Type: application/json');
        $userId = $_GET['id'] ?? 0;

        if (!$userId) {
            echo json_encode(['error' => 'User ID required']);
            exit;
        }

        try {
            require_once APP_ROOT . '/app/models/UserProfile.php';
            require_once APP_ROOT . '/config/database.php';

            $pdo = Database::getInstance()->getConnection();
            $profile = new UserProfile($pdo);

            $data = [
                'user' => $profile->getUser($userId),
                'education' => $profile->getEducation($userId),
                'experience' => $profile->getExperiences($userId),
                'skills' => $profile->getSkills($userId),
                'languages' => $profile->getLanguagesWithProf($userId),
                'career' => $profile->getCareerInterest($userId),
                'reviews' => $profile->getReviews($userId),
                'achievements' => $profile->getAchievements($userId),
                'cv' => null
            ];
            
            $stmt = $pdo->prepare("SELECT * FROM user_cv WHERE user_id = ?");
            $stmt->execute([$userId]);
            $data['cv'] = $stmt->fetch(PDO::FETCH_ASSOC);

            ob_clean(); // Clean EVERYTHING before outputting JSON
            echo json_encode($data);

        } catch (Throwable $e) {
            ob_clean();
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function jobs()
    {
        require_once APP_ROOT . '/app/models/Job.php';
        $jobs = [];
        
        // Capture filters (even though getAllJobs implementation in Job.php currently doesn't support them, 
        // we should pass them to view to avoid warnings if view uses them, and eventually implement filtering in Job model)
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '', // View uses 'status'
            'category' => $_GET['category'] ?? '',
            'sort' => $_GET['sort'] ?? ''
        ];

        try {
            // Note: Job::getAllJobs currently doesn't accept filters. 
            // We should ideally update Job::getAllJobs to accept filters like we did for User.
            // For now, fetching all. I will update Job model next.
            $jobs = Job::getAllJobs($filters); 
        } catch (Exception $e) {
            error_log("Error fetching jobs: " . $e->getMessage());
        }

        $this->render('jobs', ['pageTitle' => 'Manage Jobs', 'jobs' => $jobs, 'filters' => $filters]);
    }

    public function searchJobs()
    {
        // Alias to jobs
        $this->jobs();
    }

    // AJAX: Close a job
    public function closeJob()
    {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $jobId = isset($input['job_id']) ? (int)$input['job_id'] : 0;
        
        if (!$jobId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Job ID is required']);
            exit;
        }

        require_once APP_ROOT . '/app/models/Job.php';
        require_once APP_ROOT . '/config/database.php';
        
        try {
            $db = Database::getInstance()->getConnection();
            $jobModel = new Job($db);
            
            if ($jobModel->changeStatus($jobId, 'closed')) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to close job']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function applications()
    {
        require_once APP_ROOT . '/app/models/Application.php';
        $applications = [];
        try {
            $applications = Application::getAllApplications();
        } catch (Exception $e) {
            error_log("Error fetching applications: " . $e->getMessage());
        }

        $this->render('applications', ['pageTitle' => 'Manage Applications', 'applications' => $applications]);
    }

 

    public function reports()
    {
        require_once __DIR__ . '/../models/Report.php';
        require_once __DIR__ . '/../models/SupportMessage.php';

        // Fetch Reports
        try {
            $reports = Report::getForUser(null, 50); // Fetch all reports
        } catch (Throwable $e) {
            $reports = [];
            error_log("Error fetching reports: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }

        // Fetch Messages
        try {
            $messages = SupportMessage::getForUser(null); // Fetch all messages
        } catch (Throwable $e) {
            $messages = [];
            error_log("Error fetching messages: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }

        $this->render('reports', [
            'pageTitle' => 'Safety & Support',
            'reports' => $reports,
            'messages' => $messages
        ]);
    }

    public function settings()
    {
        $this->render('settings', ['pageTitle' => 'Admin Settings']);
    }

    // AJAX: Delete a post
    public function deletePost()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : 0;
        try {
            if (!$postId) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Post ID is required']);
                exit;
            }

            require_once APP_ROOT . '/app/models/Post.php';
            
            require_once APP_ROOT . '/app/models/Post.php';
            
            if (Post::deleteById($postId)) {
                ob_clean();
                echo json_encode(['success' => true]);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Post not found']);
            }
        } catch (Throwable $e) {
            ob_clean();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function community()
    {
        // Fetch recent posts
        require_once APP_ROOT . '/app/models/Post.php';
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        try {
            $posts = Post::getAll(null, $limit, $offset);
        } catch (Exception $e) {
            $posts = [];
            error_log("Error fetching community posts: " . $e->getMessage());
        }

        $this->render('community', [
            'pageTitle' => 'Community Moderation',
            'posts' => $posts,
            'page' => $page
        ]);
    }

    public function post_details()
    {
        require_once APP_ROOT . '/app/models/Post.php';
        
        $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$postId) {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=community');
            exit;
        }

        $post = Post::getById($postId);
        $comments = Post::getComments($postId);

        $this->render('post_details', [
            'pageTitle' => 'Post Details',
            'post' => $post,
            'comments' => $comments
        ]);
    }

    // AJAX: Delete a comment
    public function deleteComment()
    {
        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $commentId = isset($input['comment_id']) ? (int)$input['comment_id'] : 0;
            
            if (!$commentId) {
                echo json_encode(['success' => false, 'error' => 'Comment ID is required']);
                exit;
            }

            require_once APP_ROOT . '/app/models/Post.php';
            
            if (Post::deleteComment($commentId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Comment not found']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Reply to support message
    public function replyToSupportMessage()
    {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $messageId = isset($input['message_id']) ? (int)$input['message_id'] : 0;
            $email = trim($input['email'] ?? '');
            $subject = trim($input['subject'] ?? '');
            $replyBody = trim($input['reply_body'] ?? '');

            if (!$messageId || empty($email) || empty($replyBody)) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                exit;
            }

            $this->ensureReplySchema();

            // 1. Send Email (simulated if local)
            $headers = "From: support@shaghalny.com\r\n";
            $headers .= "Reply-To: support@shaghalny.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            @mail($email, $subject, $replyBody, $headers);

            // 2. Update Database
            global $pdo;
            if (!$pdo) $pdo = Database::getInstance()->getConnection();
            
            $sql = "UPDATE support_messages SET status = 'resolved', admin_reply = :reply, replied_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'reply' => $replyBody, 
                'id' => $messageId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);

        } catch (Throwable $e) {
            error_log("Reply error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to send reply: ' . $e->getMessage()]);
        }
    }

    private function ensureReplySchema()
    {
        // Use PDO explicitly
        require_once APP_ROOT . '/config/database.php';
        $pdo = Database::getInstance()->getConnection();
        if (!$pdo) return;

        try {
            // Check if admin_reply column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM support_messages LIKE 'admin_reply'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE support_messages ADD COLUMN admin_reply TEXT NULL AFTER message");
            }

            // Check if replied_at column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM support_messages LIKE 'replied_at'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE support_messages ADD COLUMN replied_at DATETIME NULL AFTER admin_reply");
            }

            // Fix the status column constraint (convert ENUM to VARCHAR to allow 'resolved')
            $pdo->exec("ALTER TABLE support_messages MODIFY COLUMN status VARCHAR(50) DEFAULT 'open'");

        } catch (Exception $e) {
            // Log warning but don't stop execution, proceed to try update
            error_log("Schema auto-update failed: " . $e->getMessage());
        }
    }

    // AJAX: Delete a report
    public function deleteReport()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            require_once __DIR__ . '/../models/Report.php';
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? 0;

            if (Report::delete($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete report']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Delete a review
    public function deleteReview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             // If not POST, just redirect back
             header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users');
             exit;
        }

        $reviewId = $_POST['review_id'] ?? 0;
        
        if ($reviewId) {
            // Using global PDO or DB for quick delete since no Review model exists
            global $pdo;
            if (!$pdo) {
                $db = Database::getInstance();
                $pdo = $db->getConnection(); // Helper I added? Or just use mysqli
            }
            if (!$pdo) {
                 // Fallback to mysqli
                 $conn = Database::getInstance()->getConnection();
                 $stmt = $conn->prepare("DELETE FROM user_reviews WHERE review_id = ?");
                 $stmt->bind_param("i", $reviewId);
                 $stmt->execute();
            } else {
                 $stmt = $pdo->prepare("DELETE FROM user_reviews WHERE review_id = ?");
                 $stmt->execute([$reviewId]);
            }
            
            // Redirect back to users page (unfortunately we don't know which user we were viewing, so just main list)
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&success=deleted');
        } else {
            header('Location: /shaghalny8/shaghalny/public/index.php?controller=admin&action=users&error=failed');
        }
        exit;
    }

    // AJAX: Delete a support message
    public function deleteSupportMessage()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            require_once __DIR__ . '/../models/SupportMessage.php';
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? 0;

            if (SupportMessage::delete($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete message']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Update report status
    public function updateReportStatus()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            require_once __DIR__ . '/../models/Report.php';
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? 0;
            $status = $input['status'] ?? '';

            if (Report::updateStatus($id, $status)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Resolve support message (Mark as Closed)
    public function resolveSupportMessage()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            require_once __DIR__ . '/../models/SupportMessage.php';
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $input['id'] ?? 0;

            if (SupportMessage::updateStatus($id, 'closed')) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to resolve message']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Get Report Details
    public function getReportDetails()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            require_once __DIR__ . '/../models/Report.php';
            $id = $_GET['id'] ?? 0;
            
            $report = Report::getById($id);
            
            if ($report) {
                echo json_encode(['success' => true, 'report' => $report]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Report not found']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
