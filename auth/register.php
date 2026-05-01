<?php
require_once '../includes/functions.php';
require_once '../includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        // Check email uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            redirect('../app/index.php');
        }
    }
}
?>

<div class="max-w-md mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-4">Register</h2>
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
            <input type="text" id="name" name="name" value="<?= isset($_POST['name']) ? h($_POST['name']) : '' ?>" 
                   class="form-input" required autofocus>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>" 
                   class="form-input" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
            <input type="password" id="password" name="password" 
                   class="form-input" required minlength="8" placeholder="At least 8 characters">
        </div>
        
        <div class="mb-6">
            <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" 
                   class="form-input" required>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition">Create Account</button>
    </form>
    
    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Already have an account? <a href="login.php" class="text-blue-600 hover:text-blue-700 font-medium">Login</a>
    </p>
</div>

<?php require_once '../includes/footer.php'; ?>

<?php require_once '../includes/footer.php'; ?>
