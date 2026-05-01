<?php require_once '../includes/functions.php'; require_login(); ?>
<?php $settings = get_user_settings($pdo, $_SESSION['user_id']); ?>
<?php require_once '../includes/header.php'; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap');

:root {
  --bg: #f5f5f5;
  --surface: #ffffff;
  --surface-2: #f0f0f0;
  --border: #e5e5e5;
  --text-primary: #111111;
  --text-secondary: #666666;
  --text-muted: #999999;
  --accent: #111111;
  --accent-fg: #ffffff;
  --user-bubble: #111111;
  --user-bubble-fg: #ffffff;
  --ai-bubble: #f0f0f0;
  --ai-bubble-fg: #111111;
  --sidebar-bg: #fafafa;
  --hover: #f0f0f0;
  --active: #e8e8e8;
  --input-bg: #ffffff;
  --shadow: 0 1px 3px rgba(0,0,0,0.08);
  --radius: 12px;
  --radius-sm: 8px;
}

[data-theme="dark"] {
  --bg: #141414;
  --surface: #1c1c1c;
  --surface-2: #242424;
  --border: #2a2a2a;
  --text-primary: #f0f0f0;
  --text-secondary: #888888;
  --text-muted: #555555;
  --accent: #f0f0f0;
  --accent-fg: #111111;
  --user-bubble: #f0f0f0;
  --user-bubble-fg: #111111;
  --ai-bubble: #242424;
  --ai-bubble-fg: #f0f0f0;
  --sidebar-bg: #181818;
  --hover: #242424;
  --active: #2e2e2e;
  --input-bg: #1c1c1c;
  --shadow: 0 1px 3px rgba(0,0,0,0.3);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
  background: var(--bg);
  color: var(--text-primary);
  font-size: 14px;
  line-height: 1.5;
  transition: background 0.2s, color 0.2s;
}

/* ── Layout ── */
.app-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* ── Topbar ── */
.topbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  height: 52px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  padding: 0 20px;
  gap: 8px;
  z-index: 100;
}

.topbar-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 15px;
  color: var(--text-primary);
  text-decoration: none;
  flex-shrink: 0;
}

.logo-icon {
  width: 28px;
  height: 28px;
  background: var(--accent);
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-icon svg { color: var(--accent-fg); }

.topbar-divider {
  width: 1px;
  height: 20px;
  background: var(--border);
  margin: 0 4px;
}

.topbar-nav {
  display: flex;
  align-items: center;
  gap: 2px;
  flex: 1;
}

.topbar-nav-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: var(--radius-sm);
  border: none;
  background: transparent;
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.topbar-nav-btn:hover { background: var(--hover); color: var(--text-primary); }
.topbar-nav-btn.active { background: var(--accent); color: var(--accent-fg); }

.topbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}

.topbar-icon-btn {
  width: 32px;
  height: 32px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: transparent;
  color: var(--text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s, color 0.15s;
}

.topbar-icon-btn:hover { background: var(--hover); color: var(--text-primary); }

.topbar-upgrade {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-primary);
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}

.topbar-upgrade:hover { background: var(--hover); }

.avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--surface-2);
  border: 1.5px solid var(--border);
  object-fit: cover;
  cursor: pointer;
}

/* ── Sidebar ── */
.sidebar {
  width: 260px;
  flex-shrink: 0;
  background: var(--sidebar-bg);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  padding-top: 52px;
  overflow: hidden;
}

.sidebar-inner {
  display: flex;
  flex-direction: column;
  flex: 1;
  overflow: hidden;
  padding: 12px;
  gap: 8px;
}

.btn-new-chat {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 8px 12px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-primary);
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s, box-shadow 0.15s;
}

.btn-new-chat:hover { background: var(--hover); box-shadow: var(--shadow); }

.search-wrap {
  position: relative;
}

.search-wrap svg {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 7px 10px 7px 32px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: var(--input-bg);
  color: var(--text-primary);
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition: border-color 0.15s;
}

