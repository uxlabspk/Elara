<?php
// config/database.php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'elara');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('MISTRAL_API_KEY', 'YOUR_MISTRAL_API_KEY');
define('MISTRAL_API_URL', 'https://api.mistral.ai/v1/chat/completions');

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