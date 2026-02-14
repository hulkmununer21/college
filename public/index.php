<?php
/**
 * Application Entry Point
 * 
 * This is the single entry point for all requests
 * All requests are routed through this file
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

// Load configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Start session with secure settings
session_start([
    'name' => SESSION_NAME,
    'cookie_lifetime' => SESSION_LIFETIME,
    'cookie_httponly' => SESSION_HTTPONLY,
    'cookie_secure' => SESSION_SECURE,
    'use_strict_mode' => true,
    'use_only_cookies' => true
]);

// Check for remember me cookie if not logged in
if (!isset($_SESSION['user_id'])) {
    $sessionHelper = new SessionHelper();
    $sessionHelper->checkRememberMe();
}

// Initialize and run the router
$router = new Router();
