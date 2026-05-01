<?php
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($pdo, $user_id);

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($name)) {
        $error = 'Name is required.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email is required.';
    } elseif ($user['email'] !== $email && !is_email_available($pdo, $email, $user_id)) {
        $error = 'Email already in use.';
    } else {
        // Handle avatar upload
        $avatar = $user['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $mime = mime_content_type($_FILES['avatar']['tmp_name']);
            
            if (!in_array($mime, $allowed)) {
                $error = 'Only JPG, PNG, and WebP images are allowed.';
            } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                $error = 'Avatar must be less than 2MB.';
            } else {
                // Create avatars directory if not exists
                $avatar_dir = __DIR__ . '/assets/avatars';
                if (!is_dir($avatar_dir)) {
                    mkdir($avatar_dir, 0755, true);
                }
                
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('avatar_', true) . '.' . $ext;
                $destination = $avatar_dir . '/' . $filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                    // Remove old avatar if not default
                    if ($user['avatar'] && $user['avatar'] !== 'default.png' && file_exists($avatar_dir . '/' . $user['avatar'])) {
                        unlink($avatar_dir . '/' . $user['avatar']);
                    }
                    $avatar = $filename;
                }
            }
        }
        
        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$name, $email, $avatar, $user_id]);
            $success = 'Profile updated successfully!';
            $_SESSION['success'] = $success;
            // Refresh user data
            $user = get_user_by_id($pdo, $user_id);
        }
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password)) {
        $error = 'Current password is required.';
    } elseif (empty($new_password) || strlen($new_password) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } elseif (!verify_user_password($pdo, $user_id, $current_password)) {
        $error = 'Current password is incorrect.';
    } else {
        update_user_password($pdo, $user_id, $new_password);
        $success = 'Password updated successfully!';
        $_SESSION['success'] = $success;
    }
}

require_once '../includes/header.php';
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Profile Settings</h3>
                <nav class="space-y-2">
                    <a href="#profile" class="block px-3 py-2 rounded text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400">
                        Edit Profile
                    </a>
                    <a href="#password" class="block px-3 py-2 rounded text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        Change Password
                    </a>
                    <a href="/app/settings.php" class="block px-3 py-2 rounded text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        App Settings
                    </a>
                </nav>
            </div>
            
            <!-- Profile Preview -->
            <div class="card mt-6 text-center">
                <img src="<?= get_user_avatar($pdo, $user_id) ?>" alt="Profile Avatar" 
                     class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                <h4 class="font-semibold text-gray-900 dark:text-white"><?= h($user['name']) ?></h4>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= h($user['email']) ?></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                </p>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="md:col-span-2">
            <!-- Profile Section -->
            <div id="profile" class="card mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Edit Profile</h3>
                
                <?php if ($error && isset($_POST['action']) && $_POST['action'] === 'update_profile'): ?>
                    <div class="alert-error mb-4"><?= h($error) ?></div>
                <?php endif; ?>
                <?php if ($success && isset($_POST['action']) && $_POST['action'] === 'update_profile'): ?>
                    <div class="alert-success mb-4"><?= h($success) ?></div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Profile Picture
                        </label>
                        <div class="flex items-center space-x-4">
                            <img src="<?= get_user_avatar($pdo, $user_id) ?>" alt="Current Avatar" 
                                 class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <input type="file" name="avatar" id="avatar" 
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          dark:file:bg-blue-900/30 dark:file:text-blue-300
                                          dark:hover:file:bg-blue-800/30">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    JPG, PNG, or WebP. Max 2MB.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Full Name
                        </label>
                        <input type="text" id="name" name="name" value="<?= h($user['name']) ?>" 
                               class="form-input" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="<?= h($user['email']) ?>" 
                               class="form-input" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Save Changes
                    </button>
                </form>
            </div>
            
            <!-- Password Section -->
            <div id="password" class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Change Password</h3>
                
                <?php if ($error && isset($_POST['action']) && $_POST['action'] === 'update_password'): ?>
                    <div class="alert-error mb-4"><?= h($error) ?></div>
                <?php endif; ?>
                <?php if ($success && isset($_POST['action']) && $_POST['action'] === 'update_password'): ?>
                    <div class="alert-success mb-4"><?= h($success) ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <input type="hidden" name="action" value="update_password">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-input" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            New Password
                        </label>
                        <input type="password" id="new_password" name="new_password" 
                               class="form-input" required minlength="8">
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Confirm New Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-input" required minlength="8">
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
