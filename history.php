<?php
require_once 'includes/functions.php';
require_login();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT id, title FROM conversations WHERE user_id = ? ORDER BY is_pinned DESC, updated_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'messages') {
    $convId = (int)($_GET['conversation_id'] ?? 0);
    if ($convId <= 0) exit(json_encode([]));
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmt->execute([$convId, $_SESSION['user_id']]);
    if (!$stmt->fetch()) exit(json_encode([]));

    $stmt = $pdo->prepare("SELECT role, content FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
    $stmt->execute([$convId]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(json_encode(['error' => 'POST required']));
    if (!verify_csrf($_POST['csrf_token'] ?? '')) exit(json_encode(['error' => 'CSRF failed']));
    
    $convId = (int)($_POST['conversation_id'] ?? 0);
    if (delete_conversation($pdo, $convId, $_SESSION['user_id'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete']);
    }
    exit;
}

if ($action === 'rename') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(json_encode(['error' => 'POST required']));
    if (!verify_csrf($_POST['csrf_token'] ?? '')) exit(json_encode(['error' => 'CSRF failed']));
    
    $convId = (int)($_POST['conversation_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if (empty($title)) exit(json_encode(['error' => 'Title required']));
    
    if (rename_conversation($pdo, $convId, $_SESSION['user_id'], $title)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to rename']);
    }
    exit;
}

if ($action === 'pin') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(json_encode(['error' => 'POST required']));
    if (!verify_csrf($_POST['csrf_token'] ?? '')) exit(json_encode(['error' => 'CSRF failed']));
    
    $convId = (int)($_POST['conversation_id'] ?? 0);
    if (toggle_pin_conversation($pdo, $convId, $_SESSION['user_id'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to toggle pin']);
    }
    exit;
}

// Invalid action
echo json_encode(['error' => 'Invalid action']);