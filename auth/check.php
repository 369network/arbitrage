<?php
/**
 * 369Network - Session Check Middleware
 * Include this at the top of every protected page
 */

session_start();

// Session timeout (2 hours)
define('SESSION_TIMEOUT', 2 * 60 * 60);

/**
 * Check if user is logged in
 */
function checkAuth() {
    // Check if session exists
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        redirectToLogin('expired');
    }
    
    // Check session timeout
    if (isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            // Session expired
            session_destroy();
            redirectToLogin('expired');
        }
        
        // Update last activity time
        $_SESSION['login_time'] = time();
    }
    
    // Check remember me cookie for extended sessions
    if (isset($_COOKIE['remember_token']) && isset($_SESSION['remember_token'])) {
        if ($_COOKIE['remember_token'] === $_SESSION['remember_token']) {
            // Extend session
            $_SESSION['login_time'] = time();
        }
    }
}

/**
 * Check if user is admin
 */
function checkAdmin() {
    checkAuth();
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        // Not an admin, redirect to client dashboard
        header('Location: client-dashboard.php');
        exit;
    }
}

/**
 * Check if user is a client
 */
function checkClient() {
    checkAuth();
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
        // Not a client, redirect to admin dashboard
        header('Location: index.php');
        exit;
    }
}

/**
 * Get current user info
 */
function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? 'Guest',
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'client_id' => $_SESSION['client_id'] ?? null
    ];
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if current user is client
 */
function isClient() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'client';
}

/**
 * Get current client ID
 */
function getClientId() {
    return $_SESSION['client_id'] ?? null;
}

/**
 * Redirect to login page
 */
function redirectToLogin($error = null) {
    $url = 'login.php';
    if ($error) {
        $url .= '?error=' . urlencode($error);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output CSRF hidden field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}
