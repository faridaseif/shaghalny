<?php
// Authentication Helper Functions
require_once __DIR__ . '/database.php';

function isLoggedIn() {
    // Only require AuthController when needed
    if (!class_exists('AuthController')) {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
    }
    
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    // Validate session from database (This checks the "Remember Me" token)
    if (AuthController::validateSession($db)) {
        // FIX: If validateSession returns true, the user is valid.
        // We trust AuthController to have restored the session variables.
        // Even if it didn't, returning 'true' stops the redirect loop.
        return true; 
    }
    
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /shaghalny/public/index.php?page=login');
        exit;
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'first_name' => $_SESSION['user_first_name'] ?? '',
            'last_name' => $_SESSION['user_last_name'] ?? '',
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? ''
        ];
    }
    return null;
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Delete session from database
    if (isset($_SESSION['session_token'])) {
        try {
            $database = Database::getInstance();
            $db = $database->getConnection();
            $stmt = $db->prepare("DELETE FROM sessions WHERE session_token = ?");
            $stmt->execute([$_SESSION['session_token']]);
        } catch (PDOException $e) {
            // Ignore errors
        }
    }
    
    // Clear cookies
    setcookie('shaghalny_token', '', time() - 3600, '/');
    setcookie('shaghalny_user_id', '', time() - 3600, '/');
    
    // Destroy PHP session
    session_unset();
    session_destroy();
    
    header('Location: /shaghalny/public/index.php?page=landing');
    exit;
}
