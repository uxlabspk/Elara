<?php
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];

// Get user conversations
$conversations = get_user_conversations($pdo, $user_id);

// Get already shared conversations
$stmt = $pdo->prepare("SELECT * FROM shared_chats WHERE conversation_id IN (SELECT id FROM conversations WHERE user_id = ?)");
$stmt->execute([$user_id]);
$shared_conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map shared conversations by conversation_id
$shared_map = [];
foreach ($shared_conversations as $shared) {
    $shared_map[$shared['conversation_id']] = $shared;
}

$error = '';
$success = '';

// Handle generate share link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_link') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $conversation_id = (int)($_POST['conversation_id'] ?? 0);
    $visibility = $_POST['visibility'] ?? 'unlisted';
    
    // Verify conversation ownership
    $conversation = get_conversation($pdo, $conversation_id, $user_id);
    if (!$conversation) {
        $error = 'Conversation not found.';
    } else {
        // Check if already shared
        if (isset($shared_map[$conversation_id])) {
            // Update existing share
            $token = $shared_map[$conversation_id]['public_token'];
        } else {
            // Generate new token
            $token = generate_token();
        }
        
        // Insert or update
        if (isset($shared_map[$conversation_id])) {
            $stmt = $pdo->prepare("UPDATE shared_chats SET visibility = ?, created_at = NOW() WHERE id = ?");
            $stmt->execute([$visibility, $shared_map[$conversation_id]['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO shared_chats (conversation_id, public_token, visibility) VALUES (?, ?, ?)");
            $stmt->execute([$conversation_id, $token, $visibility]);
        }
        
        $share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                     '://' . $_SERVER['HTTP_HOST'] . '/app/view_shared.php?token=' . $token;
        
        $success = 'Share link generated!';
        $_SESSION['success'] = $success;
        
        // Redirect to share page with the token
        redirect('share.php?generated=1&token=' . $token);
    }
}

// Handle revoke link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'revoke_link') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $share_id = (int)($_POST['share_id'] ?? 0);
    
    // Verify share belongs to user
    $stmt = $pdo->prepare("SELECT sc.id FROM shared_chats sc JOIN conversations c ON sc.conversation_id = c.id WHERE sc.id = ? AND c.user_id = ?");
    $stmt->execute([$share_id, $user_id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM shared_chats WHERE id = ?");
        $stmt->execute([$share_id]);
        $success = 'Share link revoked!';
        $_SESSION['success'] = $success;
        redirect('share.php');
    } else {
        $error = 'Share link not found.';
    }
}

// Get the generated token if any
$generated_token = $_GET['generated'] ?? null;
$token = $_GET['token'] ?? null;

require_once '../includes/header.php';
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Generate Share Link -->
        <div class="card">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Generate Share Link</h3>
            
            <?php if ($error): ?>
                <div class="alert-error mb-4"><?= h($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success && !isset($generated_token)): ?>
                <div class="alert-success mb-4"><?= h($success) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="action" value="generate_link">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Conversation
                    </label>
                    <select name="conversation_id" class="form-input" required>
                        <option value="">Select a conversation</option>
                        <?php foreach ($conversations as $conv): ?>
                            <option value="<?= $conv['id'] ?>" 
                                <?= (isset($generated_token) && isset($shared_map[$conv['id']]) && $shared_map[$conv['id']]['public_token'] === $token) ? 'selected' : '' ?>>
                                <?= h($conv['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Visibility
                    </label>
                    <select name="visibility" class="form-input">
                        <option value="public">Public - Anyone can view (searchable)</option>
                        <option value="unlisted" selected>Unlisted - Anyone with link can view</option>
                        <option value="private">Private - Only you can view</option>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Unlisted is recommended for most sharing.
                    </p>
                </div>
                
                <button type="submit" class="btn-primary w-full">
                    Generate Share Link
                </button>
            </form>
            
            <!-- Generated Link Display -->
            <?php if (isset($generated_token) && $token && isset($shared_map) && count($shared_map) > 0): ?>
                <?php 
                $found = false;
                foreach ($shared_map as $conv_id => $shared) {
                    if ($shared['public_token'] === $token) {
                        $found = true;
                        $conversation = get_conversation($pdo, $conv_id, $user_id);
                        break;
                    }
                }
                if ($found):
                    $share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                                 '://' . $_SERVER['HTTP_HOST'] . '/app/view_shared.php?token=' . $token;
                ?>
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-2">Share Link Generated</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">
                        Conversation: <strong><?= h($conversation['title']) ?></strong><br>
                        Visibility: <strong><?= ucfirst($shared['visibility']) ?></strong>
                    </p>
                    <div class="flex gap-2">
                        <input type="text" value="<?= h($share_url) ?>" 
                               class="flex-1 form-input text-sm" readonly>
                        <button onclick="navigator.clipboard.writeText('<?= addslashes($share_url) ?>').then(() => alert('Link copied to clipboard!'))" 
                                class="btn-secondary whitespace-nowrap">
                            Copy
                        </button>
                    </div>
                    <a href="<?= h($share_url) ?>" target="_blank" class="inline-block mt-3 text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                        Open in new tab →
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Shared Conversations List -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Shared Conversations</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <?= count($shared_conversations) ?> active
                </span>
            </div>
            
            <?php if (empty($shared_conversations)): ?>
                <p class="text-center text-gray-400 dark:text-gray-500 py-8">
                    You haven't shared any conversations yet.
                </p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($shared_conversations as $shared): ?>
                        <?php 
                        $conv = get_conversation($pdo, $shared['conversation_id'], $user_id);
                        if (!$conv) continue;
                        
                        $share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                                     '://' . $_SERVER['HTTP_HOST'] . '/app/view_shared.php?token=' . $shared['public_token'];
                        ?>
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                        <?= h($conv['title']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Visibility: <span class="capitalize"><?= h($shared['visibility']) ?></span> • 
                                        Created: <?= date('M j, Y', strtotime($shared['created_at'])) ?>
                                    </p>
                                    <a href="<?= h($share_url) ?>" target="_blank" 
                                       class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                                        View →
                                    </a>
                                </div>
                                <form method="post" onsubmit="return confirm('Are you sure you want to revoke this share link?');">
                                    <input type="hidden" name="action" value="revoke_link">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="share_id" value="<?= $shared['id'] ?>">
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 p-1 ml-2"
                                            title="Revoke">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                Note: anyone with a share link can view the conversation. Be careful when sharing sensitive information.
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
