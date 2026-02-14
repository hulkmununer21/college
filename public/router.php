<?php
/**
 * Router Script for PHP Built-in Server
 * 
 * This script mimics Apache's mod_rewrite behavior for the PHP built-in server
 * It routes all requests through index.php with the url parameter
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for a real file/directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Serve the file directly
}

// Extract the path after the public directory
$path = ltrim($uri, '/');

// Set the url parameter for the router
$_GET['url'] = $path;

// Include the main index.php
require __DIR__ . '/index.php';
