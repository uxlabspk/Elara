<?php require_once 'includes/functions.php'; require_login(); ?>
<?php $settings = get_user_settings($pdo, $_SESSION['user_id']); ?>
<?php require_once 'includes/header.php'; ?>

<div class="flex h-[calc(100vh-4rem)]">
    <!-- Sidebar: Chat History -->
    <aside class="w-64 bg-gray-100 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4 overflow-y-auto flex flex-col">
        <button id="new-chat" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg mb-4 transition">
            + New Chat
        </button>
        
        <!-- Search in conversations -->
        <div class="mb-4">
            <input type="text" id="conv-search" placeholder="Search conversations..." 
                   class="form-input text-sm" />
        </div>
        
        <!-- Conversation List -->
        <div class="flex-1 overflow-y-auto">
            <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Recent</h3>
            <ul id="conversation-list" class="space-y-1">
                <!-- Loaded via AJAX from history.php -->
                <li class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">Loading...</li>
            </ul>
        </div>
    </aside>

    <!-- Main Chat Area -->
    <main class="flex-1 flex flex-col bg-white dark:bg-gray-900">
        <!-- Chat Header -->
        <div id="chat-header" class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
            <div>
                <h2 id="chat-title" class="text-lg font-semibold text-gray-900 dark:text-white">New Chat</h2>
                <p id="chat-date" class="text-xs text-gray-500 dark:text-gray-400"></p>
            </div>
            <div class="flex gap-2">
                <button id="share-btn" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-2" title="Share">
                    🔗
                </button>
                <button id="delete-btn" class="text-red-600 hover:text-red-700 p-2" title="Delete" style="display: none;">
                    🗑️
                </button>
            </div>
        </div>
        
        <!-- Messages Container -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Messages will appear here -->
            <div class="text-center text-gray-400 dark:text-gray-500 mt-12">
                <p class="text-lg mb-2">👋 Hello! Start a conversation</p>
                <p class="text-sm">Type a message below to begin chatting with Elara AI</p>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <form id="chat-form" class="flex gap-2">
                <input type="text" id="message-input" placeholder="Type your message..." 
                       class="flex-1 form-input" autofocus />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition">
                    Send
                </button>
            </form>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Elara AI can make mistakes. Please verify important information.
            </p>
        </div>
    </main>
</div>

<script>
const currentConversationId = <?= isset($_GET['c']) ? (int)$_GET['c'] : 0 ?>;
let conversations = [];

// Load conversation list
function loadConversations() {
    fetch('history.php?action=list')
        .then(r => r.json())
        .then(data => {
            conversations = data;
            renderConversations();
        })
        .catch(err => console.error(err));
}

// Render conversations
function renderConversations() {
    let html = '';
    const searchTerm = document.getElementById('conv-search')?.value.toLowerCase() || '';
    
    const filtered = conversations.filter(c => 
        c.title.toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        html = '<li class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No conversations</li>';
    } else {
        filtered.forEach(c => {
            const isActive = c.id == currentConversationId ? 'bg-blue-100 dark:bg-blue-900/30' : '';
            html += `<li class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded cursor-pointer transition ${isActive}"
                         onclick="openConversation(${c.id})"
                         title="${c.title}">
                        <div class="truncate text-sm font-medium text-gray-900 dark:text-white">${c.title}</div>
                    </li>`;
        });
    }
    document.getElementById('conversation-list').innerHTML = html;
}

// Open conversation
function openConversation(id) {
    window.location.href = 'index.php?c=' + id;
}

// New chat
document.getElementById('new-chat').addEventListener('click', function() {
    window.location.href = 'index.php';
});

// Search conversations
document.getElementById('conv-search')?.addEventListener('input', renderConversations);

// Load messages for current conversation
function loadMessages() {
    if (!currentConversationId) return;
    
    fetch(`history.php?action=messages&conversation_id=${currentConversationId}`)
        .then(r => r.json())
        .then(messages => {
            document.getElementById('chat-messages').innerHTML = '';
            messages.forEach(m => appendMessage(m.role, m.content));
        })
        .catch(err => console.error(err));
}

// Append message to chat
function appendMessage(role, content) {
    const div = document.getElementById('chat-messages');
    const msgDiv = document.createElement('div');
    msgDiv.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';
    
    const bubble = document.createElement('div');
    bubble.className = role === 'user' 
        ? 'bg-blue-600 text-white rounded-lg rounded-br-none px-4 py-2 max-w-xs lg:max-w-md'
        : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg rounded-bl-none px-4 py-2 max-w-xs lg:max-w-md';
    bubble.innerHTML = content.replace(/\n/g, '<br>');
    
    msgDiv.appendChild(bubble);
    div.appendChild(msgDiv);
    div.scrollTop = div.scrollHeight;
}

// Send message
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    if (!content) return;

    // Append user message optimistically
    appendMessage('user', content);
    input.value = '';
    input.focus();

    fetch('chat.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            'csrf_token': document.querySelector('meta[name="csrf-token"]').content,
            'message': content,
            'conversation_id': currentConversationId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            // Update conversation ID if it's a new chat
            if (!currentConversationId) {
                window.location.href = 'index.php?c=' + data.conversation_id;
            }
            appendMessage('assistant', data.reply);
            loadConversations();
        }
    })
    .catch(err => alert('Error: ' + err));
});

// Share button
document.getElementById('share-btn')?.addEventListener('click', function() {
    if (!currentConversationId) {
        alert('Start a conversation first');
        return;
    }
    window.location.href = 'share.php';
});

// Delete button
document.getElementById('delete-btn')?.addEventListener('click', function() {
    if (!currentConversationId || !confirm('Delete this conversation?')) return;
    
    fetch('history.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            'csrf_token': document.querySelector('meta[name="csrf-token"]').content,
            'conversation_id': currentConversationId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'index.php';
        } else {
            alert(data.error || 'Failed to delete');
        }
    });
});

// Initial load
loadConversations();
if (currentConversationId) {
    document.getElementById('delete-btn').style.display = 'block';
    loadMessages();
}
</script>

<?php require_once 'includes/footer.php'; ?>