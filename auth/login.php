<?php
/**
 * 369Network - Login Authentication Handler
 */

session_start();

// Include config
require_once '../includes/config.php';

// Demo users for testing (with actual passwords)
$users = [
    'contact@369network.com' => [
        'id' => 1,
        'username' => 'contact@369network.com',
        'password' => 'Spidigoo@#369',
        'role' => 'admin',
        'name' => 'Nipam Patel',
        'email' => 'contact@369network.com',
        'client_id' => null
    ],
    'Usman@369network.com' => [
        'id' => 2,
        'username' => 'Usman@369network.com',
        'password' => 'Password@#123',
        'role' => 'client',
        'name' => 'Usmanbhai',
        'email' => 'Usman@369network.com',
        'client_id' => 'USM001'
    ],
    'vpmedia@369network.com' => [
        'id' => 3,
        'username' => 'vpmedia@369network.com',
        'password' => 'Password@#123',
        'role' => 'client',
        'name' => 'VP Media (Priyankbhai)',
        'email' => 'vpmedia@369network.com',
        'client_id' => 'VPM001'
    ],
    'thebes@369network.com' => [
        'id' => 4,
        'username' => 'thebes@369network.com',
        'password' => 'Password@#123',
        'role' => 'client',
        'name' => 'Thebes Media',
        'email' => 'thebes@369network.com',
        'client_id' => 'THB001'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=invalid');
        exit;
    }
    
    // Check user exists
    $user = null;
    
    // First try demo users
    if (isset($users[$username])) {
        $user = $users[$username];
    } else {
        // Try database lookup
        try {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT u.*, c.client_code as client_id
                FROM users u
                LEFT JOIN clients c ON u.id = c.user_id
                WHERE u.username = ? OR u.email = ?
                LIMIT 1
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            // Database not available, continue with demo users
        }
    }
    
    if (!$user) {
        header('Location: ../login.php?error=invalid');
        exit;
    }
    
    // Verify password
    // For demo users, check plain password directly
    $passwordValid = false;
    if (isset($users[$username])) {
        // Demo user - check against stored password
        $passwordValid = ($password === $users[$username]['password']);
    } else {
        // Database user - verify hash
        $passwordValid = password_verify($password, $user['password']);
    }
    
    if (!$passwordValid) {
        header('Location: ../login.php?error=invalid');
        exit;
    }
    
    // Check if user is active
    if (isset($user['status']) && $user['status'] !== 'active') {
        header('Location: ../login.php?error=inactive');
        exit;
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['client_id'] = $user['client_id'] ?? null;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    // Set remember me cookie (30 days)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        
        // Store token in database/session for verification
        $_SESSION['remember_token'] = $token;
    }
    
    // Log login activity
    logActivity($user['id'], 'login', 'User logged in');
    
    // Redirect based on role
    if ($user['role'] === 'admin') {
        header('Location: ../index.php');
    } else {
        header('Location: ../client-dashboard.php');
    }
    exit;
    
} else {
    // Not a POST request
    header('Location: ../login.php');
    exit;
}

/**
 * Log user activity
 */
function logActivity($userId, $action, $details = '') {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO activity_log (user_id, action, details, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        // Silently fail if logging doesn't work
    }
}
