<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = get_user_by_email($pdo, $email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in_at'] = time();
            
            // Initialize settings if not exists
            $stmt = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
                $stmt->execute([$user['id']]);
            }
            
            // Set cookie if remember me
            if ($remember) {
                setcookie('elara_remember', bin2hex(random_bytes(32)), time() + (30 * 24 * 60 * 60), '/', '', true, true);
                $hash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
                setcookie('elara_user', $user['id'], time() + (30 * 24 * 60 * 60), '/', '', true, true);
                setcookie('elara_token', $hash, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
            
            redirect('index.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto mt-20 px-4">
    <div class="card">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Login to Elara AI</h2>
        
        <?php if ($error): ?>
            <div class="alert-error mb-4"><?= h($error) ?></div>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= h($email) ?>" 
                       class="form-input" required autofocus>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                </label>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-300">
            <a href="register.php" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                Don't have an account? Register
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
