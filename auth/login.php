<?php
/**
 * Login page for Elara AI
 * 
 * This page follows the design system from the landing page (index.php)
 * using Tailwind CSS with swiss-red color scheme.
 */

require_once '../includes/functions.php';
require_once '../config/database.php';

$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = get_user_by_email($pdo, $email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in_at'] = time();
            
            $stmt = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
                $stmt->execute([$user['id']]);
            }
            
            if ($remember) {
                setcookie('elara_remember', bin2hex(random_bytes(32)), time() + (30 * 24 * 60 * 60), '/', '', true, true);
                $hash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
                setcookie('elara_user', $user['id'], time() + (30 * 24 * 60 * 60), '/', '', true, true);
                setcookie('elara_token', $hash, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
            
            redirect('../app/index.php');
        } else {
            $error = 'Invalid email or password.';
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
    <meta name="description" content="Login to your Elara AI account">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
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
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-50 transition-colors duration-300 font-sans">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline">
                <span class="text-2xl font-bold text-swiss-red">E</span>
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Elara</span>
            </a>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="/features.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Features</a>
                <a href="/about-us.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">About us</a>
                <a href="/pricing.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Pricing</a>
                <a href="/contact-us.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Contact us</a>
            </div>
            
            <div class="flex items-center gap-4">
                <?php if (is_logged_in()): ?>
                    <a href="/app/index.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Go to App
                    </a>
                <?php else: ?>
                    <a href="/auth/login.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Login</a>
                    <a href="/auth/register.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Login Form Section -->
    <section class="pt-36 pb-28 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-6 lg:col-span-5">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Login
                </p>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-6 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Welcome back.
                </h1>
                
                <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed mb-10 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Sign in to continue to your Elara AI account.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-6 lg:col-span-5 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <?php if ($error): ?>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg mb-6">
                        <?= h($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="login.php" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="<?= h($email) ?>" 
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors" required autofocus>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors" required>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-swiss-red bg-gray-100 border-gray-300 rounded focus:ring-swiss-red dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Remember me</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="inline-flex items-center gap-2 w-full justify-center px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Sign In
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
                
                <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Don't have an account? 
                    <a href="/auth/register.php" class="text-swiss-red font-semibold hover:underline">Sign up</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 max-w-7xl mx-auto px-6 mt-auto">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-3">
                <a href="/" class="flex items-center gap-2 no-underline mb-4">
                    <span class="text-2xl font-bold text-swiss-red">E</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Elara</span>
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your personal AI assistant.</p>
            </div>
            
            <div class="col-span-6 md:col-span-2">
                <p class="text-sm font-semibold mb-4">Product</p>
                <ul class="space-y-3">
                    <li><a href="/features.php" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Features</a></li>
                    <li><a href="/pricing.php" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Pricing</a></li>
                </ul>
            </div>
            
            <div class="col-span-6 md:col-span-2">
                <p class="text-sm font-semibold mb-4">Company</p>
                <ul class="space-y-3">
                    <li><a href="/about-us.php" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">About</a></li>
                    <li><a href="/contact-us.php" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-span-6 md:col-span-2">
                <p class="text-sm font-semibold mb-4">Legal</p>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Terms</a></li>
                </ul>
            </div>
            
            <div class="col-span-12 md:col-span-3">
                <p class="text-sm font-semibold mb-4">Connect</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-800 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">&copy; 2026 Elara AI. All rights reserved.</p>
        </div>
    </footer>

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
                        entry.target.classList.add('!opacity-100', '!translate-y-0');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('[data-animate]').forEach(el => {
                observer.observe(el);
            });
        })();
    </script>
</body>
</html>