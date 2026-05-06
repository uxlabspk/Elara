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
 * Send an email through the configured SMTP server.
 */
function send_smtp_email($toEmail, $toName, $subject, $body) {
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.hostinger.com';
    $port = (int)(defined('SMTP_PORT') ? SMTP_PORT : 587);
    $username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
    $password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
    $encryption = strtolower(defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls');
    $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : $username;
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Aivyra';

    if ($username === '' || $password === '' || $fromEmail === '') {
        error_log('SMTP is not configured.');
        return false;
    }

    $remoteHost = ($encryption === 'ssl') ? "ssl://{$host}" : $host;
    $socket = @stream_socket_client($remoteHost . ':' . $port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        error_log('SMTP connect failed: ' . $errstr);
        return false;
    }

    $readResponse = function () use ($socket) {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    };

    $sendCommand = function ($command, $expectedCodes) use ($socket, $readResponse) {
        fwrite($socket, $command . "\r\n");
        $response = $readResponse();
        $code = (int)substr($response, 0, 3);
        if (!in_array($code, (array)$expectedCodes, true)) {
            error_log('SMTP command failed: ' . trim($command) . ' | Response: ' . trim($response));
            return false;
        }
        return $response;
    };

    $initialResponse = $readResponse();
    if ((int)substr($initialResponse, 0, 3) !== 220) {
        error_log('SMTP server rejected connection: ' . trim($initialResponse));
        fclose($socket);
        return false;
    }

    $hostname = gethostname() ?: 'localhost';
    if ($sendCommand('EHLO ' . $hostname, [250]) === false) {
        fclose($socket);
        return false;
    }

    if ($encryption === 'tls' && $port !== 465) {
        if ($sendCommand('STARTTLS', [220]) === false) {
            fclose($socket);
            return false;
        }

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('SMTP STARTTLS negotiation failed.');
            fclose($socket);
            return false;
        }

        if ($sendCommand('EHLO ' . $hostname, [250]) === false) {
            fclose($socket);
            return false;
        }
    }

    if ($sendCommand('AUTH LOGIN', [334]) === false) {
        fclose($socket);
        return false;
    }
    if ($sendCommand(base64_encode($username), [334]) === false) {
        fclose($socket);
        return false;
    }
    if ($sendCommand(base64_encode($password), [235]) === false) {
        fclose($socket);
        return false;
    }

    if ($sendCommand('MAIL FROM:<' . $fromEmail . '>', [250]) === false) {
        fclose($socket);
        return false;
    }
    if ($sendCommand('RCPT TO:<' . $toEmail . '>', [250, 251]) === false) {
        fclose($socket);
        return false;
    }
    if ($sendCommand('DATA', [354]) === false) {
        fclose($socket);
        return false;
    }

    $encodedSubject = preg_match('/[^\x20-\x7E]/', $subject)
        ? '=?UTF-8?B?' . base64_encode($subject) . '?='
        : $subject;
    $safeBody = preg_replace('/\r?\n/', "\r\n", $body);
    $safeBody = preg_replace('/^\./m', '..', $safeBody);

    $headers = [
        'From: ' . ($fromName !== '' ? $fromName . ' <' . $fromEmail . '>' : $fromEmail),
        'To: ' . ($toName !== '' ? $toName . ' <' . $toEmail . '>' : $toEmail),
        'Subject: ' . $encodedSubject,
        'Date: ' . date(DATE_RFC2822),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 7bit',
    ];

    fwrite($socket, implode("\r\n", $headers) . "\r\n\r\n" . $safeBody . "\r\n.\r\n");
    $dataResponse = $readResponse();
    $sent = (int)substr($dataResponse, 0, 3) === 250;

    $sendCommand('QUIT', [221]);
    fclose($socket);

    if (!$sent) {
        error_log('SMTP send failed: ' . trim($dataResponse));
    }

    return $sent;
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
    global $pdo;

    if (!is_logged_in()) {
        redirect('/auth/login.php');
    }

    if (isset($pdo)) {
        $user = get_user_by_id($pdo, $_SESSION['user_id']);
        if (!$user) {
            user_logout();
            redirect('/auth/login.php');
        }
    }
}

/**
 * Get current user's settings (or defaults).
 */
function get_user_settings($pdo, $user_id) {
    $user = get_user_by_id($pdo, $user_id);
    if (!$user) {
        return [
            'theme' => 'light',
            'model' => 'mistral-small-latest',
            'temperature' => 0.7,
            'max_tokens' => 2048,
            'language' => 'en'
        ];
    }

    $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $settings = $stmt->fetch();
    if (!$settings) {
        // Insert defaults
        try {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
            $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            return [
                'theme' => 'light',
                'model' => 'mistral-small-latest',
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'language' => 'en'
            ];
        }
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
        'model' => 'mistral-small-latest',
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