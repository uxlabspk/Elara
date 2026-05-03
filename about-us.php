<?php
/**
 * About Us page for Aivyra AI
 * 
 * This page follows the design system from the landing page (index.php)
 * using Tailwind CSS with swiss-red color scheme.
 */

require_once 'includes/functions.php';
$pdo = require 'config/database.php';

$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Aivyra AI</title>
    <meta name="description" content="Learn about Aivyra AI - our mission, team, and vision for making AI accessible to everyone.">
    
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
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline">
                <span class="text-2xl font-bold text-swiss-red">E</span>
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Aivyra</span>
            </a>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="/features.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Features</a>
                <a href="/about-us.php" class="text-sm font-medium text-swiss-red transition-colors">About us</a>
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
    <section style="background-image: linear-gradient(rgba(3,7,18,0.6), rgba(3,7,18,0.6)), url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="hero-section pt-36 pb-28 h-screen container mx-auto px-6 flex items-center">
            <div class="grid grid-cols-12 gap-6 w-full">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    About Us
                </p>
            </div>
            
            <div class="col-span-12">
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold leading-[1.05] tracking-tight mb-6 text-white opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Building the future<br>
                    of AI assistance.
                </h1>
            </div>
            
            <div class="col-span-12 max-w-xl">
                <p class="text-lg text-gray-200 leading-relaxed mb-10 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    We're on a mission to make artificial intelligence accessible, 
                    helpful, and trustworthy for everyone. Learn more about 
                    our story, our team, and the values that drive everything we do.
                </p>
            </div>
        </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Our Mission
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        AI that empowers<br>
                        everyone.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 lg:col-span-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        We believe that AI should be a powerful tool for everyone, not just 
                        technologists. Our mission is to democratize access to advanced AI 
                        capabilities, making it simple and intuitive for people from all 
                        backgrounds to leverage the power of artificial intelligence.
                    </p>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
                        Aivyra AI is designed to be your intelligent companion — helping you with 
                        writing, coding, research, learning, and countless other tasks. We believe 
                        in building AI that augments human potential rather than replacing it.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-28 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Our Values
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    What drives us.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Trust & Security</h3>
                <p class="text-gray-500 dark:text-gray-400">Your data privacy and security are our top priority. We employ industry-leading security practices and never share your information.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Human-Centered Design</h3>
                <p class="text-gray-500 dark:text-gray-400">We build AI that works for humans. Every feature is designed with empathy and a deep understanding of user needs.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Continuous Innovation</h3>
                <p class="text-gray-500 dark:text-gray-400">We're constantly improving and evolving. Aivyra gets better every day thanks to user feedback and cutting-edge AI research.</p>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Our Story
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        From idea to reality.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 lg:col-span-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        Aivyra AI was born from a simple idea: what if everyone could have 
                        access to a brilliant assistant that helps them accomplish more? Our 
                        founders, with backgrounds in AI research and product design, set out 
                        to build an AI assistant that feels like a natural extension of your mind.
                    </p>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                        Since our launch, we've helped thousands of users — from students to 
                        professionals, creators to developers — achieve more with less effort. 
                        And we're just getting started.
                    </p>
                    <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
                        Today, we continue to push the boundaries of what's possible with AI, 
                        always keeping our users at the center of every decision we make.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-28 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Team
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    The people behind<br>
                    Aivyra.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 mx-auto mb-5"></div>
                <h3 class="text-xl font-semibold mb-1">Muhammad Naveed</h3>
                <p class="text-sm text-swiss-red mb-3">Head of Engineering</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Researcher with 5+ years of experience in Software and ML.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-pink-400 to-rose-600 mx-auto mb-5"></div>
                <h3 class="text-xl font-semibold mb-1">Tayyab Raza</h3>
                <p class="text-sm text-swiss-red mb-3">Head of Marketing</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Streamlining the process of market analysis with his statistical skills.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-sky-400 to-cyan-500 mx-auto mb-5"></div>
                <h3 class="text-xl font-semibold mb-1">Qazi Shahid</h3>
                <p class="text-sm text-swiss-red mb-3">Head of Design</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Product designer focused on human-centered AI.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 mx-auto mb-5"></div>
                <h3 class="text-xl font-semibold mb-1">Muhammad Ansar</h3>
                <p class="text-sm text-swiss-red mb-3">Head of Finances</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Finance experienced with 3+ years of experience.</p>
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
                <a href="/" class="flex items-center gap-2 no-underline mb-4">
                    <span class="text-2xl font-bold text-swiss-red">E</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Aivyra</span>
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