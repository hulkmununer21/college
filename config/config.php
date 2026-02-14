<?php
/**
 * Application Configuration File
 * 
 * This file contains all application-level configuration settings
 */

// Application Settings
define('APP_NAME', 'Tertiary School Management System');
define('APP_VERSION', '1.0.0');

// Base URL Configuration
define('BASE_URL', 'https://didactic-capybara-px7pxxx9rxvhrxgj-8000.app.github.dev');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEW_PATH', APP_PATH . '/views');

// Session Configuration
define('SESSION_NAME', 'TSMS_SESSION');
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_SECURE', false); // Set to true in production with HTTPS
define('SESSION_HTTPONLY', true);

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_TIME_NAME', 'csrf_token_time');
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hour

// Default Timezone
date_default_timezone_set('Africa/Lagos');

// Error Reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader
spl_autoload_register(function ($className) {
    $coreFile = CORE_PATH . '/' . $className . '.php';
    $modelFile = APP_PATH . '/models/' . $className . '.php';
    $controllerFile = APP_PATH . '/controllers/' . $className . '.php';
    $helperFile = APP_PATH . '/helpers/' . $className . '.php';

    if (file_exists($coreFile)) {
        require_once $coreFile;
    } elseif (file_exists($modelFile)) {
        require_once $modelFile;
    } elseif (file_exists($controllerFile)) {
        require_once $controllerFile;
    } elseif (file_exists($helperFile)) {
        require_once $helperFile;
    }
});