.search-input::placeholder { color: var(--text-muted); }
.search-input:focus { border-color: var(--text-secondary); }

.conv-section-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 4px 4px 2px;
}

.conv-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 1px;
  flex: 1;
  overflow-y: auto;
}

.conv-list::-webkit-scrollbar { width: 4px; }
.conv-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.conv-item {
  padding: 8px 10px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background 0.12s;
  font-size: 13px;
  font-weight: 400;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conv-item:hover { background: var(--hover); color: var(--text-primary); }
.conv-item.active { background: var(--active); color: var(--text-primary); font-weight: 500; }

.conv-empty {
  padding: 20px 8px;
  text-align: center;
  color: var(--text-muted);
  font-size: 13px;
}

/* ── Main ── */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding-top: 52px;
  min-width: 0;
  background: var(--bg);
}

/* ── Chat Header ── */
.chat-header {
  height: 52px;
  border-bottom: 1px solid var(--border);
  background: var(--surface);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  flex-shrink: 0;
}

.chat-header-left { min-width: 0; }

.chat-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chat-date {
  font-size: 11px;
  color: var(--text-muted);
  margin-top: 1px;
}

.chat-header-actions { display: flex; gap: 4px; }

.header-btn {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 5px 10px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: transparent;
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 12.5px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.header-btn:hover { background: var(--hover); color: var(--text-primary); }
.header-btn.danger:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
[data-theme="dark"] .header-btn.danger:hover { background: #2a1515; color: #f87171; border-color: #5c2020; }

/* ── Welcome Screen ── */
.welcome-screen {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  gap: 32px;
}

.welcome-heading {
  font-size: 32px;
  font-weight: 600;
  color: var(--text-primary);
  letter-spacing: -0.5px;
  text-align: center;
}

.welcome-sub {
  font-size: 14px;
  color: var(--text-secondary);
  text-align: center;
  margin-top: -24px;
  max-width: 380px;
  line-height: 1.6;
}

.capability-cards {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: center;
  max-width: 700px;
}

.cap-card {
  padding: 14px 16px;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: var(--surface);
  cursor: pointer;
  width: 130px;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.cap-card:hover { border-color: var(--text-muted); box-shadow: var(--shadow); }

.cap-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.cap-title { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.cap-desc { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; line-height: 1.4; }

/* ── Messages ── */
.messages-wrap {
  flex: 1;
  overflow-y: auto;
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.messages-wrap::-webkit-scrollbar { width: 5px; }
.messages-wrap::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.msg-row { display: flex; }
.msg-row.user { justify-content: flex-end; }
.msg-row.assistant { justify-content: flex-start; }

.msg-bubble {
  padding: 10px 14px;
  border-radius: 12px;
  max-width: min(480px, 75%);
  font-size: 14px;
  line-height: 1.55;
  word-break: break-word;
}

.msg-row.user .msg-bubble {
  background: var(--user-bubble);
  color: var(--user-bubble-fg);
  border-bottom-right-radius: 4px;
}

.msg-row.assistant .msg-bubble {
  background: var(--ai-bubble);
  color: var(--ai-bubble-fg);
  border-bottom-left-radius: 4px;
}

/* ── Typing indicator ── */
.typing-dots { display: flex; gap: 4px; padding: 4px 2px; }
.typing-dots span {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--text-muted);
  animation: bounce 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-5px); }
}

/* ── Input Area ── */
.input-area {
  padding: 16px 20px 20px;
  background: var(--bg);
  flex-shrink: 0;
}

.input-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 16px 12px;
  box-shadow: var(--shadow);
  transition: border-color 0.15s;
}

.input-box:focus-within { border-color: var(--text-secondary); }

.input-textarea {
  width: 100%;
  background: transparent;
  border: none;
  outline: none;
  resize: none;
  font-family: inherit;
  font-size: 14px;
  color: var(--text-primary);
  line-height: 1.5;
  min-height: 24px;
  max-height: 160px;
}

.input-textarea::placeholder { color: var(--text-muted); }

.input-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 10px;
}

