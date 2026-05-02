<?php
// config/database.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
defined('SMTP_HOST') || define('SMTP_HOST', 'smtp.hostinger.com');
defined('SMTP_PORT') || define('SMTP_PORT', '587');
defined('SMTP_USERNAME') || define('SMTP_USERNAME', '');
defined('SMTP_PASSWORD') || define('SMTP_PASSWORD', '');
defined('SMTP_ENCRYPTION') || define('SMTP_ENCRYPTION', 'tls');
defined('SMTP_FROM_EMAIL') || define('SMTP_FROM_EMAIL', '');
defined('SMTP_FROM_NAME') || define('SMTP_FROM_NAME', 'Elara');

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

    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (email),
        INDEX (expires_at)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS email_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (email),
        INDEX (expires_at)
    )");

    $columnCheck = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'email_verified'");
    $columnCheck->execute();
    if (!$columnCheck->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 1 AFTER password");
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}