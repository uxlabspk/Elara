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
                        'sans': ['"DM Sans"', 'system-ui', 'sans-serif'],
                        'mono': ['"DM Mono"', 'monospace'],
                    },
                    colors: {
                        'accent': '#FF3B30',
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

        :root {
            --accent: #FF3B30;
            --accent-hover: #e0362b;
            --accent-glow: rgba(255,59,48,0.18);
            --sidebar-w: 260px;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 999px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); }

        /* ─── Base ─── */
        html { scroll-behavior: smooth; }
        body { font-family: 'DM Sans', sans-serif; }

        /* ─── Sidebar ─── */
        #sidebar {
            width: var(--sidebar-w);
            background: #f9f9f8;
            border-right: 1px solid #ebebea;
            display: flex;
            flex-direction: column;
            transition: transform .25s ease;
        }
        .dark #sidebar {
            background: #1a1a1a;
            border-right-color: #2a2a2a;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 16px 12px;
        }
        .logo-mark {
            width: 30px; height: 30px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 17px; color: white; flex-shrink: 0;
        }
        .logo-text { font-weight: 700; font-size: 15px; letter-spacing: -.3px; }

        .new-chat-btn {
            margin: 4px 10px 10px;
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid #e2e2e0;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            color: #333;
            transition: background .15s, border-color .15s;
            width: calc(100% - 20px);
        }
        .new-chat-btn:hover { background: #f0f0ef; border-color: #d4d4d2; }
        .dark .new-chat-btn { color: #e8e8e6; border-color: #2e2e2e; }
        .dark .new-chat-btn:hover { background: #242424; border-color: #3a3a3a; }

        .search-wrap {
            padding: 0 10px 8px;
        }
        .search-wrap input {
            width: 100%;
            padding: 7px 10px 7px 32px;
            background: #f0f0ef;
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 13px;
            color: #333;
            outline: none;
            transition: border-color .15s, background .15s;
        }
        .search-wrap input:focus { background: white; border-color: #d4d4d2; }
        .dark .search-wrap input { background: #252525; color: #e0e0de; }
        .dark .search-wrap input:focus { background: #2a2a2a; border-color: #3a3a3a; }
        .search-wrap svg { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none; }
        .search-wrap { position: relative; }

        .conv-section-label {
            padding: 8px 16px 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #aaa;
        }
        .dark .conv-section-label { color: #555; }

        #conversation-list { list-style: none; padding: 0 6px; margin: 0; }
        #conversation-list li {
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13.5px;
            cursor: pointer;
            color: #444;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: background .12s;
            margin-bottom: 1px;
        }
        #conversation-list li:hover { background: #ededed; }
        #conversation-list li.active { background: #e8e8e6; font-weight: 500; color: #111; }
        .dark #conversation-list li { color: #bbb; }
        .dark #conversation-list li:hover { background: #252525; }
        .dark #conversation-list li.active { background: #2a2a2a; color: #eee; }

        /* ─── Sidebar footer ─── */
        .sidebar-footer {
            margin-top: auto;
            padding: 12px 10px;
            border-top: 1px solid #ebebea;
        }
        .dark .sidebar-footer { border-top-color: #2a2a2a; }
        .sidebar-user {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: background .12s;
            position: relative;
        }
        .sidebar-user:hover { background: #ededed; }
        .dark .sidebar-user:hover { background: #252525; }
        .sidebar-user img { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-size: 13px; font-weight: 500; color: #222; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dark .sidebar-user-name { color: #ddd; }

        /* ─── Main area ─── */
        #main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
        }
        .dark #main { background: #141414; }

        /* ─── Topbar (mobile only) ─── */
        #topbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: 52px;
            border-bottom: 1px solid #ebebea;
            flex-shrink: 0;
        }
        .dark #topbar { border-bottom-color: #2a2a2a; }
        @media (max-width: 767px) {
            #sidebar { position: fixed; top: 0; left: 0; height: 100%; z-index: 60; transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.12); }
            #topbar { display: flex; }
            .overlay { display: block; }
        }

        #overlay {
            display: none;
            position: fixed; inset: 0; z-index: 55;
            background: rgba(0,0,0,.35);
        }
        #overlay.visible { display: block; }

        /* ─── Welcome ─── */
        #welcome-screen {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .welcome-heading {
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 700;
            letter-spacing: -.5px;
            color: #111;
            text-align: center;
            margin: 0 0 8px;
        }
        .dark .welcome-heading { color: #f0f0ee; }
        .welcome-sub {
            font-size: 15px;
            color: #888;
            text-align: center;
            margin: 0 0 36px;
        }

        /* ─── Suggestion chips ─── */
        .chip-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            width: 100%;
            max-width: 560px;
            margin-bottom: 24px;
        }
        @media (min-width: 480px) { .chip-grid { grid-template-columns: repeat(2, 1fr); } }

        .chip-card {
            background: #fafaf9;
            border: 1px solid #e8e8e6;
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            text-align: left;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        .chip-card:hover {
            border-color: #ccc;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            transform: translateY(-1px);
        }
        .dark .chip-card { background: #1e1e1e; border-color: #2a2a2a; }
        .dark .chip-card:hover { border-color: #3a3a3a; box-shadow: 0 2px 16px rgba(0,0,0,.3); }

        .chip-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 10px;
        }
        .chip-title { font-size: 14px; font-weight: 600; color: #222; margin-bottom: 2px; }
        .dark .chip-title { color: #e0e0de; }
        .chip-desc { font-size: 12px; color: #999; }

        .prompt-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            max-width: 580px;
        }
        .prompt-pill {
            padding: 7px 14px;
            background: #f5f5f4;
            border: 1px solid #e8e8e6;
            border-radius: 999px;
            font-size: 13px;
            color: #555;
            cursor: pointer;
            transition: background .12s, border-color .12s;
        }
        .prompt-pill:hover { background: #eeeeec; border-color: #d4d4d2; color: #222; }
        .dark .prompt-pill { background: #1e1e1e; border-color: #2a2a2a; color: #aaa; }
        .dark .prompt-pill:hover { background: #262626; border-color: #3a3a3a; color: #ddd; }

        /* ─── Chat messages ─── */
        #chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 28px 0;
            display: none;
            flex-direction: column;
        }
        #chat-messages.visible { display: flex; }

        .msg-row {
            display: flex;
            padding: 6px 0;
            max-width: 720px;
            margin: 0 auto;
            width: 100%;
            padding-left: 24px;
            padding-right: 24px;
        }
        .msg-row.user { justify-content: flex-end; }
        .msg-row.assistant { justify-content: flex-start; }

        .msg-bubble {
            max-width: 78%;
            padding: 10px 14px;
            font-size: 14.5px;
            line-height: 1.65;
            white-space: pre-wrap;
            border-radius: 16px;
        }
        .msg-bubble.user {
            background: var(--accent);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .msg-bubble.assistant {
            background: #f4f4f3;
            color: #1a1a1a;
            border-bottom-left-radius: 4px;
        }
        .dark .msg-bubble.assistant { background: #242424; color: #e8e8e6; }

        .msg-bubble pre {
            background: #1e2130;
            color: #e2e8f0;
            padding: 12px 14px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 8px 0;
            font-family: 'DM Mono', monospace;
            font-size: 13px;
        }
        .msg-bubble code {
            background: rgba(0,0,0,.08);
            color: inherit;
            padding: 1px 5px;
            border-radius: 4px;
            font-family: 'DM Mono', monospace;
            font-size: .9em;
        }
        .msg-bubble.user code { background: rgba(255,255,255,.2); }
        .msg-bubble pre code { background: transparent; padding: 0; }

        /* ─── Typing ─── */
        .typing-dots { display: flex; gap: 4px; padding: 4px 2px; align-items: center; }
        .typing-dots span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #aaa;
            animation: tdot 1.2s infinite ease-in-out;
        }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes tdot {
            0%, 80%, 100% { transform: scale(1); opacity: .5; }
            40% { transform: scale(1.3); opacity: 1; }
        }

        /* ─── Chat header strip ─── */
        #chat-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            border-bottom: 1px solid #ebebea;
            flex-shrink: 0;
        }
        #chat-header.visible { display: flex; }
        .dark #chat-header { border-bottom-color: #2a2a2a; }
        #chat-title { font-size: 14px; font-weight: 600; color: #222; }
        .dark #chat-title { color: #ddd; }
        #chat-date { font-size: 12px; color: #aaa; margin-top: 1px; }

        .header-btn {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e0e0de;
            background: transparent;
            cursor: pointer;
            color: #555;
            transition: background .12s, border-color .12s;
        }
        .header-btn:hover { background: #f4f4f3; border-color: #ccc; }
        .header-btn.danger { color: #dc2626; border-color: #fecaca; }
        .header-btn.danger:hover { background: #fef2f2; }
        .dark .header-btn { border-color: #2e2e2e; color: #999; }
        .dark .header-btn:hover { background: #222; }
        .dark .header-btn.danger { border-color: #7f1d1d; color: #f87171; }
        .dark .header-btn.danger:hover { background: #1c1010; }

        /* ─── Input area ─── */
        #input-area {
            padding: 14px 24px 18px;
            flex-shrink: 0;
        }

        .input-shell {
            max-width: 720px;
            margin: 0 auto;
            background: #f5f5f4;
            border: 1.5px solid #e4e4e2;
            border-radius: 16px;
            transition: border-color .15s, box-shadow .15s;
            overflow: hidden;
        }
        .dark .input-shell { background: #1e1e1e; border-color: #2e2e2e; }
        .input-shell:focus-within {
            border-color: #c8c8c6;
            box-shadow: 0 0 0 3px rgba(0,0,0,.06);
        }
        .dark .input-shell:focus-within {
            border-color: #3e3e3e;
            box-shadow: 0 0 0 3px rgba(255,255,255,.04);
        }

        #message-input {
            display: block;
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            resize: none;
            padding: 14px 16px 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14.5px;
            color: #1a1a1a;
            line-height: 1.55;
            max-height: 180px;
        }
        .dark #message-input { color: #e8e8e6; }
        #message-input::placeholder { color: #bbb; }
        .dark #message-input::placeholder { color: #555; }

        .input-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px 10px;
        }

        .toolbar-left { display: flex; gap: 4px; align-items: center; }

        .tool-btn {
            padding: 5px 8px;
            border-radius: 7px;
            border: none;
            background: transparent;
            cursor: pointer;
            color: #999;
            transition: background .12s, color .12s;
            display: flex; align-items: center; gap: 5px;
            font-size: 12.5px; font-weight: 500;
        }
        .tool-btn:hover { background: #ebebea; color: #555; }
        .dark .tool-btn:hover { background: #2a2a2a; color: #aaa; }

        .model-chip {
            padding: 4px 10px;
            border-radius: 7px;
            border: 1px solid #e0e0de;
            background: transparent;
            cursor: pointer;
            color: #777;
            font-size: 12px;
            font-weight: 500;
            display: flex; align-items: center; gap: 4px;
            transition: background .12s, border-color .12s;
        }
        .model-chip:hover { background: #ebebea; border-color: #d0d0ce; }
        .dark .model-chip { border-color: #2e2e2e; color: #666; }
        .dark .model-chip:hover { background: #2a2a2a; }

        #send-btn {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: var(--accent);
            border: none;
            cursor: pointer;
            color: white;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s, transform .15s, box-shadow .15s;
            flex-shrink: 0;
        }
        #send-btn:hover { background: var(--accent-hover); transform: scale(1.05); box-shadow: 0 2px 12px var(--accent-glow); }
        #send-btn:disabled { background: #d4d4d2; cursor: not-allowed; transform: none; box-shadow: none; }
        .dark #send-btn:disabled { background: #333; }

        .input-hint { text-align: center; font-size: 11.5px; color: #c0c0be; margin-top: 8px; max-width: 720px; margin-left: auto; margin-right: auto; }
        .dark .input-hint { color: #444; }

        /* ─── User dropdown ─── */
        #user-dropdown {
            position: absolute;
            bottom: calc(100% + 6px); left: 0; right: 0;
            background: white;
            border: 1px solid #e8e8e6;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
            overflow: hidden;
            z-index: 100;
        }
        .dark #user-dropdown { background: #1e1e1e; border-color: #2e2e2e; box-shadow: 0 8px 28px rgba(0,0,0,.4); }
        .dropdown-item {
            display: block; padding: 9px 14px;
            font-size: 13px; color: #444;
            text-decoration: none;
            transition: background .1s;
        }
        .dropdown-item:hover { background: #f4f4f3; }
        .dark .dropdown-item { color: #bbb; }
        .dark .dropdown-item:hover { background: #252525; }
        .dropdown-item.danger { color: #dc2626; }
        .dark .dropdown-item.danger { color: #f87171; }
        .dropdown-divider { height: 1px; background: #ebebea; margin: 3px 0; }
        .dark .dropdown-divider { background: #2a2a2a; }

        /* ─── Fade-in animations ─── */
        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .5s ease, transform .5s ease;
        }
        .fade-up.in { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="bg-white dark:bg-[#141414] text-gray-900 dark:text-gray-50 font-sans h-screen overflow-hidden flex">

    <!-- Sidebar -->
    <aside id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="logo-mark">E</div>
            <span class="logo-text dark:text-gray-100">Elara</span>
        </div>

        <!-- New Chat -->
        <button onclick="newChat()" class="new-chat-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            New conversation
        </button>

        <!-- Search -->
        <div class="search-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="conv-search" placeholder="Search conversations…" oninput="filterConversations()">
        </div>

        <!-- Conversation List -->
        <div style="flex:1;overflow-y:auto;">
            <p class="conv-section-label">Recent</p>
            <ul id="conversation-list">
                <li style="padding:8px 10px;font-size:13px;color:#aaa;">Loading…</li>
            </ul>
        </div>

        <!-- User Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user" onclick="toggleUserMenu()" id="sidebar-user-btn">
                <?php
                    $user_avatar = get_user_avatar($pdo, $_SESSION['user_id']);
                    $user_name = get_user_name($pdo, $_SESSION['user_id']);
                ?>
                <img src="<?= h($user_avatar) ?>" alt="Avatar">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= h($user_name) ?></div>
                </div>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#aaa;flex-shrink:0;"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>

                <!-- Dropdown -->
                <div id="user-dropdown" class="hidden">
                    <a href="/app/profile.php" class="dropdown-item">Profile</a>
                    <a href="/app/settings.php" class="dropdown-item">Settings</a>
                    <!-- Theme toggle -->
                    <button onclick="toggleTheme()" style="width:100%;margin-top:4px;display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:8px;background:transparent;border:none;cursor:pointer;font-size:13px;color:#888;transition:background .12s;" onmouseover="this.style.background='#ededed'" onmouseout="this.style.background='transparent'">
                        <svg id="theme-icon-light" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        <svg id="theme-icon-dark" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                        <span id="theme-label">Dark mode</span>
                    </button>
                    <div class="dropdown-divider"></div>
                    <a href="/auth/logout.php" class="dropdown-item danger">Log out</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div id="overlay" onclick="closeSidebar()"></div>

    <!-- Main -->
    <main id="main">

        <!-- Mobile topbar -->
        <div id="topbar">
            <button onclick="openSidebar()" style="padding:6px;background:transparent;border:none;cursor:pointer;color:#666;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="logo-mark" style="width:26px;height:26px;font-size:15px;">E</div>
                <span style="font-weight:700;font-size:15px;" class="dark:text-gray-100">Elara</span>
            </div>
            <button onclick="newChat()" style="padding:6px 12px;background:var(--accent);color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">New</button>
        </div>

        <!-- Chat header strip -->
        <div id="chat-header">
            <div>
                <div id="chat-title">Conversation</div>
                <div id="chat-date"></div>
            </div>
            <div style="display:flex;gap:8px;">
                <button onclick="shareChat()" class="header-btn">Share</button>
                <button onclick="deleteChat()" class="header-btn danger">Delete</button>
            </div>
        </div>

        <!-- Welcome screen -->
        <div id="welcome-screen">
            <div class="fade-up">
                <!-- <h1 class="welcome-heading">How can I help?</h1> -->
                <h1 class="welcome-heading">Think bigger - Create faster.</h1>
                <p class="welcome-sub">Ask me anything — I'm ready when you are.</p>
            </div>

            <!-- Prompt pills -->
            <div class="prompt-pills fade-up" style="transition-delay:.16s;">
                <button onclick="insertPrompt('Help me write a cover letter')" class="prompt-pill">Cover letter</button>
                <button onclick="insertPrompt('Give me a challenging puzzle')" class="prompt-pill">Puzzle</button>
                <button onclick="insertPrompt('Explain a complex concept simply')" class="prompt-pill">Explain something</button>
                <button onclick="insertPrompt('Help me analyze this data')" class="prompt-pill">Analyze data</button>
                <button onclick="insertPrompt('Write a short story')" class="prompt-pill">Short story</button>
            </div>
        </div>

        <!-- Chat messages -->
        <div id="chat-messages"></div>

        <!-- Input area -->
        <div id="input-area">
            <div class="input-shell">
                <textarea
                    id="message-input"
                    placeholder="Message Elara…"
                    rows="1"
                    oninput="autoResize(this)"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                ></textarea>
                <div class="input-toolbar">
                    <div class="toolbar-left">
                        <button class="tool-btn" title="Attach file">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                        <button class="model-chip" title="Switch model">
                            Elara 4.0
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </div>
                    <button onclick="sendMessage()" id="send-btn" title="Send">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </div>
            </div>
            <p class="input-hint">Elara can make mistakes. Verify important information.</p>
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

        /* ─── Textarea auto-resize ─── */
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 180) + 'px';
        }

        /* ─── Fade-up init ─── */
        requestAnimationFrame(() => {
            const items = document.querySelectorAll('.fade-up');
            items.forEach((el, i) => {
                setTimeout(() => el.classList.add('in'), 80 + i * 80);
            });
        });

        /* ─── Mobile sidebar ─── */
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('visible');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('visible');
        }

        /* ─── User menu ─── */
        function toggleUserMenu() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', e => {
            const btn = document.getElementById('sidebar-user-btn');
            const menu = document.getElementById('user-dropdown');
            if (!btn.contains(e.target)) menu.classList.add('hidden');
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
                list.innerHTML = '<li style="padding:8px 10px;font-size:13px;color:#aaa;">No conversations</li>';
                return;
            }
            list.innerHTML = filtered.map(c => `
                <li onclick="openConversation(${c.id})"
                    class="${c.id == conversationId ? 'active' : ''}">
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
            msgs.classList.add('visible');
            document.getElementById('chat-header').classList.add('visible');
        }

        /* ─── Load messages ─── */
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
            row.className = 'msg-row ' + role;

            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble ' + role;
            bubble.innerHTML = content.replace(/\n/g, '<br>');

            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function appendTyping() {
            const wrap = document.getElementById('chat-messages');
            const row = document.createElement('div');
            row.className = 'msg-row assistant';
            row.id = 'typing-indicator';
            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble assistant';
            bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            row.appendChild(bubble);
            wrap.appendChild(row);
            wrap.scrollTop = wrap.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        /* ─── Send message ─── */
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