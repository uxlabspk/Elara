<?php
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];

// Get current settings
$stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch();

if (!$settings) {
    // Create default settings
    $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $settings = [
        'theme' => 'light',
        'model' => 'mistral-small-latest',
        'temperature' => 0.7,
        'max_tokens' => 2048,
        'language' => 'en'
    ];
}

$error = '';
$success = '';

// Handle theme update via POST (from header toggle)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_theme') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['error' => 'CSRF validation failed']);
        exit;
    }
    
    $theme = $_POST['theme'] ?? 'light';
    $stmt = $pdo->prepare("UPDATE user_settings SET theme = ? WHERE user_id = ?");
    $stmt->execute([$theme, $user_id]);
    echo json_encode(['success' => true]);
    exit;
}

// Handle full settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'update_theme')) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $theme = $_POST['theme'] ?? $settings['theme'];
    $model = $_POST['model'] ?? $settings['model'];
    $temperature = (float)($_POST['temperature'] ?? $settings['temperature']);
    $max_tokens = (int)($_POST['max_tokens'] ?? $settings['max_tokens']);
    $language = $_POST['language'] ?? $settings['language'];
    
    // Validate
    if ($temperature < 0 || $temperature > 2) {
        $error = 'Temperature must be between 0 and 2.';
    } elseif ($max_tokens < 256 || $max_tokens > 8192) {
        $error = 'Max tokens must be between 256 and 8192.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE user_settings SET theme = ?, model = ?, temperature = ?, max_tokens = ?, language = ? WHERE user_id = ?"
        );
        $stmt->execute([$theme, $model, $temperature, $max_tokens, $language, $user_id]);
        $success = 'Settings updated successfully!';
        $_SESSION['success'] = $success;
        
        // Refresh settings
        $settings = [
            'theme' => $theme,
            'model' => $model,
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
            'language' => $language
        ];
    }
}

require_once '../includes/header.php';

// Available models (can be expanded)
$available_models = [
    'mistral-tiny-latest' => 'Mistral Tiny (Fast, Cheap)',
    'mistral-small-latest' => 'Mistral Small (Balanced)',
    'mistral-medium-latest' => 'Mistral Medium (More Capable)',
    'mistral-large-latest' => 'Mistral Large (Most Capable)',
];

// Available languages
$languages = [
    'en' => 'English',
    'es' => 'Español',
    'fr' => 'Français',
    'de' => 'Deutsch',
    'it' => 'Italiano',
    'pt' => 'Português',
    'ru' => 'Русский',
    'zh' => '中文',
    'ja' => '日本語',
    'ar' => 'العربية',
];
?>

<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Settings</h3>
                <nav class="space-y-2">
                    <a href="/app/profile.php" class="block px-3 py-2 rounded text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        Profile
                    </a>
                    <a href="#app-settings" class="block px-3 py-2 rounded text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400">
                        App Settings
                    </a>
                    <a href="#chat-settings" class="block px-3 py-2 rounded text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        Chat Settings
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="md:col-span-2">
            <!-- App Settings Section -->
            <div id="app-settings" class="card mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">App Settings</h3>
                
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Theme
                        </label>
                        <select name="theme" class="form-input">
                            <option value="light" <?= ($settings['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>>
                                Light Mode
                            </option>
                            <option value="dark" <?= ($settings['theme'] ?? 'light') === 'dark' ? 'selected' : '' ?>>
                                Dark Mode
                            </option>
                            <option value="system" <?= ($settings['theme'] ?? 'light') === 'system' ? 'selected' : '' ?>>
                                System Default
                            </option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Language
                        </label>
                        <select name="language" class="form-input">
                            <?php foreach ($languages as $code => $name): ?>
                                <option value="<?= $code ?>" <?= ($settings['language'] ?? 'en') === $code ? 'selected' : '' ?>>
                                    <?= h($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Save App Settings
                    </button>
                </form>
            </div>
            
            <!-- Chat Settings Section -->
            <div id="chat-settings" class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Chat Settings</h3>
                
                <?php if ($error): ?>
                    <div class="alert-error mb-4"><?= h($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-success mb-4"><?= h($success) ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            AI Model
                        </label>
                        <select name="model" class="form-input">
                            <?php foreach ($available_models as $model_id => $model_name): ?>
                                <option value="<?= $model_id ?>" <?= ($settings['model'] ?? 'mistral-small-latest') === $model_id ? 'selected' : '' ?>>
                                    <?= h($model_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Higher capability models use more tokens and may be slower.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Temperature: <span id="temp-value" class="font-mono"><?= $settings['temperature'] ?? 0.7 ?></span>
                        </label>
                        <input type="range" id="temperature" name="temperature" 
                               min="0" max="2" step="0.1" 
                               value="<?= $settings['temperature'] ?? 0.7 ?>" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                               oninput="document.getElementById('temp-value').textContent = this.value">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>Precise</span>
                            <span>Balanced</span>
                            <span>Creative</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Lower values produce more focused responses. Higher values produce more creative responses.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="max_tokens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Max Tokens: <span id="tokens-value" class="font-mono"><?= $settings['max_tokens'] ?? 2048 ?></span>
                        </label>
                        <input type="range" id="max_tokens" name="max_tokens" 
                               min="256" max="8192" step="256" 
                               value="<?= $settings['max_tokens'] ?? 2048 ?>" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                               oninput="document.getElementById('tokens-value').textContent = this.value">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>256</span>
                            <span>4096</span>
                            <span>8192</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Maximum number of tokens the AI can use in its response. Longer responses use more tokens.
                        </p>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Save Chat Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
