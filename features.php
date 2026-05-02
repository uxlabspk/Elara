<?php
/**
 * Features page for Elara AI
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
    <title>Features — Elara AI</title>
    <meta name="description" content="Discover the powerful features of Elara AI - your personal AI assistant for writing, coding, research, and learning.">
    
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
                <a href="/features.php" class="text-sm font-medium text-swiss-red transition-colors">Features</a>
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
    <section class="pt-36 pb-28 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-4 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Features
                </p>
            </div>
            
            <div class="col-span-12">
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold leading-[1.05] tracking-tight mb-6 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Powerful AI.<br>
                    Limitless possibilities.
                </h1>
            </div>
            
            <div class="col-span-12 max-w-xl">
                <p class="text-lg text-gray-500 dark:text-gray-400 leading-relaxed mb-10 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Discover all the ways Elara AI can help you work smarter, 
                    not harder. From writing to coding, research to learning — 
                    experience the full power of AI assistance.
                </p>
            </div>
        </div>
    </section>

    <!-- Core Features Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Core Features
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Everything you need<br>
                        in one place.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#16A34A" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Smart Writing</h3>
                    <p class="text-gray-500 dark:text-gray-400">Create compelling content with AI assistance. From emails to essays, blog posts to books — get help with any writing task.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Code & Debug</h3>
                    <p class="text-gray-500 dark:text-gray-400">Write, review, and debug code across multiple programming languages with intelligent suggestions and explanations.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Research</h3>
                    <p class="text-gray-500 dark:text-gray-400">Dive deep into any topic with comprehensive analysis, well-sourced information, and detailed explanations.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#9333EA" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Learning</h3>
                    <p class="text-gray-500 dark:text-gray-400">Learn new concepts with explanations tailored to your level of understanding. From basics to advanced topics.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#E11D48" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Data Analysis</h3>
                    <p class="text-gray-500 dark:text-gray-400">Transform raw data into actionable insights with powerful analytical capabilities and visualizations.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center mb-5">
                        <svg width="24" height="24" fill="none" stroke="#0EA5E9" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Conversations</h3>
                    <p class="text-gray-500 dark:text-gray-400">Natural, contextual conversations that remember your context and preferences throughout your session.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Features Section -->
    <section class="py-28 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Advanced Features
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Take your productivity<br>
                    to the next level.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#6366F1" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Multi-Model Support</h3>
                <p class="text-gray-500 dark:text-gray-400">Access multiple AI models tailored to different tasks. Choose the best model for writing, coding, analysis, and more.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#10B981" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">File Analysis</h3>
                <p class="text-gray-500 dark:text-gray-400">Upload and analyze documents, PDFs, spreadsheets, and more. Get insights from your files in seconds.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#8B5CF6" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Custom Workflows</h3>
                <p class="text-gray-500 dark:text-gray-400">Create custom AI workflows for recurring tasks. Automate your repetitive work with personalized prompts.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all duration-300 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center mb-5">
                    <svg width="24" height="24" fill="none" stroke="#06B6D4" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 20V10M12 20V4M6 20v-6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Advanced Analytics</h3>
                <p class="text-gray-500 dark:text-gray-400">Track your usage patterns, productivity gains, and more with detailed analytics and insights.</p>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-12 gap-6 mb-16">
                <div class="col-span-12">
                    <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        Use Cases
                    </p>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                        One AI, endless<br>
                        applications.
                    </h2>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Content Creation</h3>
                    <p class="text-gray-500 dark:text-gray-400">Write blog posts, social media content, marketing copy, and more with AI-powered creativity.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Software Development</h3>
                    <p class="text-gray-500 dark:text-gray-400">Get help with coding, debugging, code review, and architectural decisions.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Academic Research</h3>
                    <p class="text-gray-500 dark:text-gray-400">Conduct literature reviews, summarize papers, and generate research hypotheses.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Business Analysis</h3>
                    <p class="text-gray-500 dark:text-gray-400">Analyze market trends, competitive landscapes, and business strategies.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Language Learning</h3>
                    <p class="text-gray-500 dark:text-gray-400">Practice conversations, get grammar explanations, and translate across languages.</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <h3 class="text-xl font-semibold mb-3">Personal Assistant</h3>
                    <p class="text-gray-500 dark:text-gray-400">Get help with planning, organization, and everyday tasks.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-28 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-6 mb-16">
            <div class="col-span-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-swiss-red mb-3 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    How It Works
                </p>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    Simple to start.<br>
                    Powerful results.
                </h2>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-gray-300 dark:text-gray-700 block mb-4">01</span>
                <h3 class="text-xl font-semibold mb-3">Ask anything</h3>
                <p class="text-gray-500 dark:text-gray-400">Type your question or task in natural language. No complicated prompts needed.</p>
            </div>
            
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-gray-300 dark:text-gray-700 block mb-4">02</span>
                <h3 class="text-xl font-semibold mb-3">Get instant answers</h3>
                <p class="text-gray-500 dark:text-gray-400">Receive intelligent responses tailored to your specific needs and context.</p>
            </div>
            
            <div class="p-8 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                <span class="text-6xl font-extrabold text-gray-300 dark:text-gray-700 block mb-4">03</span>
                <h3 class="text-xl font-semibold mb-3">Iterate & refine</h3>
                <p class="text-gray-500 dark:text-gray-400">Continue the conversation to refine results until you get exactly what you need.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-28 max-w-7xl mx-auto px-6">
        <div class="text-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">Ready to experience it?</h2>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-10">Start using Elara's powerful features today. It's free to get started.</p>
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
                <a href="/pricing.php" class="inline-flex items-center gap-2 px-7 py-4 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-50 font-semibold rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                    View Pricing
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 max-w-7xl mx-auto px-6">
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