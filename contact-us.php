<?php
/**
 * Contact Us page for Aivyra AI
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
    <title>Contact Us — Aivyra AI</title>
    <meta name="description" content="Get in touch with the Aivyra AI team. We're here to help with any questions or support you need.">
    
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
                <img src="/assets/images/logo.png" alt="aivyra logo" class="w-7" />
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50">Aivyra</span>
            </a>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="/features.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Features</a>
                <a href="/about-us.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">About us</a>
                <a href="/pricing.php" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors">Pricing</a>
                <a href="/contact-us.php" class="text-sm font-medium text-swiss-red transition-colors">Contact us</a>
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
    <section style="background-image: linear-gradient(rgba(3,7,18,0.65), rgba(3,7,18,0.65)), url('https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="hero-section pt-36 pb-28 container h-screen mx-auto px-6 flex items-center">
            <div class="grid grid-cols-12 gap-6 w-full">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Contact Us
                </p>
            </div>
            
            <div class="col-span-12">
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold leading-[1.05] tracking-tight mb-6 text-white opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    We'd love to<br>
                    hear from you.
                </h1>
            </div>
            
            <div class="col-span-12 max-w-xl">
                <p class="text-lg text-gray-200 leading-relaxed mb-10 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Have questions? Need support? Want to connect? We're here to help. 
                    Reach out through any of the channels below and we'll get back to you as soon as possible.
                </p>
            </div>
        </div>
        </div>
    </section>

    <!-- Contact Options Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Get In Touch
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Choose how to<br>
                        reach us.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Live Chat</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Get instant help from our support team.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Email</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Send us a message anytime.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-swiss-red flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M23 7l-7 5 7 5V7z"/>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Video Call</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Book a demo or support call.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-28 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Send a Message
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Drop us a line.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-6 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <form class="space-y-6" action="#" method="POST">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors" placeholder="Your name" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors" placeholder="you@example.com" required>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium mb-2">Subject</label>
                        <select id="subject" name="subject" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors">
                            <option value="">Select a topic</option>
                            <option value="general">General Question</option>
                            <option value="support">Technical Support</option>
                            <option value="billing">Billing</option>
                            <option value="partnership">Partnership</option>
                            <option value="press">Press</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:border-swiss-red transition-colors resize-none" placeholder="How can we help?" required></textarea>
                    </div>
                    
                    <button type="submit" class="inline-flex items-center gap-2 px-7 py-4 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-xl hover:shadow-swiss-red/25 transition-all">
                        Send Message
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div class="col-span-12 lg:col-span-5 lg:col-start-7 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8">
                    <h3 class="text-xl font-semibold mb-6">Other Ways to Connect</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-semibold mb-2">Support</h4>
                            <p class="text-gray-500 dark:text-gray-400">For technical help and account issues, email our support team at <a href="mailto:support@aivyra.ai" class="text-swiss-red hover:underline">support@aivyra.ai</a></p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-2">Sales</h4>
                            <p class="text-gray-500 dark:text-gray-400">Interested in Elara for your team? Contact <a href="mailto:sales@elara.ai" class="text-swiss-red hover:underline">sales@elara.ai</a></p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-2">Partnerships</h4>
                            <p class="text-gray-500 dark:text-gray-400">Interested in partnering with us? Reach out to <a href="mailto:partners@aivyra.ai" class="text-swiss-red hover:underline">partners@aivyra.ai</a></p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-2">Press</h4>
                            <p class="text-gray-500 dark:text-gray-400">For press inquiries, contact <a href="mailto:press@elara.ai" class="text-swiss-red hover:underline">press@elara.ai</a></p>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold mb-4">Follow Us</h4>
                        <div class="flex gap-4">
                            <a href="#" class="text-gray-500 hover:text-swiss-red transition-colors">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-swiss-red transition-colors">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-swiss-red transition-colors">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0377c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8725-.8923a.077.077 0 01-.0076-.0287c.1738-.1324.3467-.2643.52-.3918a.0747.0747 0 01.0776-.0091c.1859.1047.3719.2087.5576.3181a.075.075 0 01.0466.0249c.0862.2924.1718.5845.2572.8769a.074.074 0 01-.0051.0977c-.1021.1043-.2059.2084-.3086.3126a.0789.0789 0 00-.005.0413c.2843.2677.5686.5354.8535.7989a.075.075 0 01.0463.0267c.1132.4137.2263.8274.3394 1.2411a.077.077 0 01-.0076.1059c-.261.2633-.523.5257-.7845.7881a.0802.0802 0 00.0033.1176c.3034.2576.6067.5152.9099.7729a.075.075 0 01.0462.0237c.084.2789.1678.5582.2515.8377a.076.076 0 01-.0113.0799c-.2817.2608-.5637.5217-.8457.7824a.076.076 0 00-.0079.0413c.2624.2775.525.5548.7878.8326a.0732.0732 0 00.0841-.0208c.1648-.1807.3296-.3615.4942-.5422a.0734.0734 0 00.0737-.0051c.1625.1088.3253.2175.4879.3263a.0737.0737 0 00.0847-.0114z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-swiss-red transition-colors">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Response Time Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 md:col-span-6 lg:col-span-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8">
                        <span class="text-4xl font-extrabold text-swiss-red block mb-2">~2 hrs</span>
                        <p class="text-gray-500 dark:text-gray-400">Average response time for email support</p>
                    </div>
                </div>
                
                <div class="col-span-12 md:col-span-6 lg:col-span-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8">
                        <span class="text-4xl font-extrabold text-swiss-red block mb-2">24/7</span>
                        <p class="text-gray-500 dark:text-gray-400">Live chat available around the clock</p>
                    </div>
                </div>
                
                <div class="col-span-12 md:col-span-12 lg:col-span-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8">
                        <span class="text-4xl font-extrabold text-swiss-red block mb-2">100+</span>
                        <p class="text-gray-500 dark:text-gray-400">Countries supported worldwide</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 container mx-auto px-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-3">
                <a href="/" class="flex items-center gap-2 no-underline mb-4">
                    <img src="/assets/images/logo.png" alt="aivyra logo" class="w-7" />
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
</html>>