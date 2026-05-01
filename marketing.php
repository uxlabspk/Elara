<?php 
require_once 'includes/functions.php';
$settings = isset($_SESSION['user_id']) ? get_user_settings($pdo, $_SESSION['user_id']) : [];
$theme = ($settings['theme'] ?? 'light') == 'dark' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elara AI — Your Personal AI Assistant</title>
    <meta name="description" content="Elara AI is your personal AI assistant powered by advanced AI models. Chat, create, code, and learn with intelligent conversations.">
    
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
                        'swiss-black': '#111111',
                        'swiss-gray': '#F5F5F5',
                    }
                }
            }
        }
    </script>
    
    <link href="assets/css/app.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23FF3B30'/><text x='50' y='65' font-size='50' font-family='sans-serif' font-weight='bold' text-anchor='middle' fill='white'>E</text></svg>">
    
    <style>
        :root {
            --bg: #FFFFFF;
            --surface: #FAFAFA;
            --border: #E5E5E5;
            --text-primary: #111111;
            --text-secondary: #666666;
            --text-muted: #999999;
            --accent: #FF3B30;
            --accent-fg: #FFFFFF;
        }

        [data-theme="dark"] {
            --bg: #0A0A0A;
            --surface: #141414;
            --border: #252525;
            --text-primary: #FAFAFA;
            --text-secondary: #A1A1A1;
            --text-muted: #555555;
            --accent: #FF3B30;
            --accent-fg: #FFFFFF;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            line-height: 1.5;
            transition: background 0.3s, color 0.3s;
        }

        .swiss-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .swiss-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section-padding {
            padding: 120px 0;
        }

        .display-text {
            font-size: clamp(48px, 8vw, 96px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .heading-xl {
            font-size: clamp(36px, 5vw, 56px);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .heading-lg {
            font-size: clamp(28px, 3vw, 36px);
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .heading-md {
            font-size: 20px;
            font-weight: 600;
            line-height: 1.3;
        }

        .body-lg {
            font-size: 18px;
            line-height: 1.6;
        }

        .body-md {
            font-size: 16px;
            line-height: 1.6;
        }

        .body-sm {
            font-size: 14px;
            line-height: 1.5;
        }

        .text-muted {
            color: var(--text-secondary);
        }

        .text-muted-dark {
            color: var(--text-muted);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 28px;
            background: var(--accent);
            color: var(--accent-fg);
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 59, 48, 0.25);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 28px;
            background: transparent;
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            border-color: var(--text-primary);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: var(--text-muted);
            transform: translateY(-4px);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--text-primary);
        }

        .accordion-item {
            border-bottom: 1px solid var(--border);
        }

        .accordion-header {
            width: 100%;
            padding: 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .accordion-content.open {
            max-height: 200px;
            padding-bottom: 24px;
        }

        .accordion-icon {
            transition: transform 0.3s ease;
        }

        .accordion-icon.open {
            transform: rotate(45deg);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .section-padding {
                padding: 80px 0;
            }
            
            .swiss-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50" style="background: var(--bg); border-bottom: 1px solid var(--border);">
        <div class="swiss-container" style="padding: 16px 24px;">
            <div class="flex items-center justify-between">
                <a href="#" class="flex items-center gap-2" style="text-decoration: none;">
                    <span style="font-size: 24px; font-weight: 700;">E</span>
                    <span style="font-weight: 700; font-size: 18px; color: var(--text-primary);">Elara</span>
                </a>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="nav-link">Features</a>
                    <a href="#how-it-works" class="nav-link">How it works</a>
                    <a href="#pricing" class="nav-link">Pricing</a>
                    <a href="#faq" class="nav-link">FAQ</a>
                </div>
                
                <div class="flex items-center gap-4">
                    <?php if (is_logged_in()): ?>
                        <a href="/app/index.php" class="btn-primary" style="padding: 12px 20px; font-size: 14px;">
                            Go to App
                        </a>
                    <?php else: ?>
                        <a href="/auth/login.php" class="nav-link">Login</a>
                        <a href="/auth/register.php" class="btn-primary" style="padding: 12px 20px; font-size: 14px;">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="section-padding" style="padding-top: 160px; padding-bottom: 120px;">
        <div class="swiss-container">
            <div class="swiss-grid">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.1em;">
                        AI Assistant
                    </p>
                </div>
                
                <div style="grid-column: span 12;">
                    <h1 class="display-text fade-in" style="margin-bottom: 24px;">
                        Think bigger.<br>
                        Create faster.
                    </h1>
                </div>
                
                <div style="grid-column: span 12; max-width: 600px;">
                    <p class="body-lg text-muted fade-in" style="margin-bottom: 40px;">
                        Your personal AI assistant powered by advanced AI models. 
                        Chat, create, code, and learn with intelligent conversations 
                        that adapt to your needs.
                    </p>
                </div>
                
                <div style="grid-column: span 12;" class="fade-in">
                    <div class="flex flex-wrap gap-4">
                        <?php if (!is_logged_in()): ?>
                            <a href="/auth/register.php" class="btn-primary">
                                Start Free Chat
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="/app/index.php" class="btn-primary">
                                Open Chat
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        <a href="#features" class="btn-secondary">
                            See Features
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding" style="background: var(--surface);">
        <div class="swiss-container">
            <div class="swiss-grid" style="margin-bottom: 64px;">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
                        Capabilities
                    </p>
                    <h2 class="heading-xl fade-in">
                        Everything you need<br>
                        in one place.
                    </h2>
                </div>
            </div>
            
            <div class="swiss-grid">
                <!-- Feature 1 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #F0FDF4;">
                            <svg width="24" height="24" fill="none" stroke="#16A34A" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Smart Writing</h3>
                        <p class="body-md text-muted">
                            Create compelling content with AI assistance. From emails to essays, get help with any writing task.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #EFF6FF;">
                            <svg width="24" height="24" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="16 18 22 12 16 6"/>
                                <polyline points="8 6 2 12 8 18"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Code & Debug</h3>
                        <p class="body-md text-muted">
                            Write, review, and debug code across multiple programming languages with intelligent suggestions.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #FEF3C7;">
                            <svg width="24" height="24" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Research</h3>
                        <p class="body-md text-muted">
                            Dive deep into any topic with comprehensive analysis and well-sourced information.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #FDF4FF;">
                            <svg width="24" height="24" fill="none" stroke="#9333EA" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Learning</h3>
                        <p class="body-md text-muted">
                            Learn new concepts with explanations tailored to your level of understanding.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #FFF1F2;">
                            <svg width="24" height="24" fill="none" stroke="#E11D48" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Data Analysis</h3>
                        <p class="body-md text-muted">
                            Transform raw data into actionable insights with powerful analytical capabilities.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <div class="feature-icon" style="background: #F0F9FF;">
                            <svg width="24" height="24" fill="none" stroke="#0EA5E9" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Conversations</h3>
                        <p class="body-md text-muted">
                            Natural, contextual conversations that remember your context and preferences.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="section-padding">
        <div class="swiss-container">
            <div class="swiss-grid" style="margin-bottom: 64px;">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
                        Process
                    </p>
                    <h2 class="heading-xl fade-in">
                        Simple to start.<br>
                        Powerful results.
                    </h2>
                </div>
            </div>
            
            <div class="swiss-grid">
                <!-- Step 1 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div style="padding: 32px;">
                        <span class="heading-xl" style="color: var(--text-muted); margin-bottom: 16px; display: block;">01</span>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Ask anything</h3>
                        <p class="body-md text-muted">
                            Type your question or task in natural language. No complicated prompts needed.
                        </p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div style="padding: 32px;">
                        <span class="heading-xl" style="color: var(--text-muted); margin-bottom: 16px; display: block;">02</span>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Get instant answers</h3>
                        <p class="body-md text-muted">
                            Receive intelligent responses tailored to your specific needs and context.
                        </p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div style="grid-column: span 4;" class="fade-in">
                    <div style="padding: 32px;">
                        <span class="heading-xl" style="color: var(--text-muted); margin-bottom: 16px; display: block;">03</span>
                        <h3 class="heading-md" style="margin-bottom: 12px;">Iterate & refine</h3>
                        <p class="body-md text-muted">
                            Continue the conversation to refine results until you get exactly what you need.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding" style="background: var(--surface);">
        <div class="swiss-container">
            <div class="swiss-grid" style="margin-bottom: 64px;">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
                        What people say
                    </p>
                    <h2 class="heading-xl fade-in">
                        Trusted by thousands<br>
                        of users worldwide.
                    </h2>
                </div>
            </div>
            
            <div class="swiss-grid">
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card">
                        <p class="body-md" style="margin-bottom: 24px; font-style: italic;">
                            "Elara has completely transformed how I work. It's like having a brilliant colleague available 24/7."
                        </p>
                        <div class="flex items-center gap-3">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                            <div>
                                <p style="font-weight: 600; font-size: 15px;">Sarah Chen</p>
                                <p class="body-sm text-muted">Product Designer</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card">
                        <p class="body-md" style="margin-bottom: 24px; font-style: italic;">
                            "The coding assistance is incredible. It helps me write better code faster and catch bugs before they happen."
                        </p>
                        <div class="flex items-center gap-3">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #f093fb, #f5576c);"></div>
                            <div>
                                <p style="font-weight: 600; font-size: 15px;">Alex Rodriguez</p>
                                <p class="body-sm text-muted">Software Engineer</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="grid-column: span 4;" class="fade-in">
                    <div class="card">
                        <p class="body-md" style="margin-bottom: 24px; font-style: italic;">
                            "I use Elara daily for research and writing. It's genuinely improved my productivity by leaps and bounds."
                        </p>
                        <div class="flex items-center gap-3">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #4facfe, #00f2fe);"></div>
                            <div>
                                <p style="font-weight: 600; font-size: 15px;">Emily Watson</p>
                                <p class="body-sm text-muted">Content Writer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="section-padding">
        <div class="swiss-container">
            <div class="swiss-grid" style="margin-bottom: 64px;">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
                        Pricing
                    </p>
                    <h2 class="heading-xl fade-in">
                        Simple, transparent<br>
                        pricing for everyone.
                    </h2>
                </div>
            </div>
            
            <div class="swiss-grid" style="align-items: start;">
                <!-- Free Plan -->
                <div style="grid-column: span 6;" class="fade-in">
                    <div class="card" style="height: 100%;">
                        <p class="body-sm" style="color: var(--accent); font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Free</p>
                        <h3 class="heading-lg" style="margin-bottom: 8px;">$0</h3>
                        <p class="body-md text-muted" style="margin-bottom: 32px;">Forever free</p>
                        
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px;">
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Basic AI conversations</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Limited history</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Standard response time</span>
                            </li>
                        </ul>
                        
                        <div style="margin-top: 32px;">
                            <a href="/auth/register.php" class="btn-secondary" style="width: 100%; justify-content: center;">Get Started</a>
                        </div>
                    </div>
                </div>
                
                <!-- Pro Plan -->
                <div style="grid-column: span 6;" class="fade-in">
                    <div class="card" style="height: 100%; border-color: var(--accent);">
                        <p class="body-sm" style="color: var(--accent); font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Pro</p>
                        <h3 class="heading-lg" style="margin-bottom: 8px;">$19<span class="body-md text-muted">/month</span></h3>
                        <p class="body-md text-muted" style="margin-bottom: 32px;">For power users</p>
                        
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px;">
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Unlimited conversations</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Unlimited history</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Faster responses</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="body-md">Advanced features</span>
                            </li>
                        </ul>
                        
                        <div style="margin-top: 32px;">
                            <a href="/auth/register.php" class="btn-primary" style="width: 100%; justify-content: center;">Upgrade to Pro</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section-padding" style="background: var(--surface);">
        <div class="swiss-container">
            <div class="swiss-grid" style="margin-bottom: 64px;">
                <div style="grid-column: span 12;">
                    <p class="body-sm fade-in" style="color: var(--accent); font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
                        FAQ
                    </p>
                    <h2 class="heading-xl fade-in">
                        Frequently asked<br>
                        questions.
                    </h2>
                </div>
            </div>
            
            <div class="swiss-grid">
                <div style="grid-column: span 8;">
                    <div class="fade-in">
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>What is Elara AI?</span>
                                <svg class="accordion-icon" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                            </button>
                            <div class="accordion-content">
                                <p class="body-md text-muted">Elara is your personal AI assistant powered by advanced AI models. It can help you with writing, coding, research, learning, and much more through natural conversations.</p>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>Is Elara free to use?</span>
                                <svg class="accordion-icon" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                            </button>
                            <div class="accordion-content">
                                <p class="body-md text-muted">Yes, Elara has a free plan that lets you use basic features indefinitely. We also offer a Pro plan for users who need more advanced features and faster responses.</p>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>Is my data secure?</span>
                                <svg class="accordion-icon" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                            </button>
                            <div class="accordion-content">
                                <p class="body-md text-muted">Absolutely. We take data security seriously and follow industry best practices to protect your information. Your conversations are encrypted and never shared with third parties.</p>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>Can I cancel anytime?</span>
                                <svg class="accordion-icon" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                            </button>
                            <div class="accordion-content">
                                <p class="body-md text-muted">Yes, you can cancel your Pro subscription at any time. You'll continue to have access until the end of your billing period.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding">
        <div class="swiss-container">
            <div class="swiss-grid">
                <div style="grid-column: span 12; text-align: center;" class="fade-in">
                    <h2 class="heading-xl" style="margin-bottom: 24px;">
                        Ready to get started?
                    </h2>
                    <p class="body-lg text-muted" style="margin-bottom: 40px; max-width: 500px; margin-left: auto; margin-right: auto;">
                        Join thousands of users who have already transformed their workflow with Elara.
                    </p>
                    <div class="flex flex-wrap gap-4" style="justify-content: center;">
                        <?php if (!is_logged_in()): ?>
                            <a href="/auth/register.php" class="btn-primary">
                                Create Free Account
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="/app/index.php" class="btn-primary">
                                Start Chatting
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="border-top: 1px solid var(--border); padding: 48px 0;">
        <div class="swiss-container">
            <div class="swiss-grid">
                <div style="grid-column: span 3;">
                    <a href="#" class="flex items-center gap-2" style="text-decoration: none; margin-bottom: 16px;">
                        <span style="font-size: 24px; font-weight: 700;">E</span>
                        <span style="font-weight: 700; font-size: 18px; color: var(--text-primary);">Elara</span>
                    </a>
                    <p class="body-sm text-muted">
                        Your personal AI assistant.
                    </p>
                </div>
                
                <div style="grid-column: span 2;">
                    <p class="body-sm" style="font-weight: 600; margin-bottom: 16px;">Product</p>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
                        <li><a href="#features" class="body-sm nav-link">Features</a></li>
                        <li><a href="#pricing" class="body-sm nav-link">Pricing</a></li>
                    </ul>
                </div>
                
                <div style="grid-column: span 2;">
                    <p class="body-sm" style="font-weight: 600; margin-bottom: 16px;">Company</p>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
                        <li><a href="#" class="body-sm nav-link">About</a></li>
                        <li><a href="#" class="body-sm nav-link">Blog</a></li>
                    </ul>
                </div>
                
                <div style="grid-column: span 2;">
                    <p class="body-sm" style="font-weight: 600; margin-bottom: 16px;">Legal</p>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
                        <li><a href="#" class="body-sm nav-link">Privacy</a></li>
                        <li><a href="#" class="body-sm nav-link">Terms</a></li>
                    </ul>
                </div>
                
                <div style="grid-column: span 3;">
                    <p class="body-sm" style="font-weight: 600; margin-bottom: 16px;">Connect</p>
                    <div class="flex gap-4">
                        <a href="#" class="nav-link">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="#" class="nav-link">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center;">
                <p class="body-sm text-muted">
                    &copy; 2026 Elara AI. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Theme toggle
        function initTheme() {
            const root = document.documentElement;
            const savedTheme = localStorage.getItem('theme') || 'light';
            root.setAttribute('data-theme', savedTheme);
        }
        initTheme();

        // Accordion
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.accordion-icon');
            
            content.classList.toggle('open');
            icon.classList.toggle('open');
        }

        // Fade in animation
        function initFadeIn() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.fade-in').forEach(el => {
                observer.observe(el);
            });
        }
        initFadeIn();
    </script>
</body>
</html>