.input-left-actions { display: flex; gap: 4px; }

.input-action-btn {
  width: 30px;
  height: 30px;
  border-radius: 7px;
  border: 1px solid var(--border);
  background: transparent;
  color: var(--text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s, color 0.15s;
}

.input-action-btn:hover { background: var(--hover); color: var(--text-primary); }

.input-right-actions { display: flex; gap: 6px; align-items: center; }

.btn-send {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: var(--radius-sm);
  border: none;
  background: var(--accent);
  color: var(--accent-fg);
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  transition: opacity 0.15s;
}

.btn-send:hover { opacity: 0.85; }
.btn-send:disabled { opacity: 0.4; cursor: not-allowed; }

.input-hint {
  font-size: 11.5px;
  color: var(--text-muted);
  margin-top: 8px;
  text-align: center;
}

/* ── Suggestion pills ── */
.suggestions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: -8px;
}

.suggestion-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 12px;
  border-radius: 20px;
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 12.5px;
  cursor: pointer;
  transition: background 0.15s, color 0.15s, border-color 0.15s;
}

.suggestion-pill:hover { background: var(--hover); color: var(--text-primary); border-color: var(--text-muted); }

/* ── Model selector ── */
.model-selector {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 5px 10px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 12.5px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}

.model-selector:hover { background: var(--hover); color: var(--text-primary); }
</style>

