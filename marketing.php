<?php
// Marketing landing page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elara AI - Intelligent Conversational Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @layer base {
            body {
                font-family: 'Inter', sans-serif;
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Elara AI</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">
                Your intelligent conversational assistant powered by advanced AI technology
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4 mb-12">
                <a href="/app" class="bg-white text-blue-600 hover:bg-gray-100 font-medium py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105">
                    Start Chatting
                </a>
                <a href="/auth/login.php" class="border border-white/20 hover:border-white hover:bg-white/10 py-3 px-8 rounded-lg transition-all duration-300">
                    Sign In
                </a>
            </div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1677442136019-21780ecad995?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')] opacity-15"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-gray-100">Powerful Features</h2>
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Persistent Chat History</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        All your conversations are saved and easily accessible for future reference
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h3m8 9v-9M8 7l5-5 5 5M5 3a2 2 0 00-2 2v2m6 4V9a2 2 0 012-2h2.343M5 3v14c0 1.104.896 2 2 2h6c1.104 0 2-.896 2-2V5a2 2 0 00-2-2zm0 0a2 2 0 012 2v2m6-4H9a2 2 0 00-2 2v2m6-4v2a2 2 0 002 2h2v-2a2 2 0 00-2-2zm0 0a2 2 0 012 2v2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Shareable Conversations</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Generate shareable links to collaborate and share insights with others
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8V7a4 4 0 00-4-4H8a4 4 0 00-4 4v1m0 11a9 9 0 100-18 9 9 0 000 18zm-8 9a3 3 0 110-6 3 3 0 010 6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Advanced Search</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Quickly find past conversations and messages with powerful search capabilities
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.1 0-2 .9-2 2s1 2 2 2 2-.9 2-2-1-2-2-2zm0 12c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2-2 2-2 2H6c0-3.31 2.69-6 6-6zm0 16.17V16a2 2 0 012-2h2a2 2 0 012 2v.17a6 6 0 01-6 6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Custom Settings</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Personalize your experience with custom themes, models, and preferences
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a6 6 0 01-12 0v1a6 6 0 0012 0zm0 0a6 6 0 0012 0v-1a6 6 0 01-12 0zM3 9a9 9 0 019-9V5a2 2 0 00-4 0v1a2 2 0 00-2 2v3a9 9 0 00-9 9h12a3 3 0 010 6H3a3 3 0 010-6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Scalable Architecture</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Built on Laravel with MySQL for reliable performance at any scale
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold ml-3">Security Focused</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Enterprise-grade security with CSRF protection, rate limiting, and data encryption
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-gray-100">How It Works</h2>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="text-center p-6 bg-white dark:bg-gray-700 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-blue-100 dark:bg-blue-900/20 p-4 rounded-full mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.1 0-2 .9-2 2s1 2 2 2 2-.9 2-2-1-2-2-2zm0 12c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2-2 2-2 2H6c0-3.31 2.69-6 6-6zm0 16.17V16a2 2 0 012-2h2a2 2 0 012 2v.17a6 6 0 01-6 6z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">1. Sign Up</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Create your free account to get started with Elara AI
                    </p>
                </div>
                
                <div class="text-center p-6 bg-white dark:bg-gray-700 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-blue-100 dark:bg-blue-900/20 p-4 rounded-full mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.054-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">2. Start Chatting</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Begin conversations with our AI assistant on any topic
                    </p>
                </div>
                
                <div class="text-center p-6 bg-white dark:bg-gray-700 rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="bg-blue-100 dark:bg-blue-900/20 p-4 rounded-full mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.1 0-2 .9-2 2s1 2 2 2 2-.9 2-2-1-2-2-2zm0 12c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2-2 2-2 2H6c0-3.31 2.69-6 6-6zm0 16.17V16a2 2 0 012-2h2a2 2 0 012 2v.17a6 6 0 01-6 6z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">3. Explore Features</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Discover advanced features like search, sharing, and customization
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Experience Elara AI?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join thousands of users who are already benefiting from intelligent conversations
            </p>
            <a href="/app" class="bg-white text-blue-600 hover:bg-gray-100 font-bold py-4 px-12 rounded-lg transition-all duration-300 transform hover:scale-105">
                Start Chatting Now →
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 bg-gray-800 dark:bg-gray-900 text-gray-300 dark:text-gray-400">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2026 Elara AI. All rights reserved.</p>
            <div class="mt-4 space-x-4">
                <a href="#" class="hover:text-white transition-colors duration-300">Terms of Service</a>
                <a href="#" class="hover:text-white transition-colors duration-300">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors duration-300">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>