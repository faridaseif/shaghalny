<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utilities/Controller.php';
require_once __DIR__ . '/../../utilities/Validator.php';
require_once __DIR__ . '/../models/User.php';

use App\Utilities\Controller;
use App\Utilities\Validator;

// Clear buffers to ensure clean JSON
if (ob_get_level()) ob_end_clean();

/**
 * AuthController
 * 
 * Handles user authentication (Login, Register, Logout).
 */
class AuthController extends Controller {
    private $db;
    private $userModel;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->userModel = new User();
    }

    /**
     * Handle incoming requests based on 'action' parameter.
     */
    public function handleRequest() {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'register':
                $this->register();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'reset_request':
                $this->handleResetRequest();
                break;
            default:
                $this->jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
                break;
        }
    }

    /**
     * Process User Login.
     */
    private function login() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $validator = new Validator();
        if (!$validator->validateRequired(['email' => $email, 'password' => $password], ['email', 'password'])) {
            $this->jsonResponse(['success' => false, 'message' => $validator->getFirstError()], 400);
        }

        try {
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === 'true';
                $sessionData = $this->createSession((int)$user['user_id'], $rememberMe);
                
                if ($sessionData) {
                    $this->startSessionIfNeeded();
                    $_SESSION['user_first_name'] = $user['first_name'];
                    $_SESSION['user_last_name'] = $user['last_name'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = $user['role'] ?? 'user';

                    // Check Profile Completion
                    $stmt = $this->db->prepare("SELECT EXISTS (SELECT 1 FROM user_personal_info WHERE user_id = ?) AS completed");
                    $stmt->execute([(int)$user['user_id']]);
                    $profileComplete = (bool) $stmt->fetchColumn();
                    $_SESSION['profile_complete'] = $profileComplete;

                    // Determine Redirect
                    $redirect = 'index.php?page=home';
                    if (isset($user['role']) && $user['role'] === 'admin') {
                        $redirect = 'index.php?controller=admin&action=index';
                    }
                    
                    session_write_close();
                    
                    $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Login successful',
                        'redirect' => $redirect,
                        'user' => [
                            'user_id' => $user['user_id'],
                            'name' => $user['first_name'] . ' ' . $user['last_name'],
                            'email' => $user['email']
                        ]
                    ]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Failed to create session'], 500);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid email or password'], 401);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage()); 
            $this->jsonResponse(['success' => false, 'message' => 'Login Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Process User Registration.
     */
    private function register() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? null;
        
        $firstName = '';
        $lastName = '';
        
        if (isset($_POST['first_name']) && isset($_POST['last_name'])) {
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
        } elseif (isset($_POST['name'])) {
            $nameParts = explode(' ', trim($_POST['name']), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        }

        // Validation
        $validator = new Validator();
        if (!$validator->validateRequired(['email' => $email, 'password' => $password, 'first_name' => $firstName], ['email', 'password', 'first_name'])) {
            $this->jsonResponse(['success' => false, 'message' => $validator->getFirstError()], 400);
        }

        try {
            if ($this->userModel->findByEmail($email)) {
                $this->jsonResponse(['success' => false, 'message' => 'Email already registered'], 409);
            }

            $userId = $this->userModel->create($firstName, $lastName, $email, $phone, $password);

            if (!$userId) {
                $this->jsonResponse(['success' => false, 'message' => 'Registration failed.'], 500);
            }

            // Create default public profile
            $stmt = $this->db->prepare("INSERT INTO user_public_info (user_id, average_rating, total_reviews, recent_activity, jobs_completed) VALUES (?, 0, 0, 0, 0)");
            $stmt->execute([$userId]);

            $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === 'true';
            $sessionData = $this->createSession($userId, $rememberMe);
            
            if ($sessionData) {
                $this->startSessionIfNeeded();
                $_SESSION['user_first_name'] = $firstName;
                $_SESSION['user_last_name'] = $lastName;
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                $_SESSION['user_email'] = $email;
                $_SESSION['role'] = 'user';
                $_SESSION['profile_complete'] = false;
                
                session_write_close();
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Registration successful',
                    'redirect' => 'index.php?action=personal_info',
                    'user' => [
                        'user_id' => $userId,
                        'name' => $firstName . ' ' . $lastName,
                        'email' => $email
                    ]
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Registration successful but failed to create session'], 500);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage()); 
            $this->jsonResponse(['success' => false, 'message' => 'Registration Error: ' . $e->getMessage()], 500);
        }
    }

    public function logout() {
        // 1. Clear DB Session if exists
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? $_COOKIE['shaghalny_user_id'] ?? null;
        $token = $_COOKIE['shaghalny_token'] ?? null;

        if ($userId && $token) {
            try {
                $stmt = $this->db->prepare("DELETE FROM sessions WHERE user_id = ? AND session_token = ?");
                $stmt->execute([$userId, $token]);
            } catch (PDOException $e) {}
        }

        // 2. Destroy PHP Session
        $_SESSION = [];
        session_destroy();

        // 3. Clear Cookies
        if (isset($_COOKIE['shaghalny_token'])) {
            setcookie('shaghalny_token', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['shaghalny_user_id'])) {
            setcookie('shaghalny_user_id', '', time() - 3600, '/');
        }

        // $this->jsonResponse(['success' => true, 'message' => 'Logged out successfully']);
        header('Location: index.php?page=home');
        exit;
    }

    private function handleResetRequest() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (empty($email)) {
             $this->jsonResponse(['success' => false, 'message' => 'Email is required'], 400);
        }

        // 1. Check if user actually exists
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if (!$stmt->fetch()) {
            // SECURITY: Return success even if email not found
            $this->jsonResponse(['success' => true, 'message' => 'Reset link sent if email exists']);
        }

        // 2. Generate a secure random token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // Expires in 1 hour

        try {
            // 3. Save token to database
            $this->db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $stmt = $this->db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expiresAt]);

            // 4. "Send" the Email (Simulation)
            $link = "http://" . $_SERVER['HTTP_HOST'] . "/shaghalny/public/index.php?page=new-password&token=" . $token;
            error_log(" [RESET PASSWORD] Link for $email: " . $link);

            $this->jsonResponse(['success' => true, 'message' => 'Reset link sent']);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Database error'], 500);
        }
    }

    private function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }
    
    private function startSessionIfNeeded() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function createSession($userId, $rememberMe) {
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        $token = bin2hex(random_bytes(32));
        $expiresIn = $rememberMe ? '+30 days' : '+1 day';
        $expiresAt = date('Y-m-d H:i:s', strtotime($expiresIn));
        
        $stmt = $db->prepare("INSERT INTO sessions (user_id, session_token, expires_at) VALUES (:uid, :token, :expiry)");
        $stmt->execute([':uid' => $userId, ':token' => $token, ':expiry' => $expiresAt]);
        
        $cookieTime = time() + ($rememberMe ? 2592000 : 86400);
        setcookie('shaghalny_token', $token, $cookieTime, "/");
        setcookie('shaghalny_user_id', $userId, $cookieTime, "/");
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['session_token'] = $token;
    
        return ['success' => true, 'token' => $token];
    }
    
    private function cleanupExpiredSessions() {
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (PDOException $e) {}
    }
    
    public static function validateSession($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        
        $cookieToken = $_COOKIE['shaghalny_token'] ?? null;
        $cookieUserId = $_COOKIE['shaghalny_user_id'] ?? null;
        
        if (!$cookieToken || !$cookieUserId) {
            return false;
        }
        
        try {
            if (!$db) {
                return false; 
            }
    
            $query = "SELECT u.* FROM users u 
                      JOIN sessions s ON u.id = s.user_id 
                      WHERE s.session_token = :token 
                      AND u.id = :user_id 
                      AND s.expires_at > NOW()";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':token' => $cookieToken, 
                ':user_id' => $cookieUserId
            ]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['session_token'] = $cookieToken;
                
                return true;
            }
        } catch (PDOException $e) {
            error_log("Session Validation Error: " . $e->getMessage());
            return false;
        }
    
        return false;
    }
}

if (isset($_GET['controller']) && $_GET['controller'] === 'auth') {
    $controller = new AuthController();
    $controller->handleRequest();
}
?>
