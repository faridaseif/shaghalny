<?php
// app/controllers/SupportController.php
require_once APP_ROOT . '/app/models/Report.php';
require_once APP_ROOT . '/app/models/SupportMessage.php';
require_once APP_ROOT . '/config/database.php';

class SupportController
{
    // Main Support Page
    public function index()
    {
        // Support Center is public, but we might want to show user-specific data
        $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        
        $userReports = [];
        if ($currentUserId) {
            $userReports = Report::getForUser($currentUserId, 5);
        }

        // Just load the view
        require_once APP_ROOT . '/app/views/support/index.php';
    }

    // Dashboard Page
    public function dashboard()
    {
        // Enforce login
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_ROOT_URL . '/login'); // Adjust based on how we define root url, or use relative
            // better:
             $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
             $baseUrl = preg_replace('#/app/.*#', '', $scriptDir);
             $baseUrl = rtrim($baseUrl, '/');
             header("Location: $baseUrl/login");
             exit;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $currentUserName = $_SESSION['username'] ?? 'User'; // Assuming username is in session
        
        // Fetch User Reports
        $userReports = Report::getForUser($currentUserId, 10); // Get last 10 reports

        require_once APP_ROOT . '/app/views/support/dashboard.php';
    }

    // Safety Tips Page
    public function safetyTips()
    {
        require_once APP_ROOT . '/app/views/support/safety_tips.php';
    }

    // AJAX: Create a new report
    public function createReport()
    {
        // Clear any previous output (warnings, notices, whitespace)
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You must be logged in to file a report']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $userId = (int)$_SESSION['user_id'];
        $reportType = trim($input['report_type'] ?? '');
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $priority = trim($input['priority'] ?? 'medium');
        $postId = isset($input['post_id']) ? (int)$input['post_id'] : null;

        if (empty($reportType) || empty($title) || empty($description)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit;
        }

        try {
            $reportId = Report::create($userId, $reportType, $title, $description, $priority, $postId);
            echo json_encode(['success' => true, 'report_id' => $reportId, 'message' => 'Report submitted successfully']);
        } catch (Throwable $e) {
            http_response_code(500);
            error_log("Report Submission Error: " . $e->getMessage()); 
            echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    // AJAX: Create support message
    public function createMessage()
    {
        // Clear any previous output
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');
        
        // Guest fields
        $guestName = trim($input['guest_name'] ?? '');
        $guestEmail = trim($input['guest_email'] ?? '');

        if (empty($subject) || empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Subject and message are required']);
            exit;
        }
        
        // If guest, require name and email
        if (!$userId) {
            if (empty($guestName) || empty($guestEmail)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name and email are required for guest support']);
                exit;
            }
            // Basic email validation
            if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Please provide a valid email address']);
                exit;
            }
        } else {
            // Reset guest fields if logged in (just in case)
            $guestName = null;
            $guestEmail = null;
        }

        try {
            $messageId = SupportMessage::create($userId, $subject, $message, $guestName, $guestEmail);
            echo json_encode(['success' => true, 'message_id' => $messageId, 'message' => 'Your message has been sent. We will respond soon.']);
        } catch (Throwable $e) {
            http_response_code(500);
            error_log("Message Submission Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    // Get reports for admin/admin dashboard
    public function getReports()
    {
        // Admin check should happen here or via middleware
        // For now, simpler implementation
        // ...
    }
}
?>
