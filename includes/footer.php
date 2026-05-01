<?php
// includes/footer.php
?>
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600 dark:text-gray-400 text-sm">
            <p>&copy; 2026 Elara AI. All rights reserved.</p>
            <div class="flex justify-center gap-4 mt-2 text-xs">
                <a href="#" class="hover:text-gray-900 dark:hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-gray-900 dark:hover:text-white">Terms of Service</a>
                <a href="#" class="hover:text-gray-900 dark:hover:text-white">Contact</a>
            </div>
        </div>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        document.getElementById('theme-toggle')?.addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.classList.remove(currentTheme);
            html.classList.add(newTheme);
            
            // Save preference via AJAX
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=update_theme&theme=' + newTheme + '&csrf_token=' + document.querySelector('meta[name="csrf-token"]').content
            }).catch(() => {});
            
            // Update button icon
            this.textContent = newTheme === 'dark' ? '☀️' : '🌙';
            
            // Save to localStorage
            localStorage.setItem('theme', newTheme);
        });
        
        // Load saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            const html = document.documentElement;
            html.classList.remove('light', 'dark');
            html.classList.add(savedTheme);
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
            }
        }
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            document.getElementById('flash-success')?.remove();
            document.getElementById('flash-error')?.remove();
        }, 5000);
    </script>

    <!-- Alpine.js for simple interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Highlight.js for code syntax highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        // Auto-highlight code blocks in messages
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });
        
        // Re-highlight when new messages are added (useful for dynamic content)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.querySelectorAll) {
                            node.querySelectorAll('pre code').forEach((el) => {
                                hljs.highlightElement(el);
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
</body>
</html>
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        node.querySelectorAll('pre code').forEach((el) => {
                            hljs.highlightElement(el);
                        });
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
</body>
</html>
