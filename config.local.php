<?php
// Local Development Configuration for XAMPP
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'club_dadvice_local'); // Local database name
define('DB_USER', 'root'); // Default XAMPP MySQL user
define('DB_PASS', ''); // Default XAMPP MySQL password (empty)

// Site configuration
define('SITE_NAME', 'Club Dadvice - Local Dev');
define('SITE_TAGLINE', 'Where dads level up to raise world-class kids');
define('BASE_URL', 'http://localhost/parentTime'); // Update path as needed

// Email configuration (for local testing)
define('EMAIL_USE_SMTP', false); // Keep false for local testing
define('EMAIL_SMTP_HOST', 'localhost');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_SECURE', 'tls');
define('EMAIL_SMTP_USERNAME', '');
define('EMAIL_SMTP_PASSWORD', '');
define('EMAIL_FROM_ADDRESS', 'noreply@localhost');
define('EMAIL_FROM_NAME', 'Club Dadvice Local');

// Database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Security helper functions
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireCSRFToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            throw new Exception('Invalid security token. Please refresh the page and try again.');
        }
    }
}

// Rate limiting functions
function getRateLimitKey($identifier, $action = 'general') {
    return 'rate_limit_' . $action . '_' . md5($identifier);
}

function checkRateLimit($identifier, $action = 'general', $max_attempts = 5, $window_minutes = 15) {
    $key = getRateLimitKey($identifier, $action);
    $cache_file = 'cache/' . $key . '.json';
    
    // Ensure cache directory exists
    if (!is_dir('cache')) {
        mkdir('cache', 0755, true);
    }
    
    $now = time();
    $window_seconds = $window_minutes * 60;
    
    // Load existing attempts
    $attempts = [];
    if (file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true);
        if ($data && isset($data['attempts'])) {
            // Filter out old attempts
            $attempts = array_filter($data['attempts'], function($timestamp) use ($now, $window_seconds) {
                return ($now - $timestamp) < $window_seconds;
            });
        }
    }
    
    // Check if limit exceeded
    if (count($attempts) >= $max_attempts) {
        $oldest_attempt = min($attempts);
        $reset_time = $oldest_attempt + $window_seconds;
        throw new Exception("Too many attempts. Please try again in " . ceil(($reset_time - $now) / 60) . " minutes.");
    }
    
    // Record this attempt
    $attempts[] = $now;
    file_put_contents($cache_file, json_encode(['attempts' => $attempts]));
    
    return true;
}

function clearRateLimit($identifier, $action = 'general') {
    $key = getRateLimitKey($identifier, $action);
    $cache_file = 'cache/' . $key . '.json';
    if (file_exists($cache_file)) {
        unlink($cache_file);
    }
}
?>