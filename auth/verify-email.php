<?php
/**
 * Verify Email page for Elara AI
 * 
 * This page follows the design system from the landing page (index.php)
 * using Tailwind CSS with swiss-red color scheme.
 */

require_once '../includes/functions.php';
require_once '../config/database.php';

$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';

$error = '';
$message = '';
$verified = false;
$token = $_GET['token'] ?? '';

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT email FROM email_verifications WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $verification = $stmt->fetch();

    if ($verification) {
        $email = $verification['email'];

        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE email = ?");
        $stmt->execute([$email]);

        $stmt = $pdo->prepare("DELETE FROM email_verifications WHERE token = ?");
        $stmt->execute([$token]);

        $verified = true;
    } else {
        $error = 'Invalid or expired verification token.';
    }
}

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
            $verification_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $stmt = $pdo->prepare("DELETE FROM email_verifications WHERE email = ?");
            $stmt->execute([$email]);
            
            $stmt = $pdo->prepare("INSERT INTO email_verifications (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $verification_token, $expires]);
            
            $verify_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/auth/verify-email.php?token=' . $verification_token;

            $emailBody = "Hello " . $user['name'] . ",\n\n";
            $emailBody .= "Please verify your Elara AI email address by clicking this link:\n\n";
            $emailBody .= $verify_link . "\n\n";
            $emailBody .= "This link expires in 1 hour. If you did not request this, you can ignore this message.";

            if (!send_smtp_email($email, $user['name'], 'Verify your Elara AI email address', $emailBody)) {
                $error = 'We could not send the verification email right now. Please check your SMTP settings.';
            } else {
                $message = 'If an account with that email exists, we have sent verification instructions.';
            }
        } else {
            $message = 'If an account with that email exists, we have sent verification instructions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — Elara AI</title>
    <meta name="description" content="Verify your Elara AI email address">
    
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

        .hero-overlay {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.58), rgba(0, 0, 0, 0.58)),
                url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1650&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
                        Email verification
                    </p>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.05] tracking-tight mb-6 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Confirm your
                        account email.
                    </h1>
                    <p class="text-base md:text-lg text-gray-200 max-w-xl leading-relaxed mb-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Verify your inbox to secure your account and unlock full access.
                    </p>
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <div class="max-w-xl lg:ml-auto bg-white/95 dark:bg-gray-900/85 backdrop-blur border border-gray-200 dark:border-gray-700 rounded-2xl p-7 md:p-9 shadow-2xl opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        <div class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900 dark:text-white">Verify email</h2>
                            <p class="text-gray-500 dark:text-gray-400">We will send you a verification link.</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg mb-6">
                                <?= h($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-lg mb-6">
                                <?= h($message) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($verified): ?>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-lg mb-6">
                                Your email has been verified. You can now <a href="/auth/login.php" class="font-semibold underline">sign in</a>.
                            </div>
                        <?php elseif (!$message && empty($token)): ?>
                        <form method="post" action="verify-email.php" class="space-y-5">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>"
                                       class="w-full px-4 py-3 bg-gray-50/90 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-swiss-red focus:border-transparent transition-colors" required autofocus>
                            </div>

                            <button type="submit" class="inline-flex items-center gap-2 w-full justify-center px-7 py-3.5 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/30 transition-all">
                                Send Verification Link
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                        <?php endif; ?>

                        <div class="mt-7 pt-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
                            Already verified?
                            <a href="/auth/login.php" class="font-medium text-swiss-red hover:text-swiss-red/80 transition-colors">Sign in</a>
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