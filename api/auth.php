<?php
/**
 * 369Network - API Authentication Middleware
 * Include this at the top of every API endpoint
 */

session_start();

/**
 * Check if API request is authenticated
 */
function checkAPIAuth() {
    // Check session authentication
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return true;
    }
    
    // Check API key authentication (for external integrations)
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? null;
    if ($apiKey) {
        // Validate API key (implement your own validation)
        if (validateAPIKey($apiKey)) {
            return true;
        }
    }
    
    // Not authenticated
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Unauthorized',
        'message' => 'Please login to access this resource'
    ]);
    exit;
}

/**
 * Check if user is admin for API requests
 */
function checkAPIAdmin() {
    checkAPIAuth();
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Forbidden',
            'message' => 'Admin access required'
        ]);
        exit;
    }
}

/**
 * Get current user from API context
 */
function getAPIUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'client_id' => $_SESSION['client_id'] ?? null
    ];
}

/**
 * Validate API key
 */
function validateAPIKey($key) {
    // Define valid API keys (in production, store in database)
    $validKeys = [
        'sk_369network_admin_key_2025',
        'sk_369network_readonly_key_2025'
    ];
    
    return in_array($key, $validKeys);
}

/**
 * Rate limiting (simple implementation)
 */
function checkRateLimit($identifier, $maxRequests = 100, $period = 3600) {
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 0,
            'start' => time()
        ];
    }
    
    $data = $_SESSION[$key];
    
    // Reset if period expired
    if (time() - $data['start'] > $period) {
        $_SESSION[$key] = [
            'count' => 1,
            'start' => time()
        ];
        return true;
    }
    
    // Check limit
    if ($data['count'] >= $maxRequests) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded. Try again later.',
            'retry_after' => $period - (time() - $data['start'])
        ]);
        exit;
    }
    
    // Increment counter
    $_SESSION[$key]['count']++;
    return true;
}