<div class="app-shell" id="app">

  <!-- Topbar -->
  <header class="topbar">
    <a href="#" class="topbar-logo">
      <div class="logo-icon">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
      </div>
      Elara
    </a>

    <div class="topbar-divider"></div>

    <nav class="topbar-nav">
      <button class="topbar-nav-btn active" id="new-chat">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        New chat
      </button>
      <button class="topbar-nav-btn" id="search-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        Search chat
      </button>
      <button class="topbar-nav-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        Library
      </button>
    </nav>

    <div class="topbar-right">
      <button class="topbar-upgrade">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
        Upgrade
      </button>
      <button class="topbar-icon-btn" id="theme-toggle" title="Toggle theme">
        <svg id="theme-icon-light" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
        <svg id="theme-icon-dark" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>
      <div class="avatar" title="Profile">
        <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="padding:5px; color: var(--text-muted)">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-inner">
      <div class="search-wrap">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="conv-search" placeholder="Search conversations..." class="search-input" />
      </div>
      <div class="conv-section-label">Recent</div>
      <ul id="conversation-list" class="conv-list">
        <li class="conv-empty">Loading...</li>
      </ul>
    </div>
  </aside>

  <!-- Main -->
  <main class="main" id="main-area">

    <!-- Chat header (shown only when conversation active) -->
    <div id="chat-header" class="chat-header" style="display:none">
      <div class="chat-header-left">
        <div class="chat-title" id="chat-title">Conversation</div>
        <div class="chat-date" id="chat-date"></div>
      </div>
      <div class="chat-header-actions">
        <button class="header-btn" id="share-btn">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
          </svg>
          Share
        </button>
        <button class="header-btn danger" id="delete-btn">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="3 6 5 6 21 6"/>
            <path d="M19 6l-1 14H6L5 6"/>
            <path d="M10 11v6M14 11v6"/>
            <path d="M9 6V4h6v2"/>
          </svg>
          Delete
        </button>
      </div>
    </div>

    <!-- Welcome Screen -->
    <div class="welcome-screen" id="welcome-screen">
      <div>
        <div class="welcome-heading">Good to see you</div>
        <div class="welcome-sub">Your personal AI assistant — ready for any task you can imagine.</div>
      </div>

      <div class="capability-cards">
        <div class="cap-card" onclick="insertPrompt('Help me write something')">
          <div class="cap-icon" style="background:#f0fdf4">
            <svg width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
              <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
            </svg>
          </div>
          <div class="cap-title">Writing</div>
          <div class="cap-desc">Drafts, edits &amp; creative work</div>
        </div>
        <div class="cap-card" onclick="insertPrompt('Help me with programming')">
          <div class="cap-icon" style="background:#eff6ff">
            <svg width="14" height="14" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
              <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
            </svg>
          </div>
          <div class="cap-title">Programming</div>
          <div class="cap-desc">Code, debug &amp; review</div>
        </div>
        <div class="cap-card" onclick="insertPrompt('Help me with analysis and research')">
          <div class="cap-icon" style="background:#fef3c7">
            <svg width="14" height="14" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
          </div>
          <div class="cap-title">Research</div>
          <div class="cap-desc">Analysis &amp; deep dives</div>
        </div>
        <div class="cap-card" onclick="insertPrompt('Help me learn something new')">
          <div class="cap-icon" style="background:#fdf4ff">
            <svg width="14" height="14" fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
              <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
          </div>
          <div class="cap-title">Education</div>
          <div class="cap-desc">Learn &amp; understand</div>
        </div>
        <div class="cap-card" onclick="insertPrompt('Help me analyze data')">
          <div class="cap-icon" style="background:#fff1f2">
            <svg width="14" height="14" fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
          </div>
          <div class="cap-title">Analysis</div>
          <div class="cap-desc">Data &amp; insights</div>
        </div>
      </div>

      <div class="suggestions">
        <button class="suggestion-pill" onclick="insertPrompt('Help me write a cover letter')">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
          </svg>
          Help me write a cover letter
        </button>
        <button class="suggestion-pill" onclick="insertPrompt('Give me a challenging IQ test question')">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
          Give me an IQ test
        </button>
        <button class="suggestion-pill" onclick="insertPrompt('Can you rewrite this for clarity and structure')">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Rewrite for clarity
        </button>
        <button class="suggestion-pill" onclick="insertPrompt('Imagine a surreal creative scenario for me')">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/>
            <path d="M12 8v4l3 3"/>
          </svg>
          Imagine a scenario
        </button>
      </div>
    </div>

    <!-- Messages (hidden initially) -->
    <div id="chat-messages" class="messages-wrap" style="display:none"></div>

    <!-- Input Area -->
    <div class="input-area">
      <div class="input-box">
        <textarea
          id="message-input"
          class="input-textarea"
          placeholder="How can I help you today?"
          rows="1"
          autofocus
        ></textarea>
        <div class="input-actions">
          <div class="input-left-actions">
            <button class="input-action-btn" title="Attach file">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
              </svg>
            </button>
            <button class="model-selector" title="Switch model">
              Elara 4.0
              <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>
          </div>
          <div class="input-right-actions">
            <button class="input-action-btn" title="Voice input" id="voice-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                <line x1="12" y1="19" x2="12" y2="23"/>
                <line x1="8" y1="23" x2="16" y2="23"/>
              </svg>
            </button>
            <button class="btn-send" id="send-btn" type="button">
              Send
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
      <p class="input-hint">Elara can make mistakes. Please verify important information.</p>
    </div>

  </main>
</div>

<script>
// ── Theme ──
const root = document.documentElement;
const savedTheme = localStorage.getItem('theme') || 'light';
root.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);

document.getElementById('theme-toggle').addEventListener('click', () => {
  const current = root.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  root.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
  updateThemeIcon(next);
});

function updateThemeIcon(theme) {
  document.getElementById('theme-icon-light').style.display = theme === 'dark' ? 'block' : 'none';
  document.getElementById('theme-icon-dark').style.display = theme === 'light' ? 'block' : 'none';
}

// ── Auto-resize textarea ──
const textarea = document.getElementById('message-input');
textarea.addEventListener('input', () => {
  textarea.style.height = 'auto';
  textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
});

textarea.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

document.getElementById('send-btn').addEventListener('click', sendMessage);

