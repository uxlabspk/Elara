<?php
// includes/auth.php
// Authentication helper functions

/**
 * Get user by ID
 */
function get_user_by_id($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

/**
 * Get user by email
 */
function get_user_by_email($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

/**
 * Get user avatar URL
 */
function get_user_avatar($pdo, $user_id) {
    $user = get_user_by_id($pdo, $user_id);
    if ($user && !empty($user['avatar']) && $user['avatar'] !== 'default.png') {
        return 'assets/avatars/' . h($user['avatar']);
    }
    // Return default avatar or gravatar
    $user = get_user_by_id($pdo, $user_id);
    if ($user) {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['email']))) . '?d=mp&s=40';
    }
    return 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23666"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';
}

/**
 * Get user name
 */
function get_user_name($pdo, $user_id) {
    $user = get_user_by_id($pdo, $user_id);
    return $user ? $user['name'] : 'User';
}

/**
 * Get user email
 */
function get_user_email($pdo, $user_id) {
    $user = get_user_by_id($pdo, $user_id);
    return $user ? $user['email'] : '';
}

/**
 * Update user profile
 */
function update_user_profile($pdo, $user_id, $name, $email, $avatar = null) {
    $updates = [];
    $params = [];
    
    if ($name !== null) {
        $updates[] = "name = ?";
        $params[] = $name;
    }
    if ($email !== null) {
        $updates[] = "email = ?";
        $params[] = $email;
    }
    if ($avatar !== null) {
        $updates[] = "avatar = ?";
        $params[] = $avatar;
    }
    
    if (empty($updates)) {
        return true;
    }
    
    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Update user password
 */
function update_user_password($pdo, $user_id, $new_password) {
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hash, $user_id]);
}

/**
 * Verify user password
 */
function verify_user_password($pdo, $user_id, $password) {
    $user = get_user_by_id($pdo, $user_id);
    if ($user && password_verify($password, $user['password'])) {
        return true;
    }
    return false;
}

/**
 * Check if email is available (for registration/update)
 */
function is_email_available($pdo, $email, $exclude_user_id = null) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $params = [$email];
    if ($exclude_user_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_user_id;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return !$stmt->fetch();
}

/**
 * User login
 */
function user_login($pdo, $email, $password) {
    $user = get_user_by_email($pdo, $email);
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['logged_in_at'] = time();
        
        // Initialize settings if not exists
        $stmt = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)");
            $stmt->execute([$user['id']]);
        }
        
        return $user['id'];
    }
    return false;
}

/**
 * User logout
 */
function user_logout() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
}

/**
 * Get conversation by ID
 */
function get_conversation($pdo, $conversation_id, $user_id = null) {
    $sql = "SELECT * FROM conversations WHERE id = ?";
    $params = [$conversation_id];
    if ($user_id) {
        $sql .= " AND user_id = ?";
        $params[] = $user_id;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Get all conversations for a user
 */
function get_user_conversations($pdo, $user_id, $limit = null) {
    $sql = "SELECT * FROM conversations WHERE user_id = ? ORDER BY is_pinned DESC, updated_at DESC";
    $params = [$user_id];
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Delete conversation
 */
function delete_conversation($pdo, $conversation_id, $user_id) {
    // Verify ownership
    $conversation = get_conversation($pdo, $conversation_id, $user_id);
    if (!$conversation) {
        return false;
    }
    $stmt = $pdo->prepare("DELETE FROM conversations WHERE id = ? AND user_id = ?");
    return $stmt->execute([$conversation_id, $user_id]);
}

/**
 * Pin/Unpin conversation
 */
function toggle_pin_conversation($pdo, $conversation_id, $user_id) {
    $conversation = get_conversation($pdo, $conversation_id, $user_id);
    if (!$conversation) {
        return false;
    }
    $new_pinned = $conversation['is_pinned'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE conversations SET is_pinned = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$new_pinned, $conversation_id, $user_id]);
}

/**
 * Rename conversation
 */
function rename_conversation($pdo, $conversation_id, $user_id, $title) {
    $conversation = get_conversation($pdo, $conversation_id, $user_id);
    if (!$conversation) {
        return false;
    }
    $stmt = $pdo->prepare("UPDATE conversations SET title = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$title, $conversation_id, $user_id]);
}
