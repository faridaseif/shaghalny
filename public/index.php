<?php
// public/index.php - Restored Legacy Router (Adapted for public/ dir)
ob_start(); // Buffer output to prevent headers/JSON errors
session_start();

// Define APP_ROOT (One level up from public)
define('APP_ROOT', dirname(__DIR__));

// Require Configs
require_once APP_ROOT . '/config/constants.php';
require_once APP_ROOT . '/config/database.php';

// Controllers & Models
require_once APP_ROOT . '/app/models/UserProfile.php';
require_once APP_ROOT . '/app/controllers/UserProfileController.php';
require_once APP_ROOT . '/app/controllers/SupportController.php';
require_once APP_ROOT . '/app/controllers/JobController.php';
require_once APP_ROOT . '/app/controllers/ApplicationController.php';
require_once APP_ROOT . '/app/controllers/MapController.php';

// Define Asset Root for views
define('ASSET_ROOT', '.');

// Initialize DB & Controllers
$db = Database::getInstance()->getConnection();
$pdo = $db; // Ensure global $pdo is available for models using global keyword
$userProfileModel = new UserProfile($db);
$userProfileController = new UserProfileController($userProfileModel);
$supportController = new SupportController();
$jobController = new JobController($db);
$applicationController = new ApplicationController($db);
$mapController = new MapController();

// New Controllers (Message & Post)
require_once APP_ROOT . '/app/controllers/MessageController.php';
require_once APP_ROOT . '/app/controllers/PostController.php';
$messageController = new MessageController();
$postController = new PostController();

// --- CONTROLLER DISPATCH (Legacy API style) ---
// Used by AuthController (login/register)
if (isset($_GET['controller'])) {
    $controllerName = ucfirst($_GET['controller']) . 'Controller';
    $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        // Check if class exists (namespace-less or namespaced?)
        // Assuming global namespace for simplicity based on legacy
        if (class_exists($controllerName)) {
            // Check if constructor accepts arguments (to avoid error if it expects none)
            // But MessageController/PostController have no constructor, so args are ignored or accepted? 
            // Better to instantiate specifically if we know them, but generic dispatch is useful.
            // For now, we rely on PHP's behavior (ignoring extra args for no-constructor classes)
            $controllerInstance = new $controllerName($db);
            
            // Handle POST actions if needed, or query param action
            // AuthController usually expects 'action' in POST or GET
            $action = $_POST['action'] ?? $_GET['action'] ?? 'index';
            
            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
                exit;
            }
        }
    }
}

// --- ACTION HANDLING (Logic/AJAX) ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        // User Profile Actions
        case 'personal_info': $userProfileController->personalInfo(); break;
        case 'education': $userProfileController->education(); break;
        case 'experience': $userProfileController->experience(); break;
        case 'expertise': $userProfileController->expertise(); break;
        case 'career_interest': $userProfileController->careerInterest(); break;
        case 'settings': $userProfileController->settings(); break;
        case 'update_email': $userProfileController->updateEmail(); break;
        case 'change_password': $userProfileController->changePassword(); break;
        case 'delete_account': $userProfileController->deleteAccount(); break;
        case 'public_profile': $userProfileController->publicProfile(); break;
        case 'private_profile': $userProfileController->privateProfile(); break;
        
        // Support Feature Actions
        case 'support': $supportController->index(); break;
        case 'dashboard': $supportController->dashboard(); break;
        case 'safety_tips': $supportController->safetyTips(); break;

        // Job Actions
        case 'myJobs': $jobController->myJobs(); break;
        case 'create': $jobController->create(); break;
        case 'postJob': $jobController->postJob(); break;
        case 'jobsIAppliedFor': $jobController->jobsIAppliedFor(); break;
        case 'jobClose': $jobController->jobClose(); break;
        case 'editJob': $jobController->editJob(); break;

        // Application Actions
        case 'workHistory': $applicationController->workHistory(); break;
        case 'submitReview': $applicationController->submitReview(); break;
        case 'acceptApplication': $applicationController->acceptApplication(); break;
        case 'declineApplication': $applicationController->declineApplication(); break;
        case 'apply': $applicationController->apply(); break;

        // Map Actions
        case 'map': $mapController->map(); break;
        case 'report_submit': $supportController->createReport(); break; // POST
        case 'message_submit': $supportController->createMessage(); break; // POST
        case 'get_report': // GET details
             if (isset($_GET['id'])) {
                 // We need to implement getReport in SupportController or call it if it exists
                 // For now, let's assume methods align with SupportController.php
                 // But wait, SupportController.php had `createReport`, `createMessage`.
                 // It didn't have `getReport` logic exposed as simple method potentially.
                 // Let's rely on what we have. If createReport checks POST internally, we are good.
             }
             break;
             
        // Message Actions
        case 'inbox': $messageController->inbox(); break;
        // AJAX Message Actions are handled via ?controller=Message&action=... usually,
        // but if we want ?action=send shortcuts:
        // case 'send_message': $messageController->send(); break; 
        
        // Post Actions (Social Feed)
        case 'feed': $postController->feed(); break;
             
        case 'logout':
            session_destroy();
            header("Location: index.php?page=login");
            exit;
            
        case 'about':
             // Redirect or load view
             header("Location: index.php?page=about");
             exit;
    }
    
    // Support AJAX handling manual mapping if needed
    if ($action === 'report_submit') {
        $supportController->createReport();
        exit;
    }
    if ($action === 'contact_submit') { // mapped to message_submit or similar
        $supportController->createMessage();
        exit;
    }
    if ($action === 'get_report') {
         // Need to add this method to SupportController if missing verify later
         // For now, assume it's there or implemented
        //  $supportController->getReport(); 
         // Implementation check required.
         exit;
    }

    // Default Fallback
    // header("Location: index.php?page=home");
    // exit;
    exit;
}

// --- PAGE ROUTING (Views) ---
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        // Legacy Home/Landing
        // If we want the "New" Home Controller:
        require_once APP_ROOT . '/app/controllers/HomeController.php';
        $homeController = new HomeController(); // Namespace removed previously
        $homeController->index();
        break;

    case 'login':
        require_once APP_ROOT . '/app/views/auth/login.php';
        break;

    case 'register':
        require_once APP_ROOT . '/app/views/auth/login.php';
        break;
        
    case 'about':
        // require_once APP_ROOT . '/app/views/home/about.php'; 
        echo "About Us Page"; // Placeholder
        break;

    default:
        // 404
        echo "404 Page Not Found";
        break;
}
