<?php
/**
 * Elara AI Forgot Password Page
 * Theme-matched forgot password page with dark/light mode support
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current theme from session or default to light
$theme = (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'dark' : 'light';

// Prepare gradient string for hero background
$hero_gradient = $theme === 'dark'
    ? 'rgba(3,7,18,0.94), rgba(3,7,18,0.94)'
    : 'rgba(0,0,0,0.58), rgba(0,0,0,0.58)';

require_once '../includes/functions.php';
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $user = get_user_by_email($pdo, $email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);
            
            $reset_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/auth/reset-password.php?token=' . $token;

            $emailBody = "Hello " . $user['name'] . ",\n\n";
            $emailBody .= "We received a request to reset your Elara AI password.\n\n";
            $emailBody .= "Reset your password here: " . $reset_link . "\n\n";
            $emailBody .= "This link expires in 1 hour. If you did not request this, you can ignore this message.";

            if (!send_smtp_email($email, $user['name'], 'Reset your Elara AI password', $emailBody)) {
                $error = 'We could not send the reset email right now. Please check your SMTP settings.';
            } else {
                $success = 'If an account with that email exists, we have sent password reset instructions.';
            }
        } else {
            $success = 'If an account with that email exists, we have sent password reset instructions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Elara AI</title>
    <meta name="description" content="Reset your Elara AI password">
    
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

        .hero-overlay {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.58), rgba(0, 0, 0, 0.58)),
                url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1650&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 dark:bg-gray-950/90 backdrop-blur border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline">
                <span class="text-2xl font-bold text-swiss-red">E</span>
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Elara</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="/auth/login.php" class="hidden sm:inline-flex text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Back to sign in</a>
                <button id="themeToggle" type="button" class="flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg id="themeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span id="themeText" class="hidden sm:inline">Dark Mode</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-overlay min-h-screen">
        <div class="pt-28 pb-16 min-h-screen container mx-auto px-6 flex items-center">
            <div class="grid grid-cols-12 gap-6 w-full items-center">
                <div class="col-span-12 lg:col-span-6 text-white">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Account recovery
                    </p>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05] tracking-tight mb-6 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Recover access
                        without stress.
                    </h1>
                    <p class="text-base md:text-lg text-gray-200 max-w-xl leading-relaxed mb-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        We will email a secure reset link if an account exists for your address.
                    </p>

                    <div class="rounded-xl border border-white/25 bg-white/10 backdrop-blur-sm p-4 max-w-xl opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        <p class="text-xs uppercase tracking-wider text-gray-200 mb-1">Security notice</p>
                        <p class="font-semibold">Reset links expire after 1 hour.</p>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <div class="max-w-xl lg:ml-auto bg-white/95 dark:bg-gray-900/85 backdrop-blur border border-gray-200 dark:border-gray-700 rounded-2xl p-7 md:p-9 shadow-2xl opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        <div class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900 dark:text-white">Reset your password</h2>
                            <p class="text-gray-500 dark:text-gray-400">Enter your email to receive reset instructions.</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg mb-6">
                                <?= h($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-lg mb-6">
                                <?= h($success) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$success): ?>
                        <form method="post" action="forgot-password.php" class="space-y-5">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>"
                                    required
                                    class="w-full px-4 py-3 bg-gray-50/90 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors"
                                    placeholder="you@example.com"
                                    autofocus
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/30 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-swiss-red"
                            >
                                Send Reset Link
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="ml-2" aria-hidden="true">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                        <?php endif; ?>

                        <div class="mt-7 pt-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
                            Remember your password?
                            <a href="/auth/login.php" class="font-medium text-swiss-red hover:text-swiss-red/80 transition-colors">Sign in</a>
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

        // Scroll-triggered reveal animation.
        (function initReveal() {
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

        // Theme toggle in navbar.
        (function initThemeToggle() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');

            if (!themeToggle || !themeIcon || !themeText) {
                return;
            }

            const currentTheme = localStorage.getItem('theme') || 'light';
            updateThemeToggle(currentTheme);

            themeToggle.addEventListener('click', () => {
                const html = document.documentElement;
                const newTheme = html.classList.contains('dark') ? 'light' : 'dark';

                html.classList.toggle('dark');
                localStorage.setItem('theme', newTheme);
                updateThemeToggle(newTheme);
            });

            function updateThemeToggle(theme) {
                if (theme === 'dark') {
                    themeIcon.innerHTML = '<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
                    themeText.textContent = 'Light Mode';
                } else {
                    themeIcon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
                    themeText.textContent = 'Dark Mode';
                }
            }
        })();
    </script>
</body>
</html>