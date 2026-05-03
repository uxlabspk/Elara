<?php
require_once '../includes/functions.php';

// Get public token
$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    die('Invalid share token');
}

// Get shared conversation
$stmt = $pdo->prepare("SELECT * FROM shared_chats WHERE public_token = ?");
$stmt->execute([$token]);
$share = $stmt->fetch();

if (!$share) {
    die('Share link not found or expired');
}

// Get conversation
$stmt = $pdo->prepare("SELECT * FROM conversations WHERE id = ?");
$stmt->execute([$share['conversation_id']]);
$conversation = $stmt->fetch();

if (!$conversation) {
    die('Conversation not found');
}

// Get messages
$stmt = $pdo->prepare("SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
$stmt->execute([$share['conversation_id']]);
$messages = $stmt->fetchAll();

// Get conversation owner (for display purposes)
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$conversation['user_id']]);
$owner = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aivyra AI - Shared Conversation</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🧠</text></svg>">
    
    <!-- Local CSS -->
    <link href="assets/css/app.css" rel="stylesheet">
    
    <!-- Highlight.js for code syntax highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    
    <style>
        .markdown-content { white-space: pre-wrap; }
        .markdown-content pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
        .markdown-content code { background: #e2e8f0; color: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-family: monospace; }
        .dark .markdown-content code { background: #334155; color: #f1f5f9; }
        .markdown-content pre code { background: transparent; padding: 0; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/app/index.php" class="flex items-center space-x-2">
                    <span class="text-2xl">🧠</span>
                    <span class="font-bold text-xl text-gray-900 dark:text-white">Aivyra AI</span>
                </a>
                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" class="p-2 rounded-full text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        🌙
                    </button>
                    <a href="/app/index.php" class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Open Elara</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto py-8 px-4">
            <div class="card mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            <?= h($conversation['title']) ?>
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Shared conversation by <strong><?= h($owner['name'] ?? 'User') ?></strong>
                        </p>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo ucfirst($share['visibility']); ?> Share
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Created: <?= date('M j, Y g:i a', strtotime($share['created_at'])) ?>
                </p>
            </div>
            
            <!-- Messages -->
            <div class="space-y-4">
                <?php foreach ($messages as $message): ?>
                    <div class="<?= $message['role'] === 'user' ? 'flex justify-end' : 'flex justify-start' ?>">
                        <div class="<?= $message['role'] === 'user' ? 'message-user' : 'message-assistant' ?>">
                            <div class="markdown-content">
                                <?php
                                // Simple markdown rendering - could be enhanced with a markdown library
                                $content = h($message['content']);
                                // Basic markdown formatting
                                $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
                                $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
                                $content = preg_replace('/\n/', '<br>', $content);
                                echo $content;
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Footer Note -->
            <div class="mt-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    This is a shared conversation. You can view but not modify the messages.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    <a href="/app/index.php" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">Start your own conversation with Aivyra AI</a>
                </p>
            </div>
        </div>
    </main>
    
    <footer class="mt-12 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600 dark:text-gray-400 text-sm">
            <p>&copy; 2026 Aivyra AI. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Theme Toggle -->
    <script>
        document.getElementById('theme-toggle').addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.classList.remove(currentTheme);
            html.classList.add(newTheme);
            
            localStorage.setItem('theme', newTheme);
            this.textContent = newTheme === 'dark' ? '☀️' : '🌙';
        });
        
        // Syntax highlighting
        document.querySelectorAll('pre code').forEach((el) => {
            hljs.highlightElement(el);
        });
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(savedTheme);
            document.getElementById('theme-toggle').textContent = savedTheme === 'dark' ? '☀️' : '🌙';
        }
    </script>
</body>
</html>