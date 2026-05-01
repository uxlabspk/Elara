<?php
// includes/functions.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

/**
 * Generate CSRF token and store in session.
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token. Call on POST actions.
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to a URL.
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if user is logged in.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication. Redirect to login if not.
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/**
 * Get current user's settings (or defaults).
 */
function get_user_settings($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $settings = $stmt->fetch();
    if (!$settings) {
        // Insert defaults
        $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
        return [
            'theme' => 'light',
            'model' => 'mistral-small-latest',
            'temperature' => 0.7,
            'max_tokens' => 2048,
            'language' => 'en'
        ];
    }
    return $settings;
}

/**
 * Call Mistral AI API.
 * $messages: array of ['role' => '...', 'content' => '...']
 * Returns response text or false on error.
 */
function call_mistral($messages, $settings) {
    $apiKey = MISTRAL_API_KEY;
    $url = MISTRAL_API_URL;

    $payload = [
        'model' => $settings['model'] ?? 'mistral-small-latest',
        'messages' => $messages,
        'temperature' => (float)($settings['temperature'] ?? 0.7),
        'max_tokens' => (int)($settings['max_tokens'] ?? 2048),
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        error_log("Mistral API error: HTTP $httpCode, Response: " . $response);
        return false;
    }

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? false;
}

/**
 * Simple rate limiter: max 20 requests per minute per user.
 */
function rate_limit($user_id) {
    global $pdo;
    $minuteAgo = date('Y-m-d H:i:s', time() - 60);
    // Count messages from this user in last minute
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM messages m
         JOIN conversations c ON m.conversation_id = c.id
         WHERE c.user_id = ? AND m.created_at > ? AND m.role = 'user'"
    );
    $stmt->execute([$user_id, $minuteAgo]);
    $count = $stmt->fetchColumn();
    if ($count >= 20) {
        header('HTTP/1.1 429 Too Many Requests');
        echo json_encode(['error' => 'Rate limit exceeded. Please wait.']);
        exit;
    }
}

/**
 * Sanitize output for HTML.
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a unique share token.
 */
function generate_token() {
    return bin2hex(random_bytes(32));
}