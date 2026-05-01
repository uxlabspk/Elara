<?php
/**
 * Router for Elara AI
 * Handles routing for:
 *   / -> marketing.php
 *   /app/* -> app/* (application pages)
 *   /auth/* -> auth/* (authentication pages)
 * All other requests are attempted to be served as static files.
 */

$uri = $_SERVER['REQUEST_URI'];

// Normalize URI (remove query string if any)
if (strpos($uri, '?') !== false) {
    $uri = substr($uri, 0, strpos($uri, '?'));
}

// Root route: serve marketing page
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    // Change to the directory of this file to ensure relative paths work
    chdir(__DIR__);
    require_once 'marketing.php';
    exit;
}

// For all other requests, let the PHP built-in server try to serve the file as static.
// Returning false tells the server to serve the requested file directly.
return false;
?>