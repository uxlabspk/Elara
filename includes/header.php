<?php
// includes/header.php
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="<?= $settings['language'] ?? 'en' ?>" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Elara AI - <?=
        $current_page == 'index.php' ? 'Chat' :
        ($current_page == 'login.php' ? 'Login' :
        ($current_page == 'register.php' ? 'Register' :
        ($current_page == 'profile.php' ? 'Profile' :
        ($current_page == 'settings.php' ? 'Settings' :
        ($current_page == 'search.php' ? 'Search' :
        ($current_page == 'share.php' ? 'Share' : 'AI Assistant'))))))
    ?></title>
    
    <!-- Tailwind CSS CDN (for development) -->
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
    
    <!-- Local CSS -->
    <link href="assets/css/app.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🧠</text></svg>">
    
    <!-- Markdown CSS -->
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
    <?php if (is_logged_in()): ?>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <span class="text-2xl">🧠</span>
                        <span class="font-bold text-xl text-gray-900 dark:text-white">Elara AI</span>
                    </a>
                </div>
                
                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="index.php" class="px-3 py-2 rounded-md text-sm font-medium <?=
                        $current_page == 'index.php' ? 'bg-blue-100 text-blue-900 dark:bg-blue-900 dark:text-blue-100' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'
                    ?>">Chat</a>
                    <a href="search.php" class="px-3 py-2 rounded-md text-sm font-medium <?=
                        $current_page == 'search.php' ? 'bg-blue-100 text-blue-900 dark:bg-blue-900 dark:text-blue-100' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'
                    ?>">Search</a>
                    <a href="settings.php" class="px-3 py-2 rounded-md text-sm font-medium <?=
                        $current_page == 'settings.php' ? 'bg-blue-100 text-blue-900 dark:bg-blue-900 dark:text-blue-100' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'
                    ?>">Settings</a>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-full text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        <?php echo $theme == 'dark' ? '☀️' : '🌙'; ?>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2">
                            <?php
                            $user_avatar = get_user_avatar($pdo, $_SESSION['user_id']);
                            $user_name = get_user_name($pdo, $_SESSION['user_id']);
                            ?>
                            <img src="<?= h($user_avatar) ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                            <span class="hidden md:inline text-sm font-medium text-gray-900 dark:text-white">
                                <?= h($user_name) ?>
                            </span>
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Profile
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Settings
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php else: ?>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.php" class="flex items-center space-x-2">
                    <span class="text-2xl">🧠</span>
                    <span class="font-bold text-xl text-gray-900 dark:text-white">Elara AI</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Login</a>
                    <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Register</a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mt-4 mx-4 alert-success" id="flash-success">
            <?= h($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mt-4 mx-4 alert-error" id="flash-error">
            <?= h($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <main class="min-h-[calc(100vh-4rem)]">
