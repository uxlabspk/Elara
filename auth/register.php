<?php
/**
 * Register page for Elara AI
 * 
 * This page follows the design system from the landing page (index.php)
 * using Tailwind CSS with swiss-red color scheme.
 */

require_once '../includes/functions.php';
require_once '../config/database.php';

$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
            $stmt->execute([$pdo->lastInsertId()]);
            
            redirect('../app/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Elara AI</title>
    <meta name="description" content="Create your Elara AI account to access your personal AI assistant.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'heading': ['Space Grotesk', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'swiss-red': '#FF3B30',
                    }
                }
            }
        }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23FF3B30'/><text x='50' y='65' font-size='50' font-family='sans-serif' font-weight='bold' text-anchor='middle' fill='white'>E</text></svg>">
    
    <style>
        html { scroll-behavior: smooth; }
        
        /* Apply Space Grotesk to all heading elements */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }
        
        /* Custom form focus states */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.2);
        }
        
        /* Animation classes for fade-in */
        .animate-fade-in {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        
        .animate-fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-50 transition-colors duration-300 font-sans">

    <!-- Hero Section -->
    <section >
        <div class="pt-36 pb-28 h-screen container mx-auto px-6 flex items-center bg-cover bg-center relative">
            <div class="grid grid-cols-6 gap-6 w-full max-w-xl mx-auto">
                <div class="col-span-6">
                    <div class="bg-white dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-xl animate-fade-in">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-2">Create your account</h2>
                            <p class="text-gray-500 dark:text-gray-400">Join Elara AI to unlock powerful AI assistance</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-3 rounded-lg mb-6 flex items-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm"><?= h($error) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/auth/register.php" method="POST" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="<?= isset($_POST['name']) ? h($_POST['name']) : '' ?>"
                                    required 
                                    autofocus
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="John Doe"
                                >
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>"
                                    required 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="you@example.com"
                                >
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    minlength="8"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="•••••••• (min 8 characters)"
                                >
                            </div>
                            
                            <div>
                                <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                                <input 
                                    type="password" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    required 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="••••••••"
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-swiss-red"
                            >
                                Create Account
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="ml-2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                        
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-4">Or continue with</p>
                            <div class="flex gap-3">
                                <button class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.24 10.285V14.408h7.845c-.24 1.865-2.913 2.795-6.788 2.795-6.554 0-7.928-6.115-7.928-14.286 0-8.17 1.374-14.286 7.928-14.286 3.875 0 6.548.93 6.788 2.795h-7.845z"/>
                                    </svg>
                                    Google
                                </button>
                                <button class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12c0-6.627-5.373-12-12-12S0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    GitHub
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Already have an account? <a href="/auth/login.php" class="font-medium text-swiss-red hover:text-swiss-red/80 transition-colors">Sign in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        (function initFadeIn() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.animate-fade-in').forEach(el => {
                observer.observe(el);
            });
        })();
    </script>
</body>
</html>