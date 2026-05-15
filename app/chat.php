<?php
require_once '../includes/functions.php';
require_login();

header('Content-Type: application/json');

// Verify CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');
$convId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;

// Handle file upload
$fileContent = $_POST['file_content'] ?? '';
$fileName = $_POST['file_name'] ?? '';

if ($message === '' && $fileContent === '') {
    echo json_encode(['error' => 'Message empty']);
    exit;
}

// Rate limit
rate_limit($userId);

// Get user settings
$settings = get_user_settings($pdo, $userId);

// Conversation handling
if ($convId === 0) {
    // Create new conversation
    $stmt = $pdo->prepare("INSERT INTO conversations (user_id, title) VALUES (?, ?)");
    // Use first 30 chars of message as title
    $title = mb_substr($message, 0, 30) . (mb_strlen($message) > 30 ? '...' : '');
    $stmt->execute([$userId, $title]);
    $convId = $pdo->lastInsertId();
} else {
    // Ensure conversation belongs to user
    $stmt = $pdo->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmt->execute([$convId, $userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['error' => 'Conversation not found']);
        exit;
    }
}

// Save user message
$finalMessage = $message;
if ($fileContent !== '') {
    $finalMessage = $message . "\n\n[ATTACHED FILE: " . $fileName . "]\n" . substr($fileContent, 0, 2000) . (strlen($fileContent) > 2000 ? "..." : "");
}
$stmt = $pdo->prepare("INSERT INTO messages (conversation_id, role, content) VALUES (?, 'user', ?)");
$stmt->execute([$convId, $finalMessage]);

// Build message history for API (limit to last 20 messages to manage tokens)
$stmt = $pdo->prepare("SELECT role, content FROM messages WHERE conversation_id = ? ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$convId]);
$history = array_reverse($stmt->fetchAll()); // chronological order

// Add system prompt (optional)
$apiMessages = [];
// You could add a system message here, e.g.
// $apiMessages[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];

foreach ($history as $msg) {
    $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
}

// If there's a file attachment, add it to the current message
if ($fileContent !== '') {
    $lastMessageIndex = count($apiMessages) - 1;
    if ($lastMessageIndex >= 0 && $apiMessages[$lastMessageIndex]['role'] === 'user') {
        $apiMessages[$lastMessageIndex]['content'] = $message . "\n\n[FILE CONTENT]\n" . $fileContent;
    }
}

// Call Mistral
$reply = call_mistral($apiMessages, $settings);

if ($reply === false) {
    echo json_encode(['error' => 'AI service unavailable']);
    exit;
}

// Save assistant reply
$stmt = $pdo->prepare("INSERT INTO messages (conversation_id, role, content) VALUES (?, 'assistant', ?)");
$stmt->execute([$convId, $reply]);

echo json_encode([
    'reply' => $reply,
    'conversation_id' => $convId
]);