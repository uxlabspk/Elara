<?php
/**
 * Router for Aivyra AI
 * Handles routing for:
 *   / -> Landing page (this file)
 *   /app/* -> app/* (application pages)
 *   /auth/* -> auth/* (authentication pages)
 * All other requests are attempted to be served as static files.
 */

$uri = $_SERVER['REQUEST_URI'];

// Normalize URI (remove query string if any)
if (strpos($uri, '?') !== false) {
    $uri = substr($uri, 0, strpos($uri, '?'));
}

// Root route: serve landing page
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    chdir(__DIR__);
    // Ensure session is started before accessing $_SESSION
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once 'includes/functions.php';
    // Safely attempt to load user settings if available
    $settings = (isset($_SESSION['user_id']) && function_exists('get_user_settings') && isset($pdo)) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
    $theme = ((($settings['theme'] ?? 'light') == 'dark') ? 'dark' : 'light');
    // Prepare gradient string for hero background (avoid injecting extra quotes)
    $hero_gradient = $theme === 'dark'
        ? 'rgba(3,7,18,0.6), rgba(3,7,18,0.6)'
        : 'rgba(255,255,255,0.12), rgba(255,255,255,0.12)';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aivyra AI — Your Personal AI Assistant</title>
    <meta name="description" content="Aivyra AI is your personal AI assistant powered by advanced AI models. Chat, create, code, and learn with intelligent conversations.">
    
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
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-50 transition-colors duration-300 font-sans">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2 no-underline">
                <span class="text-2xl font-bold text-swiss-red">E</span>
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Aivyra</span>
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

    <!-- Hero Section -->
    <section style="background-image: linear-gradient(<?php echo ($theme == 'dark' ? "rgba(3,7,18,0.94), rgba(3,7,18,0.94)" : "rgba(0,0,0,0.58), rgba(0,0,0,0.58)"); ?>), url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1650&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="pt-36 pb-28 h-screen container mx-auto px-6 flex items-center bg-cover bg-center relative">
            <div class="grid grid-cols-12 gap-6 w-full">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-2 opacity-0 translate-y-8 transition-all duration-700 bg-transparent" data-animate>
                    AI Assistant
                </p>
            </div>
            
            <div class="col-span-12">
                <h1 class="text-blue-50 text-5xl md:text-7xl lg:text-8xl font-extrabold leading-[1.05] tracking-tight mb-6 opacity-0 translate-y-8 transition-all duration-700 bg-transparent" data-animate>
                    Think bigger.<br>
                    Create faster.
                </h1>
            </div>
            
            <div class="col-span-12 max-w-xl">
                <p class="text-lg text-gray-300 dark:text-gray-400 leading-relaxed mb-10 opacity-0 translate-y-8 transition-all duration-700 bg-transparent" data-animate>
                    Your personal AI assistant powered by advanced AI models. 
                    Chat, create, code, and learn with intelligent conversations 
                    that adapt to your needs.
                </p>
            </div>
            
            <div class="col-span-12 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="flex flex-wrap gap-4">
                    <?php if (!is_logged_in()): ?>
                        <a href="/auth/register.php" class="inline-flex items-center gap-2 px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                            Start Free Chat
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="/app/index.php" class="inline-flex items-center gap-2 px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                            Open Chat
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Capabilities
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Everything you need<br>
                        in one place.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature Cards -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Smart Writing</h3>
                    <p class="text-gray-500 dark:text-gray-400">Create compelling content with AI assistance. From emails to essays, get help with any writing task.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Code & Debug</h3>
                    <p class="text-gray-500 dark:text-gray-400">Write, review, and debug code across multiple programming languages with intelligent suggestions.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Research</h3>
                    <p class="text-gray-500 dark:text-gray-400">Dive deep into any topic with comprehensive analysis and well-sourced information.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Learning</h3>
                    <p class="text-gray-500 dark:text-gray-400">Learn new concepts with explanations tailored to your level of understanding.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Data Analysis</h3>
                    <p class="text-gray-500 dark:text-gray-400">Transform raw data into actionable insights with powerful analytical capabilities.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Conversations</h3>
                    <p class="text-gray-500 dark:text-gray-400">Natural, contextual conversations that remember your context and preferences.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-28 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Process
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Simple to start.<br>
                    Powerful results.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-swiss-red block mb-4">01</span>
                <h3 class="text-xl font-semibold mb-3">Ask anything</h3>
                <p class="text-gray-500 dark:text-gray-400">Type your question or task in natural language. No complicated prompts needed.</p>
            </div>
            
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-swiss-red block mb-4">02</span>
                <h3 class="text-xl font-semibold mb-3">Get instant answers</h3>
                <p class="text-gray-500 dark:text-gray-400">Receive intelligent responses tailored to your specific needs and context.</p>
            </div>
            
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-swiss-red block mb-4">03</span>
                <h3 class="text-xl font-semibold mb-3">Iterate & refine</h3>
                <p class="text-gray-500 dark:text-gray-400">Continue the conversation to refine results until you get exactly what you need.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        What people say
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Trusted by thousands<br>
                        of users worldwide.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <p class="italic text-gray-700 dark:text-gray-300 mb-6">"Aivyra has completely transformed how I work. It's like having a brilliant colleague available 24/7."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600"></div>
                        <div>
                            <p class="font-semibold">Sarah Chen</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Product Designer</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <p class="italic text-gray-700 dark:text-gray-300 mb-6">"The coding assistance is incredible. It helps me write better code faster and catch bugs before they happen."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-400 to-rose-600"></div>
                        <div>
                            <p class="font-semibold">Alex Rodriguez</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Software Engineer</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <p class="italic text-gray-700 dark:text-gray-300 mb-6">"I use Aivyra daily for research and writing. It's genuinely improved my productivity by leaps and bounds."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-400 to-cyan-500"></div>
                        <div>
                            <p class="font-semibold">Emily Watson</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Content Writer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-28 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Pricing
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Simple, transparent<br>
                    pricing for everyone.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-2">Free</p>
                <h3 class="text-4xl font-bold mb-2">$0</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8">Forever free</p>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Basic AI conversations</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Limited history</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Standard response time</span>
                    </li>
                </ul>
                
                <a href="/auth/register.php" class="flex items-center justify-center gap-2 w-full py-4 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-50 font-semibold rounded-lg hover:border-gray-400 dark:hover:border-gray-400 transition-all">
                    Get Started
                </a>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border-2 border-swiss-red rounded-2xl p-8 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-2">Pro</p>
                <h3 class="text-4xl font-bold mb-2">$19<span class="text-lg font-normal text-gray-500 dark:text-gray-400">/month</span></h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8">For power users</p>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Unlimited conversations</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Unlimited history</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Faster responses</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-swiss-red">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Advanced features</span>
                    </li>
                </ul>
                
                <a href="/auth/register.php" class="flex items-center justify-center gap-2 w-full py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                    Upgrade to Pro
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        FAQ
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Frequently asked<br>
                        questions.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12  opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <button class="w-full py-6 flex justify-between items-center text-left text-lg font-medium" onclick="toggleAccordion(this)">
                            <span>What is Aivyra AI?</span>
                            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                        <div class="max-h-0 overflow-hidden transition-all duration-300">
                            <div class="pb-6 text-gray-500 dark:text-gray-400">Elara is your personal AI assistant powered by advanced AI models. It can help you with writing, coding, research, learning, and much more through natural conversations.</div>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <button class="w-full py-6 flex justify-between items-center text-left text-lg font-medium" onclick="toggleAccordion(this)">
                            <span>Is Aivyra free to use?</span>
                            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                        <div class="max-h-0 overflow-hidden transition-all duration-300">
                            <div class="pb-6 text-gray-500 dark:text-gray-400">Yes, Aivyra has a free plan that lets you use basic features indefinitely. We also offer a Pro plan for users who need more advanced features and faster responses.</div>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <button class="w-full py-6 flex justify-between items-center text-left text-lg font-medium" onclick="toggleAccordion(this)">
                            <span>Is my data secure?</span>
                            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                        <div class="max-h-0 overflow-hidden transition-all duration-300">
                            <div class="pb-6 text-gray-500 dark:text-gray-400">Absolutely. We take data security seriously and follow industry best practices to protect your information. Your conversations are encrypted and never shared with third parties.</div>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <button class="w-full py-6 flex justify-between items-center text-left text-lg font-medium" onclick="toggleAccordion(this)">
                            <span>Can I cancel anytime?</span>
                            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                        <div class="max-h-0 overflow-hidden transition-all duration-300">
                            <div class="pb-6 text-gray-500 dark:text-gray-400">Yes, you can cancel your Pro subscription at any time. You'll continue to have access until the end of your billing period.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-28 container mx-auto px-6">
        <div class="text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">Ready to get started?</h2>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-10">Join thousands of users who have already transformed their workflow with Aivyra.</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <?php if (!is_logged_in()): ?>
                    <a href="/auth/register.php" class="inline-flex items-center gap-2 px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Create Free Account
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="/app/index.php" class="inline-flex items-center gap-2 px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Start Chatting
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-3">
                <a href="#" class="flex items-center gap-2 no-underline mb-4">
                    <span class="text-2xl font-bold text-swiss-red">E</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Aivyra</span>
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your personal AI assistant.</p>
            </div>
            
            <div class="col-span-6 md:col-span-2">
                <p class="text-sm font-semibold mb-4">Product</p>
                <ul class="space-y-3">
                    <li><a href="#features" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Features</a></li>
                    <li><a href="#pricing" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Pricing</a></li>
                </ul>
            </div>
            
            <div class="col-span-6 md:col-span-2">
                <p class="text-sm font-semibold mb-4">Company</p>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">About</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Blog</a></li>
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
        
        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 md:mb-0">&copy; 2026 Aivyra AI. All rights reserved.</p>
            <div class="flex items-center gap-2">
                <button id="themeToggle" class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg id="themeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span id="themeText">Dark Mode</span>
                </button>
            </div>
        </div>
    </footer>

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

        // Accordion toggle
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('svg');
            const isOpen = content.classList.contains('max-h-40');
            
            // Close all other open accordion items if desired (optional)
            // For now, independent toggles
            if (isOpen) {
                content.classList.remove('max-h-40');
                content.style.maxHeight = null;
                icon.classList.remove('rotate-45');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.classList.add('max-h-40'); // dummy class for tracking
                icon.classList.add('rotate-45');
            }
        }

        // Scroll-triggered fade-in animations
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

        // Theme toggle functionality
        (function initThemeToggle() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            
            if (themeToggle) {
                // Set initial state based on current theme
                const currentTheme = localStorage.getItem('theme') || 'light';
                updateThemeToggle(currentTheme);
                
                themeToggle.addEventListener('click', () => {
                    const html = document.documentElement;
                    const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
                    
                    html.classList.toggle('dark');
                    localStorage.setItem('theme', newTheme);
                    updateThemeToggle(newTheme);
                });
            }
            
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
<?php
    exit;
}

// For all other requests, let the PHP built-in server try to serve the file as static.
return false;