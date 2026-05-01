<?php
require_once 'includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$query = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all'; // all, conversations, messages

// Get user settings
$settings = get_user_settings($pdo, $user_id);

// Handle search
$results = [];
if (!empty($query) && strlen($query) >= 2) {
    // Enable fulltext search if not already enabled
    $search_query = '%' . $query . '%';
    
    if ($type === 'conversations' || $type === 'all') {
        // Search conversations by title
        $stmt = $pdo->prepare(
            "SELECT c.id, c.title, c.updated_at, 
                    (LENGTH(c.title) - LENGTH(REPLACE(c.title, ?, ''))) / LENGTH(?) AS relevance
             FROM conversations c 
             WHERE c.user_id = ? AND c.title LIKE ?
             ORDER BY relevance DESC, c.updated_at DESC
             LIMIT 50"
        );
        $stmt->execute([$query, $query, $user_id, $search_query]);
        $conversation_results = $stmt->fetchAll();
        
        foreach ($conversation_results as $conv) {
            $results[] = [
                'type' => 'conversation',
                'id' => $conv['id'],
                'title' => $conv['title'],
                'excerpt' => '',
                'date' => $conv['updated_at'],
                'relevance' => $conv['relevance']
            ];
        }
    }
    
    if ($type === 'messages' || $type === 'all') {
        // Search messages by content
        $stmt = $pdo->prepare(
            "SELECT m.id, m.conversation_id, m.role, m.content, m.created_at,
                    c.title as conversation_title,
                    (LENGTH(m.content) - LENGTH(REPLACE(m.content, ?, ''))) / LENGTH(?) AS relevance
             FROM messages m
             JOIN conversations c ON m.conversation_id = c.id
             WHERE c.user_id = ? AND m.content LIKE ?
             ORDER BY relevance DESC, m.created_at DESC
             LIMIT 50"
        );
        $stmt->execute([$query, $query, $user_id, $search_query]);
        $message_results = $stmt->fetchAll();
        
        foreach ($message_results as $msg) {
            // Extract excerpt around the query
            $content = $msg['content'];
            $pos = stripos($content, $query);
            if ($pos !== false) {
                $start = max(0, $pos - 100);
                $excerpt = substr($content, $start, 200);
                if ($start > 0) {
                    $excerpt = '...' . $excerpt;
                }
                if (strlen($content) > $start + 200) {
                    $excerpt .= '...';
                }
            } else {
                $excerpt = substr($content, 0, 200) . (strlen($content) > 200 ? '...' : '');
            }
            
            $results[] = [
                'type' => 'message',
                'id' => $msg['id'],
                'conversation_id' => $msg['conversation_id'],
                'conversation_title' => $msg['conversation_title'],
                'role' => $msg['role'],
                'excerpt' => h($excerpt),
                'date' => $msg['created_at'],
                'relevance' => $msg['relevance']
            ];
        }
    }
    
    // Sort by relevance
    usort($results, function($a, $b) {
        return ($b['relevance'] ?? 0) <=> ($a['relevance'] ?? 0);
    });
}

require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="card">
        <!-- Search Form -->
        <form method="get" action="search.php" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 relative">
                    <input type="text" name="q" value="<?= h($query) ?>" 
                           placeholder="Search conversations and messages..." 
                           class="form-input pr-10" required minlength="2" autofocus>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        🔍
                    </button>
                </div>
                <select name="type" class="form-input sm:w-48">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="conversations" <?= $type === 'conversations' ? 'selected' : '' ?>>Conversations</option>
                    <option value="messages" <?= $type === 'messages' ? 'selected' : '' ?>>Messages</option>
                </select>
            </div>
        </form>
        
        <!-- Results -->
        <?php if (empty($query)): ?>
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <p>Enter a search query to find conversations and messages.</p>
            </div>
        <?php elseif (strlen($query) < 2): ?>
            <div class="alert-warning text-center py-4">
                Please enter at least 2 characters to search.
            </div>
        <?php elseif (empty($results)): ?>
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <p>No results found for "<?= h($query) ?>".</p>
            </div>
        <?php else: ?>
            <div class="space-y-1">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Found <?= count($results) ?> result<?= count($results) > 1 ? 's' : '' ?> for "<?= h($query) ?>"
                </p>
                
                <?php foreach ($results as $result): ?>
                    <?php if ($result['type'] === 'conversation'): ?>
                        <div class="p-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg cursor-pointer" 
                             onclick="window.location.href='index.php?c=<?= $result['id'] ?>'">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">💬</span>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        <?= h($result['title']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Conversation • Updated <?= date('M j, Y g:i a', strtotime($result['date'])) ?>
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    <?php else: // message ?>
                        <div class="p-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg" 
                             onclick="window.location.href='index.php?c=<?= $result['conversation_id'] ?>'">
                            <div class="flex items-start space-x-3">
                                <span class="text-xl mt-1">💭</span>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        <?= h($result['conversation_title']) ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        <?= $result['excerpt'] ?>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= ucfirst($result['role']) ?> • <?= date('M j, Y g:i a', strtotime($result['date'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