// ── Suggestion helper ──
function insertPrompt(text) {
  textarea.value = text;
  textarea.focus();
  textarea.style.height = 'auto';
  textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
}

// ── Conversation state ──
const currentConversationId = <?= isset($_GET['c']) ? (int)$_GET['c'] : 0 ?>;
let conversations = [];

function loadConversations() {
  fetch('history.php?action=list')
    .then(r => r.json())
    .then(data => { conversations = data; renderConversations(); })
    .catch(err => console.error(err));
}

function renderConversations() {
  const searchTerm = document.getElementById('conv-search').value.toLowerCase();
  const filtered = conversations.filter(c => c.title.toLowerCase().includes(searchTerm));
  const list = document.getElementById('conversation-list');

  if (filtered.length === 0) {
    list.innerHTML = '<li class="conv-empty">No conversations yet</li>';
    return;
  }

  list.innerHTML = filtered.map(c => `
    <li class="conv-item ${c.id == currentConversationId ? 'active' : ''}"
        onclick="openConversation(${c.id})"
        title="${escapeHtml(c.title)}">
      ${escapeHtml(c.title)}
    </li>
  `).join('');
}

function escapeHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function openConversation(id) {
  window.location.href = 'index.php?c=' + id;
}

document.getElementById('new-chat').addEventListener('click', () => {
  window.location.href = 'index.php';
});

document.getElementById('conv-search').addEventListener('input', renderConversations);

// ── Messages ──
function showChatMode() {
  document.getElementById('welcome-screen').style.display = 'none';
  document.getElementById('chat-messages').style.display = 'flex';
  document.getElementById('chat-header').style.display = 'flex';
}

function loadMessages() {
  if (!currentConversationId) return;
  showChatMode();

  fetch(`history.php?action=messages&conversation_id=${currentConversationId}`)
    .then(r => r.json())
    .then(messages => {
      document.getElementById('chat-messages').innerHTML = '';
      messages.forEach(m => appendMessage(m.role, m.content));
    })
    .catch(err => console.error(err));
}

function appendMessage(role, content) {
  const wrap = document.getElementById('chat-messages');
  const row = document.createElement('div');
  row.className = `msg-row ${role}`;

  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble';
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
  bubble.className = 'msg-bubble';
  bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';

  row.appendChild(bubble);
  wrap.appendChild(row);
  wrap.scrollTop = wrap.scrollHeight;
}

function removeTyping() {
  const el = document.getElementById('typing-indicator');
  if (el) el.remove();
}

// ── Send ──
function sendMessage() {
  const content = textarea.value.trim();
  if (!content) return;

  if (!currentConversationId) showChatMode();

  appendMessage('user', content);
  textarea.value = '';
  textarea.style.height = 'auto';
  textarea.focus();

  appendTyping();
  document.getElementById('send-btn').disabled = true;

  fetch('chat.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      csrf_token: document.querySelector('meta[name="csrf-token"]').content,
      message: content,
      conversation_id: currentConversationId
    })
  })
  .then(r => r.json())
  .then(data => {
    removeTyping();
    document.getElementById('send-btn').disabled = false;
    if (data.error) {
      alert(data.error);
    } else {
      if (!currentConversationId) {
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

// ── Share / Delete ──
document.getElementById('share-btn')?.addEventListener('click', () => {
  if (!currentConversationId) { alert('Start a conversation first'); return; }
  window.location.href = 'share.php';
});

document.getElementById('delete-btn')?.addEventListener('click', () => {
  if (!currentConversationId || !confirm('Delete this conversation?')) return;

  fetch('history.php?action=delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      csrf_token: document.querySelector('meta[name="csrf-token"]').content,
      conversation_id: currentConversationId
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) window.location.href = 'index.php';
    else alert(data.error || 'Failed to delete');
  });
});

// ── Init ──
loadConversations();
if (currentConversationId) {
  loadMessages();
}
</script>

<?php require_once '../includes/footer.php'; ?>