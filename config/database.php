<?php
// config/database.php
session_start();

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ($value[0] === '"' && $value[strlen($value)-1] === '"') {
                $value = substr($value, 1, -1);
            }
            if ($value[0] === "'" && $value[strlen($value)-1] === "'") {
                $value = substr($value, 1, -1);
            }
            
            // Set as constant if not already defined
            if (!defined($key)) {
                define($key, $value);
            }
            // Also set in $_ENV for accessibility
            $_ENV[$key] = $value;
        }
    }
}

// Database configuration - use environment variables with fallbacks
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_NAME') || define('DB_NAME', 'elara');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

defined('MISTRAL_API_KEY') || define('MISTRAL_API_KEY', '');
defined('MISTRAL_API_URL') || define('MISTRAL_API_URL', 'https://api.mistral.ai/v1/chat/completions');

// PDO connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}