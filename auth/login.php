<?php
/**
 * Elara AI Login Page
 * Theme-matched login page with dark/light mode support
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$theme = (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'dark' : 'light';

require_once '../includes/functions.php';
require_once '../config/database.php';

if (is_logged_in()) {
    redirect('/app/index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid session token. Please try again.';
    } else {
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Please enter your email and password.';
        } elseif (!user_login($pdo, $email, $password)) {
            $error = 'Invalid email or password.';
        } else {
            redirect('/app/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Elara AI</title>
    <meta name="description" content="Log in to your Elara AI account to access your personal AI assistant.">
    
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
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23FF3B30'/><text x='50' y='65' font-size='50' font-family='sans-serif' font-weight='bold' text-anchor='middle' fill='white'>E</text></svg>">
    
    <style>
        /* Only minimal styles needed for smooth scroll and any edge-cases Tailwind can't handle easily */
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
                            <h2 class="text-2xl font-bold mb-2">Sign in to Elara</h2>
                            <p class="text-gray-500 dark:text-gray-400">Enter your credentials to access your AI assistant</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-3 rounded-lg mb-6 flex items-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm"><?= h($error) ?></span>
                            </div>
                        <?php endif; ?>

                        <form action="/auth/login.php" method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email address</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?= h($email) ?>"
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
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="••••••••"
                                >
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="remember" 
                                        name="remember" 
                                        class="h-4 w-4 text-swiss-red focus:ring-swiss-red border-gray-300 rounded"
                                    >
                                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                                </div>
                                <a href="/auth/forgot-password.php" class="text-sm font-medium text-swiss-red hover:text-swiss-red/80 transition-colors">Forgot password?</a>
                            </div>
                            
                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-swiss-red"
                            >
                                Sign in
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="ml-2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                        
                        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Don't have an account? <a href="/auth/register.php" class="font-medium text-swiss-red hover:text-swiss-red/80 transition-colors">Sign up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Theme initialization using Tailwind's 'class' dark mode
        (function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        // Fade-in animation for sections
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