<?php require_once '../includes/functions.php'; require_login(); ?>
<?php $settings = get_user_settings($pdo, $_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="en" class="<?= ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Chat — Aivyra AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/lib/marked.umd.js"></script>
    <script>
        function copyCode(btn) {
            const codeEl = btn.previousElementSibling.querySelector('code');
            navigator.clipboard.writeText(codeEl.textContent).then(() => {
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy', 2000);
            });
        }
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['"DM Sans"', 'system-ui', 'sans-serif'],
                        'mono': ['"DM Mono"', 'monospace'],
                    },
                    colors: {
                        'accent': '#FF3B30',
                        'accent-hover': '#e0362b',
                    },
                    width: {
                        'sidebar': '260px',
                    },
                    transitionProperty: {
                        'sidebar': 'width, transform, opacity',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%23FF3B30'/><text x='50' y='68' font-size='54' font-family='sans-serif' font-weight='700' text-anchor='middle' fill='white'>E</text></svg>">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root { --accent: #FF3B30; --accent-hover: #e0362b; --accent-glow: rgba(255,59,48,0.18); }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 999px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); }

        /* Sidebar transition */
        #sidebar {
            width: 260px;
            transition: width .25s ease, transform .25s ease;
            overflow: hidden;
        }
        #sidebar.collapsed {
            width: 0px;
        }
        /* Mobile: slide out off-screen instead */
        @media (max-width: 767px) {
            #sidebar {
                position: fixed; top: 0; left: 0; height: 100%; z-index: 60;
                width: 260px !important; /* always full width on mobile */
                transform: translateX(-100%);
                transition: transform .25s ease;
            }
            #sidebar.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.12); }
        }

        /* Sidebar content fade when collapsing */
        #sidebar-inner {
            width: 260px;
            transition: opacity .2s ease;
        }
        #sidebar.collapsed #sidebar-inner { opacity: 0; pointer-events: none; }

        /* Typing dots */
        .typing-dots { display: flex; gap: 4px; padding: 4px 2px; align-items: center; }
        .typing-dots span {
            width: 6px; height: 6px; border-radius: 50%; background: #aaa;
            animation: tdot 1.2s infinite ease-in-out;
        }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes tdot {
            0%, 80%, 100% { transform: scale(1); opacity: .5; }
            40% { transform: scale(1.3); opacity: 1; }
        }

        /* Code blocks */
        .code-block-wrapper {
            position: relative;
            margin: 8px 0;
        }
        .msg-bubble pre {
            background: #1e2130; color: #e2e8f0;
            padding: 12px 14px; border-radius: 8px;
            overflow-x: auto; margin: 0;
            font-family: 'DM Mono', monospace; font-size: 13px;
            padding-right: 50px;
        }
        .copy-code-btn {
            position: absolute;
            top: 8px; right: 8px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .copy-code-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .copy-code-btn:active {
            background: rgba(255, 255, 255, 0.2);
        }
        .msg-bubble code {
            background: rgba(0,0,0,.08); padding: 1px 5px;
            border-radius: 4px; font-family: 'DM Mono', monospace; font-size: .9em;
        }
        .msg-bubble.user code { background: rgba(255,255,255,.2); }
        .msg-bubble pre code { background: transparent; padding: 0; }

        /* Markdown elements */
        .msg-bubble h1, .msg-bubble h2, .msg-bubble h3, .msg-bubble h4, .msg-bubble h5, .msg-bubble h6 {
            margin: 12px 0 8px 0; font-weight: 600; line-height: 1.3;
        }
        .msg-bubble h1 { font-size: 1.4em; }
        .msg-bubble h2 { font-size: 1.25em; }
        .msg-bubble h3 { font-size: 1.1em; }
        .msg-bubble h4, .msg-bubble h5, .msg-bubble h6 { font-size: 1em; }

        .msg-bubble p { margin: 8px 0; }
        .msg-bubble ul, .msg-bubble ol { margin: 8px 0; padding-left: 24px; }
        .msg-bubble li { margin: 4px 0; }
        .msg-bubble blockquote {
            border-left: 3px solid rgba(255,59,48,.3);
            padding-left: 12px; margin: 8px 0;
            color: rgba(0,0,0,.7);
        }
        .dark .msg-bubble blockquote { color: rgba(255,255,255,.6); }
        .msg-bubble strong { font-weight: 600; }
        .msg-bubble em { font-style: italic; }
        .msg-bubble a { color: var(--accent); text-decoration: none; }
        .msg-bubble a:hover { text-decoration: underline; }
        .msg-bubble table { border-collapse: collapse; margin: 8px 0; width: 100%; font-size: 0.95em; }
        .msg-bubble th, .msg-bubble td { border: 1px solid rgba(0,0,0,.1); padding: 6px 8px; text-align: left; }
        .dark .msg-bubble th, .dark .msg-bubble td { border-color: rgba(255,255,255,.1); }
        .msg-bubble th { background: rgba(0,0,0,.04); font-weight: 600; }
        .dark .msg-bubble th { background: rgba(255,255,255,.05); }

        /* Fade-up animation */
        .fade-up { opacity: 0; transform: translateY(16px); transition: opacity .5s ease, transform .5s ease; }
        .fade-up.in { opacity: 1; transform: translateY(0); }

        /* Textarea auto-resize */
        #message-input { resize: none; max-height: 180px; }

        /* Chip card hover */
        .chip-card { transition: border-color .15s, box-shadow .15s, transform .15s; }
        .chip-card:hover { transform: translateY(-1px); }
    </style>
</head>
<body class="bg-white dark:bg-[#141414] text-gray-900 dark:text-gray-50 font-sans h-screen overflow-hidden flex">

    <!-- ─── Sidebar ─── -->
    <aside id="sidebar" class="bg-[#f9f9f8] dark:bg-[#1a1a1a] border-r border-[#ebebea] dark:border-[#2a2a2a] flex flex-col flex-shrink-0">
        <div id="sidebar-inner" class="flex flex-col h-full">

            <!-- Logo + collapse toggle -->
            <div class="flex items-center gap-2.5 px-4 pt-[18px] pb-3">
                <!-- <div class="w-[30px] h-[30px] rounded-lg flex items-center justify-center text-white font-bold text-[17px] flex-shrink-0" style="background:var(--accent)">E</div> -->
                <img src="../assets/images/logo.png" alt="aivyra logo" class="w-7" />
                <span class="font-bold text-[15px] tracking-tight dark:text-gray-100 flex-1">Aivyra</span>
                <!-- Desktop collapse button -->
                <button onclick="toggleSidebar()" id="collapse-btn"
                    class="hidden md:flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                    title="Collapse sidebar">
                    <svg id="collapse-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- New Chat -->
            <button onclick="newChat()" class="mx-2.5 mb-2.5 flex items-center gap-2 px-3 py-2 bg-transparent border border-[#e2e2e0] dark:border-[#2e2e2e] rounded-xl text-[13.5px] font-medium cursor-pointer text-[#333] dark:text-[#e8e8e6] hover:bg-[#f0f0ef] dark:hover:bg-[#242424] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] transition-colors w-[calc(100%-20px)]">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New conversation
            </button>

            <!-- Search -->
            <div class="relative px-2.5 pb-2">
                <svg class="absolute left-[22px] top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="conv-search" placeholder="Search conversations…" oninput="filterConversations()"
                    class="w-full pl-8 pr-3 py-1.5 bg-[#f0f0ef] dark:bg-[#252525] text-[#333] dark:text-[#e0e0de] border border-transparent rounded-lg text-[13px] outline-none focus:bg-white dark:focus:bg-[#2a2a2a] focus:border-[#d4d4d2] dark:focus:border-[#3a3a3a] transition-colors placeholder-gray-400">
            </div>

            <!-- Conversation list -->
            <div class="flex-1 overflow-y-auto">
                <p class="px-4 pt-2 pb-1 text-[11px] font-semibold tracking-widest uppercase text-[#aaa] dark:text-[#555]">Recent</p>
                <ul id="conversation-list" class="list-none p-0 m-0 px-1.5">
                    <li class="px-2.5 py-2 text-[13px] text-[#aaa]">Loading…</li>
                </ul>
            </div>

            <!-- User footer -->
            <div class="mt-auto pt-3 pb-3 px-2.5 border-t border-[#ebebea] dark:border-[#2a2a2a]">
                <div class="relative">
                    <div onclick="toggleUserMenu()" id="sidebar-user-btn"
                        class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-[#ededed] dark:hover:bg-[#252525] transition-colors">
                        <?php
                            $user_avatar = get_user_avatar($pdo, $_SESSION['user_id']);
                            $user_name = get_user_name($pdo, $_SESSION['user_id']);
                        ?>
                        <img src="<?= h($user_avatar) ?>" alt="Avatar" class="w-[30px] h-[30px] rounded-full flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#222] dark:text-[#ddd] truncate"><?= h($user_name) ?></div>
                        </div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-gray-400 flex-shrink-0"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </div>

                    <!-- Dropdown -->
                    <div id="user-dropdown" class="hidden absolute bottom-[calc(100%+6px)] left-0 right-0 bg-white dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2e2e2e] rounded-xl shadow-xl overflow-hidden z-50">
                        <!-- <a href="/app/profile.php" class="block px-3.5 py-2.5 text-[13px] text-[#444] dark:text-[#bbb] hover:bg-[#f4f4f3] dark:hover:bg-[#252525] transition-colors">Profile</a>
                        <a href="/app/settings.php" class="block px-3.5 py-2.5 text-[13px] text-[#444] dark:text-[#bbb] hover:bg-[#f4f4f3] dark:hover:bg-[#252525] transition-colors">Settings</a> -->
                        <button onclick="toggleTheme()" class="w-full flex items-center gap-2 px-3.5 py-2.5 text-[13px] text-[#888] hover:bg-[#f4f4f3] dark:hover:bg-[#252525] transition-colors bg-transparent border-none cursor-pointer">
                            <svg id="theme-icon-light" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                            <svg id="theme-icon-dark" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                            <span id="theme-label">Dark mode</span>
                        </button>
                        <div class="h-px bg-[#ebebea] dark:bg-[#2a2a2a] my-1"></div>
                        <a href="/auth/logout.php" class="block px-3.5 py-2.5 text-[13px] text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                            <span class="flex items-center justify-content-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.9999 2C10.2385 2 7.99991 4.23858 7.99991 7C7.99991 7.55228 8.44762 8 8.99991 8C9.55219 8 9.99991 7.55228 9.99991 7C9.99991 5.34315 11.3431 4 12.9999 4H16.9999C18.6568 4 19.9999 5.34315 19.9999 7V17C19.9999 18.6569 18.6568 20 16.9999 20H12.9999C11.3431 20 9.99991 18.6569 9.99991 17C9.99991 16.4477 9.55219 16 8.99991 16C8.44762 16 7.99991 16.4477 7.99991 17C7.99991 19.7614 10.2385 22 12.9999 22H16.9999C19.7613 22 21.9999 19.7614 21.9999 17V7C21.9999 4.23858 19.7613 2 16.9999 2H12.9999Z" fill="#FF3B30"/>
                                    <path d="M13.9999 11C14.5522 11 14.9999 11.4477 14.9999 12C14.9999 12.5523 14.5522 13 13.9999 13V11Z" fill="#FF3B30"/>
                                    <path d="M5.71783 11C5.80685 10.8902 5.89214 10.7837 5.97282 10.682C6.21831 10.3723 6.42615 10.1004 6.57291 9.90549C6.64636 9.80795 6.70468 9.72946 6.74495 9.67492L6.79152 9.61162L6.804 9.59454L6.80842 9.58848C6.80846 9.58842 6.80892 9.58778 5.99991 9L6.80842 9.58848C7.13304 9.14167 7.0345 8.51561 6.58769 8.19098C6.14091 7.86637 5.51558 7.9654 5.19094 8.41215L5.18812 8.41602L5.17788 8.43002L5.13612 8.48679C5.09918 8.53682 5.04456 8.61033 4.97516 8.7025C4.83623 8.88702 4.63874 9.14542 4.40567 9.43937C3.93443 10.0337 3.33759 10.7481 2.7928 11.2929L2.08569 12L2.7928 12.7071C3.33759 13.2519 3.93443 13.9663 4.40567 14.5606C4.63874 14.8546 4.83623 15.113 4.97516 15.2975C5.04456 15.3897 5.09918 15.4632 5.13612 15.5132L5.17788 15.57L5.18812 15.584L5.19045 15.5872C5.51509 16.0339 6.14091 16.1336 6.58769 15.809C7.0345 15.4844 7.13355 14.859 6.80892 14.4122L5.99991 15C6.80892 14.4122 6.80897 14.4123 6.80892 14.4122L6.804 14.4055L6.79152 14.3884L6.74495 14.3251C6.70468 14.2705 6.64636 14.1921 6.57291 14.0945C6.42615 13.8996 6.21831 13.6277 5.97282 13.318C5.89214 13.2163 5.80685 13.1098 5.71783 13H13.9999V11H5.71783Z" fill="#FF3B30"/>
                                </svg>
                                Log out
                            </span>
                        </a>
                    </div>
                </div>
            </div>

        </div><!-- /sidebar-inner -->
    </aside>

    <!-- Mobile overlay -->
    <div id="overlay" onclick="closeSidebar()" class="hidden fixed inset-0 z-[55] bg-black/35"></div>

    <!-- ─── Main ─── -->
    <main id="main" class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-[#141414] min-w-0">

        <!-- Mobile topbar -->
        <div id="topbar" class="md:hidden flex items-center justify-between px-4 h-[52px] border-b border-[#ebebea] dark:border-[#2a2a2a] flex-shrink-0">
            <button onclick="openSidebar()" class="p-1.5 bg-transparent border-none cursor-pointer text-gray-600 dark:text-gray-400">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <button onclick="newChat()" class="px-3 py-1.5 text-white border-none rounded-lg text-[13px] font-semibold cursor-pointer" style="background:var(--accent)">New</button>
        </div>

        <!-- Collapsed sidebar toggle (desktop, shown when sidebar is collapsed) -->
        <div id="expand-btn-wrap" class="hidden fixed left-3 top-3 z-50">
            <button onclick="toggleSidebar()"
                class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg bg-[#f9f9f8] dark:bg-[#1a1a1a] border border-[#ebebea] dark:border-[#2a2a2a] text-gray-500 hover:bg-[#f0f0ef] dark:hover:bg-[#242424] shadow-sm transition-colors"
                title="Expand sidebar">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Chat header strip -->
        <div id="chat-header" class="hidden items-center justify-between px-6 py-2.5 border-b border-[#ebebea] dark:border-[#2a2a2a] flex-shrink-0">
            <div>
                <div id="chat-title" class="text-[14px] font-semibold text-[#222] dark:text-[#ddd]">Conversation</div>
                <div id="chat-date" class="text-[12px] text-[#aaa]"></div>
            </div>
            <div class="flex gap-2">
                <button onclick="shareChat()" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-[#e0e0de] dark:border-[#2e2e2e] bg-transparent cursor-pointer text-[#555] dark:text-[#999] hover:bg-[#f4f4f3] dark:hover:bg-[#222] hover:border-[#ccc] transition-colors">Share</button>
                <button onclick="deleteChat()" class="px-3 py-1.5 rounded-lg text-[13px] font-medium border border-[#fecaca] dark:border-[#7f1d1d] bg-transparent cursor-pointer text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-[#1c1010] transition-colors">Delete</button>
            </div>
        </div>

        <!-- Welcome screen -->
        <div id="welcome-screen" class="flex-1 overflow-y-auto flex flex-col items-center justify-center px-6 py-10">
            <div class="fade-up">
                <h1 class="text-[clamp(28px,4vw,40px)] font-bold tracking-tight text-[#111] dark:text-[#f0f0ee] text-center m-0 mb-2">Think bigger — Create faster.</h1>
                <p class="text-[15px] text-[#888] text-center m-0 mb-9">Ask me anything — I'm ready when you are.</p>
            </div>

            <!-- Prompt pills -->
            <div class="fade-up flex flex-wrap gap-2 justify-center max-w-[580px] mb-6" style="transition-delay:.16s;">
                <button onclick="insertPrompt('Help me write a cover letter')" class="px-3.5 py-1.5 bg-[#f5f5f4] dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2a2a2a] rounded-full text-[13px] text-[#555] dark:text-[#aaa] cursor-pointer hover:bg-[#eeeeec] dark:hover:bg-[#262626] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] hover:text-[#222] dark:hover:text-[#ddd] transition-colors">Cover letter</button>
                <button onclick="insertPrompt('Give me a challenging puzzle')" class="px-3.5 py-1.5 bg-[#f5f5f4] dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2a2a2a] rounded-full text-[13px] text-[#555] dark:text-[#aaa] cursor-pointer hover:bg-[#eeeeec] dark:hover:bg-[#262626] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] hover:text-[#222] dark:hover:text-[#ddd] transition-colors">Puzzle</button>
                <button onclick="insertPrompt('Explain a complex concept simply')" class="px-3.5 py-1.5 bg-[#f5f5f4] dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2a2a2a] rounded-full text-[13px] text-[#555] dark:text-[#aaa] cursor-pointer hover:bg-[#eeeeec] dark:hover:bg-[#262626] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] hover:text-[#222] dark:hover:text-[#ddd] transition-colors">Explain something</button>
                <button onclick="insertPrompt('Help me analyze this data')" class="px-3.5 py-1.5 bg-[#f5f5f4] dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2a2a2a] rounded-full text-[13px] text-[#555] dark:text-[#aaa] cursor-pointer hover:bg-[#eeeeec] dark:hover:bg-[#262626] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] hover:text-[#222] dark:hover:text-[#ddd] transition-colors">Analyze data</button>
                <button onclick="insertPrompt('Write a short story')" class="px-3.5 py-1.5 bg-[#f5f5f4] dark:bg-[#1e1e1e] border border-[#e8e8e6] dark:border-[#2a2a2a] rounded-full text-[13px] text-[#555] dark:text-[#aaa] cursor-pointer hover:bg-[#eeeeec] dark:hover:bg-[#262626] hover:border-[#d4d4d2] dark:hover:border-[#3a3a3a] hover:text-[#222] dark:hover:text-[#ddd] transition-colors">Short story</button>
            </div>
        </div>

        <!-- Chat messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto py-7 flex-col hidden"></div>

        <!-- Input area -->
        <div id="input-area" class="px-6 pt-3.5 pb-[18px] flex-shrink-0">
            <div class="max-w-[720px] mx-auto bg-[#f5f5f4] dark:bg-[#1e1e1e] border-[1.5px] border-[#e4e4e2] dark:border-[#2e2e2e] rounded-2xl overflow-hidden focus-within:border-[#c8c8c6] dark:focus-within:border-[#3e3e3e] focus-within:shadow-[0_0_0_3px_rgba(0,0,0,0.06)] dark:focus-within:shadow-[0_0_0_3px_rgba(255,255,255,0.04)] transition-all">
                <textarea
                    id="message-input"
                    placeholder="Message Aivyra…"
                    rows="1"
                    oninput="autoResize(this)"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                    class="block w-full bg-transparent border-none outline-none px-4 pt-3.5 pb-1.5 font-sans text-[14.5px] text-[#1a1a1a] dark:text-[#e8e8e6] leading-[1.55] placeholder-[#bbb] dark:placeholder-[#555]"
                ></textarea>
                <div class="flex items-center justify-between px-2.5 pb-2.5 pt-1.5">
                    <div class="flex gap-1 items-center">
                        <!-- <button class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg border-none bg-transparent cursor-pointer text-[#999] hover:bg-[#ebebea] dark:hover:bg-[#2a2a2a] hover:text-[#555] dark:hover:text-[#aaa] transition-colors text-[12.5px] font-medium" title="Attach file">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button> -->
                        <button class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-[#e0e0de] dark:border-[#2e2e2e] bg-transparent cursor-pointer text-[#777] dark:text-[#666] text-[12px] font-medium hover:bg-[#ebebea] dark:hover:bg-[#2a2a2a] hover:border-[#d0d0ce] transition-colors" title="Switch model">
                            Aivyra 2.0
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </div>
                    <button onclick="sendMessage()" id="send-btn"
                        class="w-[34px] h-[34px] rounded-[9px] border-none cursor-pointer text-white flex items-center justify-center flex-shrink-0 transition-all hover:scale-105 disabled:cursor-not-allowed disabled:scale-100 disabled:opacity-50"
                        style="background:var(--accent);" title="Send">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </div>
            </div>
            <p class="text-center text-[11.5px] text-[#c0c0be] dark:text-[#444] mt-2 max-w-[720px] mx-auto">Aivyra can make mistakes. Verify important information.</p>
        </div>
    </main>

    <script>
        /* ─── Theme ─── */
        const root = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        root.classList.add(savedTheme);
        applyThemeIcon(savedTheme);

        function toggleTheme() {
            const cur = root.classList.contains('dark') ? 'dark' : 'light';
            const next = cur === 'dark' ? 'light' : 'dark';
            root.classList.replace(cur, next);
            localStorage.setItem('theme', next);
            applyThemeIcon(next);
        }
        function applyThemeIcon(t) {
            document.getElementById('theme-icon-light').classList.toggle('hidden', t === 'dark');
            document.getElementById('theme-icon-dark').classList.toggle('hidden', t !== 'dark');
            const lbl = document.getElementById('theme-label');
            if (lbl) lbl.textContent = t === 'dark' ? 'Light mode' : 'Dark mode';
        }

        /* ─── Sidebar collapse (desktop) ─── */
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

        function applySidebarState(animate) {
            const sidebar = document.getElementById('sidebar');
            const expandBtnWrap = document.getElementById('expand-btn-wrap');
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                expandBtnWrap.classList.remove('hidden');
            } else {
                sidebar.classList.remove('collapsed');
                expandBtnWrap.classList.add('hidden');
            }
        }

        function toggleSidebar() {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            applySidebarState(true);
        }

        // Apply on load (no animation flash)
        applySidebarState(false);

        /* ─── Mobile sidebar ─── */
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.add('hidden');
        }

        /* ─── Textarea auto-resize ─── */
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 180) + 'px';
        }

        /* ─── Fade-up init ─── */
        requestAnimationFrame(() => {
            document.querySelectorAll('.fade-up').forEach((el, i) => {
                setTimeout(() => el.classList.add('in'), 80 + i * 80);
            });
        });

        /* ─── User menu ─── */
        function toggleUserMenu() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', e => {
            const btn = document.getElementById('sidebar-user-btn');
            const menu = document.getElementById('user-dropdown');
            if (btn && !btn.contains(e.target)) menu.classList.add('hidden');
        });

        /* ─── Insert prompt ─── */
        function insertPrompt(text) {
            const ta = document.getElementById('message-input');
            ta.value = text;
            ta.focus();
            autoResize(ta);
        }

        /* ─── Conversation state ─── */
        const conversationId = <?= isset($_GET['c']) ? (int)$_GET['c'] : 0 ?>;
        let conversations = [];

        function loadConversations() {
            fetch('history.php?action=list')
                .then(r => r.json())
                .then(data => { conversations = data; renderConversations(); })
                .catch(console.error);
        }

        function renderConversations() {
            const term = (document.getElementById('conv-search')?.value || '').toLowerCase();
            const list = document.getElementById('conversation-list');
            if (!list) return;
            const filtered = conversations.filter(c => c.title.toLowerCase().includes(term));
            if (!filtered.length) {
                list.innerHTML = '<li class="px-2.5 py-2 text-[13px] text-[#aaa]">No conversations</li>';
                return;
            }
            list.innerHTML = filtered.map(c => `
                <li onclick="openConversation(${c.id})"
                    class="px-2.5 py-2 rounded-lg text-[13.5px] cursor-pointer text-[#444] dark:text-[#bbb] whitespace-nowrap overflow-hidden text-ellipsis transition-colors mb-px hover:bg-[#ededed] dark:hover:bg-[#252525] ${c.id == conversationId ? 'bg-[#e8e8e6] dark:bg-[#2a2a2a] font-medium text-[#111] dark:text-[#eee]' : ''}">
                    ${escapeHtml(c.title)}
                </li>`).join('');
        }

        function filterConversations() { renderConversations(); }
        function escapeHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
        function openConversation(id) { window.location.href = 'index.php?c=' + id; }
        function newChat() { window.location.href = 'index.php'; }

        /* ─── Show chat mode ─── */
        function showChatMode() {
            document.getElementById('welcome-screen').style.display = 'none';
            const msgs = document.getElementById('chat-messages');
            msgs.classList.remove('hidden');
            msgs.classList.add('flex');
            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('chat-header').classList.add('flex');
        }

        /* ─── Messages ─── */
        function loadMessages() {
            if (!conversationId) return;
            showChatMode();
            fetch(`history.php?action=messages&conversation_id=${conversationId}`)
                .then(r => r.json())
                .then(msgs => {
                    const wrap = document.getElementById('chat-messages');
                    wrap.innerHTML = '';
                    msgs.forEach(m => appendMessage(m.role, m.content));
                })
                .catch(console.error);
        }

        function appendMessage(role, content) {
            const wrap = document.getElementById('chat-messages');
            const row = document.createElement('div');
            row.className = `flex py-1.5 max-w-[720px] mx-auto w-full px-6 ${role === 'user' ? 'justify-end' : 'justify-start'}`;

            const bubble = document.createElement('div');
            bubble.className = `msg-bubble max-w-[78%] px-3.5 py-2.5 text-[14.5px] leading-[1.65] rounded-2xl ${
                role === 'user'
                    ? 'text-white rounded-br-[4px]'
                    : 'bg-[#f4f4f3] dark:bg-[#242424] text-[#1a1a1a] dark:text-[#e8e8e6] rounded-bl-[4px]'
            } ${role}`;
            if (role === 'user') bubble.style.background = 'var(--accent)';

            if (role === 'assistant') {
                bubble.innerHTML = marked.parse(content);
                // Add copy buttons to code blocks
                bubble.querySelectorAll('pre').forEach(pre => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'code-block-wrapper';
                    const btn = document.createElement('button');
                    btn.className = 'copy-code-btn';
                    btn.textContent = 'Copy';
                    btn.onclick = function() { copyCode(this); };
                    wrapper.appendChild(pre.cloneNode(true));
                    wrapper.appendChild(btn);
                    pre.replaceWith(wrapper);
                });
            } else {
                bubble.innerHTML = content.replace(/\n/g, '<br>');
                bubble.style.whiteSpace = 'pre-wrap';
            }

            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function appendTyping() {
            const wrap = document.getElementById('chat-messages');
            const row = document.createElement('div');
            row.className = 'flex py-1.5 max-w-[720px] mx-auto w-full px-6 justify-start';
            row.id = 'typing-indicator';
            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble max-w-[78%] px-3.5 py-2.5 rounded-2xl rounded-bl-[4px] bg-[#f4f4f3] dark:bg-[#242424]';
            bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        /* ─── Send ─── */
        function sendMessage() {
            const ta = document.getElementById('message-input');
            const content = ta.value.trim();
            if (!content) return;

            if (!conversationId) showChatMode();

            appendMessage('user', content);
            ta.value = '';
            autoResize(ta);
            ta.focus();

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

        /* ─── Share / Delete ─── */
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
            .then(d => { if (d.success) window.location.href = 'index.php'; else alert(d.error || 'Failed'); });
        }

        /* ─── Init ─── */
        loadConversations();
        if (conversationId) loadMessages();
    </script>
</body>
</html>