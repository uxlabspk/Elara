<?php require_once '../includes/functions.php'; require_login(); ?>
<?php $settings = get_user_settings($pdo, $_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="en" class="<?= ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Chat — Elara AI</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23FF3B30'/><text x='50' y='65' font-size='50' font-family='sans-serif' font-weight='bold' text-anchor='middle' fill='white'>E</text></svg>">
    
    <style>
        html { scroll-behavior: smooth; }
        
        .msg-bubble { white-space: pre-wrap; }
        .msg-bubble pre { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 0.5rem 0; }
        .msg-bubble code { background: #e2e8f0; color: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-family: monospace; }
        .dark .msg-bubble code { background: #334155; color: #f1f5f9; }
        .msg-bubble pre code { background: transparent; padding: 0; color: inherit; }
        
        .typing-dots { display: flex; gap: 4px; padding: 8px 4px; }
        .typing-dots span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #9ca3af;
            animation: bounce 1.2s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
        }
        
        [data-animate].opacity-0 { opacity: 0; transform: translateY(20px); }
        [data-animate].transition-all { transition: all 0.7s ease-out; }
        [data-animate].\!opacity-100 { opacity: 1 !important; }
        [data-animate].\!translate-y-0 { transform: translateY(0) !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-50 transition-colors duration-300 font-sans h-screen overflow-hidden">

    <!-- Top Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-14">
        <div class="flex items-center justify-between h-full px-4">
            <a href="/" class="flex items-center gap-2 no-underline flex-shrink-0">
                <span class="text-2xl font-bold text-swiss-red">E</span>
                <span class="font-bold text-lg text-gray-900 dark:text-gray-50 hidden sm:inline">Elara</span>
            </a>
            
            <div class="flex items-center gap-2 mx-4 flex-1 justify-center">
                <button onclick="newChat()" class="flex items-center gap-2 px-4 py-2 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-swiss-red/25 transition-all text-sm">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    New Chat
                </button>
            </div>
            
            <div class="flex items-center gap-3 flex-shrink-0">
                <button onclick="toggleTheme()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle theme">
                    <svg id="theme-icon-light" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <svg id="theme-icon-dark" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5"/>
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center gap-2">
                        <?php
                        $user_avatar = get_user_avatar($pdo, $_SESSION['user_id']);
                        $user_name = get_user_name($pdo, $_SESSION['user_id']);
                        ?>
                        <img src="<?= h($user_avatar) ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 hidden z-50 border border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <p class="font-semibold text-sm"><?= h($user_name) ?></p>
                        </div>
                        <a href="/app/profile.php" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                        <a href="/app/settings.php" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                        <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex h-screen pt-14">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="conv-search" placeholder="Search..." 
                           class="w-full pl-10 pr-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:border-swiss-red"
                           oninput="filterConversations()">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-2">Recent</p>
                <ul id="conversation-list" class="space-y-1">
                    <li class="px-3 py-2 text-sm text-gray-400">Loading...</li>
                </ul>
            </div>
        </aside>

        <!-- Main Chat Area -->
        <main class="flex-1 flex flex-col bg-white dark:bg-gray-800 overflow-hidden">
            
            <!-- Welcome Screen -->
            <div id="welcome-screen" class="flex-1 flex flex-col items-center justify-center p-8 overflow-y-auto">
                <div class="text-center mb-12 max-w-xl opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <div class="mb-4">
                        <span class="text-6xl font-bold text-swiss-red">E</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Good to see you</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Your personal AI assistant — ready for any task you can imagine.</p>
                </div>

                <!-- Capability Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-12 opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <button onclick="insertPrompt('Help me write something')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Writing</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Drafts, edits & creative work</p>
                    </button>
                    
                    <button onclick="insertPrompt('Help me with programming')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Programming</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Code, debug & review</p>
                    </button>
                    
                    <button onclick="insertPrompt('Help me with research')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Research</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Analysis & deep dives</p>
                    </button>
                    
                    <button onclick="insertPrompt('Help me learn something new')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5zM6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Education</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Learn & understand</p>
                    </button>
                    
                    <button onclick="insertPrompt('Help me analyze data')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Analysis</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Data & insights</p>
                    </button>
                    
                    <button onclick="insertPrompt('Help me brainstorm')" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:-translate-y-1 transition-all text-left">
                        <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="#0ea5e9" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-1">Brainstorm</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ideas & creativity</p>
                    </button>
                </div>

                <!-- Suggestions -->
                <div class="flex flex-wrap gap-3 justify-center opacity-0 translate-y-8 transition-all duration-700" data-animate>
                    <button onclick="insertPrompt('Help me write a cover letter')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-sm hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        Help me write a cover letter
                    </button>
                    <button onclick="insertPrompt('Give me a challenging puzzle')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-sm hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        Give me a puzzle
                    </button>
                    <button onclick="insertPrompt('Explain this concept')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-sm hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        Explain a concept
                    </button>
                    <button onclick="insertPrompt('Imagine a creative scenario')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-sm hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        Imagine a scenario
                    </button>
                </div>
            </div>

            <!-- Chat Messages (hidden initially) -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 hidden flex-col gap-4"></div>

            <!-- Chat Header (hidden initially) -->
            <div id="chat-header" class="hidden border-t border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between">
                <div>
                    <h2 id="chat-title" class="font-semibold">Conversation</h2>
                    <p id="chat-date" class="text-xs text-gray-500"></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="shareChat()" class="px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Share
                    </button>
                    <button onclick="deleteChat()" class="px-3 py-1.5 border border-red-200 dark:border-red-800 text-red-600 rounded-lg text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        Delete
                    </button>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white dark:bg-gray-800">
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl p-4 transition-colors">
                    <textarea
                        id="message-input"
                        class="w-full bg-transparent border-none outline-none resize-none text-gray-900 dark:text-gray-50 placeholder-gray-400"
                        placeholder="How can I help you today?"
                        rows="1"
                        oninput="autoResize(this); syncToStorage()"
                        onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }"
                    ></textarea>
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex gap-2">
                            <button class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Attach file">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                            </button>
                            <button class="px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Switch model">
                                Elara 4.0
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </div>
                        <button onclick="sendMessage()" id="send-btn" class="flex items-center gap-2 px-4 py-2 bg-swiss-red text-white font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-swiss-red/25 transition-all">
                            Send
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 text-center mt-3">Elara can make mistakes. Please verify important information.</p>
            </div>
        </main>
    </div>

    <script>
        // Theme
        const root = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        root.classList.add(savedTheme);
        updateThemeIcon(savedTheme);

        function toggleTheme() {
            const current = root.classList.contains('dark') ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            root.classList.remove(current);
            root.classList.add(next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        }

        function updateThemeIcon(theme) {
            document.getElementById('theme-icon-light').classList.toggle('hidden', theme === 'dark');
            document.getElementById('theme-icon-dark').classList.toggle('hidden', theme === 'light');
        }

        // Auto-resize
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
        }

        // Init animations
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('!opacity-100', '!translate-y-0');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
        });

        // Prompt helper
        function insertPrompt(text) {
            const textarea = document.getElementById('message-input');
            textarea.value = text;
            textarea.focus();
            autoResize(textarea);
            showChatMode();
        }

        // Conversation state
        const conversationId = <?= isset($_GET['c']) ? (int)$_GET['c'] : 0 ?>;
        let conversations = [];

        function loadConversations() {
            fetch('history.php?action=list')
                .then(r => r.json())
                .then(data => { 
                    conversations = data; 
                    renderConversations(); 
                })
                .catch(err => console.error(err));
        }

        function renderConversations() {
            const searchTerm = document.getElementById('conv-search')?.value.toLowerCase() || '';
            const filtered = conversations.filter(c => c.title.toLowerCase().includes(searchTerm));
            const list = document.getElementById('conversation-list');

            if (!list) return;
            
            if (filtered.length === 0) {
                list.innerHTML = '<li class="px-3 py-2 text-sm text-gray-400">No conversations yet</li>';
                return;
            }

            list.innerHTML = filtered.map(c => `
                <li onclick="openConversation(${c.id})" 
                    class="px-3 py-2 rounded-lg text-sm cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 ${c.id == conversationId ? 'bg-gray-200 dark:bg-gray-700 font-medium' : ''} truncate">
                    ${escapeHtml(c.title)}
                </li>
            `).join('');
        }

        function filterConversations() {
            renderConversations();
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function openConversation(id) {
            window.location.href = 'index.php?c=' + id;
        }

        function newChat() {
            window.location.href = 'index.php';
        }

        // Chat mode
        function showChatMode() {
            document.getElementById('welcome-screen').classList.add('hidden');
            document.getElementById('chat-messages').classList.remove('hidden');
            document.getElementById('chat-messages').classList.add('flex');
            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('message-input').focus();
        }

        function loadMessages() {
            if (!conversationId) return;
            showChatMode();

            fetch(`history.php?action=messages&conversation_id=${conversationId}`)
                .then(r => r.json())
                .then(messages => {
                    const wrap = document.getElementById('chat-messages');
                    wrap.innerHTML = '';
                    messages.forEach(m => appendMessage(m.role, m.content));
                })
                .catch(err => console.error(err));
        }

        function appendMessage(role, content) {
            const wrap = document.getElementById('chat-messages');
            const row = document.createElement('div');
            row.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

            const bubble = document.createElement('div');
            bubble.className = `max-w-[75%] p-3 rounded-xl ${
                role === 'user' 
                    ? 'bg-swiss-red text-white rounded-br-sm' 
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-50 rounded-bl-sm'
            }`;
            bubble.innerHTML = content.replace(/\n/g, '<br>');

            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function appendTyping() {
            const wrap = document.getElementById('chat-messages');
            const row = document.createElement('div');
            row.className = 'flex justify-start';
            row.id = 'typing-indicator';

            const bubble = document.createElement('div');
            bubble.className = 'bg-gray-100 dark:bg-gray-700 p-3 rounded-xl rounded-bl-sm';
            bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';

            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        // Send message
        function sendMessage() {
            const textarea = document.getElementById('message-input');
            const content = textarea.value.trim();
            if (!content) return;

            if (!conversationId) showChatMode();

            appendMessage('user', content);
            textarea.value = '';
            autoResize(textarea);
            textarea.focus();

            appendTyping();
            document.getElementById('send-btn').disabled = true;

            fetch('chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    csrf_token: document.querySelector('meta[name="csrf-token"]').content,
                    message: content,
                    conversation_id: conversationId
                })
            })
            .then(r => r.json())
            .then(data => {
                removeTyping();
                document.getElementById('send-btn').disabled = false;
                if (data.error) {
                    alert(data.error);
                } else {
                    if (!conversationId) {
                        window.location.href = 'index.php?c=' + data.conversation_id;
                        return;
                    }
                    appendMessage('assistant', data.reply);
                    loadConversations();
                }
            })
            .catch(err => {
                removeTyping();
                document.getElementById('send-btn').disabled = false;
                alert('Error: ' + err);
            });
        }

        // Share/Delete
        function shareChat() {
            if (!conversationId) { alert('Start a conversation first'); return; }
            window.location.href = 'share.php';
        }

        function deleteChat() {
            if (!conversationId || !confirm('Delete this conversation?')) return;

            fetch('history.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    csrf_token: document.querySelector('meta[name="csrf-token"]').content,
                    conversation_id: conversationId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) window.location.href = 'index.php';
                else alert(data.error || 'Failed to delete');
            });
        }

        // Sync to localStorage (placeholder for draft preservation)
        function syncToStorage() {
            // Can be extended to save drafts
        }

        // Init
        loadConversations();
        if (conversationId) {
            loadMessages();
        }
    </script>
</body>
</html>