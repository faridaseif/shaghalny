<?php
// Application Constants

// Base URL
define('BASE_URL', '/shaghalny');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('MODELS_PATH', APP_PATH . '/models');

// Session
define('SESSION_LIFETIME', 3600); // 1 hour

// Security
define('PASSWORD_MIN_LENGTH', 6);
