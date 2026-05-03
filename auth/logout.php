<?php
require_once '../includes/functions.php';

// Destroy session and logout
user_logout();

// Clear remember me cookies
setcookie('aivyra_remember', '', time() - 3600, '/');
setcookie('aivyra_user', '', time() - 3600, '/');
setcookie('aivyra_token', '', time() - 3600, '/');

// Redirect to login page
redirect('/auth/login.php');
