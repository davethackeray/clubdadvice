<?php
/**
 * Configuration Manager
 * Handles environment detection and conflict-free configuration loading
 */

class ConfigurationManager {
    private static $instance = null;
    private $environment = null;
    private $configuration = null;
    private $loaded_functions = [];
    private $loaded_constants = [];
    
    private function __construct() {
        $this->detectEnvironment();
        $this->loadConfiguration();
    }
    
    public static function getInstance(): ConfigurationManager {
        if (self::$instance === null) {
            self::$instance = new ConfigurationManager();
        }
        return self::$instance;
    }
    
    /**
     * Detect the current environment
     */
    public function detectEnvironment(): string {
        if ($this->environment !== null) {
            return $this->environment;
        }
        
        // CLI environment detection
        if (php_sapi_name() === 'cli') {
            $this->environment = 'local';
            return $this->environment;
        }
        
        // Web environment detection
        $is_local = (
            (isset($_SERVER['HTTP_HOST']) && (
                $_SERVER['HTTP_HOST'] === 'localhost' ||
                strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
                strpos($_SERVER['HTTP_HOST'], 'localhost:') !== false
            )) ||
            !isset($_SERVER['HTTP_HOST']) // Fallback for CLI
        );
        
        $this->environment = $is_local ? 'local' : 'production';
        return $this->environment;
    }
    
    /**
     * Load appropriate configuration without conflicts
     */
    public function loadConfiguration(): array {
        if ($this->configuration !== null) {
            return $this->configuration;
        }
        
        $environment = $this->detectEnvironment();
        
        // Load configuration based on environment
        if ($environment === 'local' && file_exists(__DIR__ . '/../config.local.php')) {
            $this->configuration = $this->loadConfigFile(__DIR__ . '/../config.local.php');
        } else {
            $this->configuration = $this->loadConfigFile(__DIR__ . '/../config.php');
        }
        
        return $this->configuration;
    }
    
    /**
     * Load configuration file safely without conflicts
     */
    private function loadConfigFile(string $file_path): array {
        // Capture current state
        $defined_constants_before = get_defined_constants(true)['user'] ?? [];
        $defined_functions_before = get_defined_functions()['user'] ?? [];
        
        // Load the configuration file
        require_once $file_path;
        
        // Capture new state
        $defined_constants_after = get_defined_constants(true)['user'] ?? [];
        $defined_functions_after = get_defined_functions()['user'] ?? [];
        
        // Track what was loaded
        $this->loaded_constants = array_diff_key($defined_constants_after, $defined_constants_before);
        $this->loaded_functions = array_diff($defined_functions_after, $defined_functions_before);
        
        // Return configuration array
        return [
            'environment' => $this->environment,
            'database' => [
                'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
                'name' => defined('DB_NAME') ? DB_NAME : '',
                'user' => defined('DB_USER') ? DB_USER : '',
                'pass' => defined('DB_PASS') ? DB_PASS : ''
            ],
            'site' => [
                'name' => defined('SITE_NAME') ? SITE_NAME : 'Club Dadvice',
                'tagline' => defined('SITE_TAGLINE') ? SITE_TAGLINE : 'Where dads level up',
                'base_url' => defined('BASE_URL') ? BASE_URL : 'http://localhost'
            ],
            'email' => [
                'use_smtp' => defined('EMAIL_USE_SMTP') ? EMAIL_USE_SMTP : false,
                'smtp_host' => defined('EMAIL_SMTP_HOST') ? EMAIL_SMTP_HOST : 'localhost',
                'smtp_port' => defined('EMAIL_SMTP_PORT') ? EMAIL_SMTP_PORT : 587,
                'smtp_secure' => defined('EMAIL_SMTP_SECURE') ? EMAIL_SMTP_SECURE : 'tls',
                'smtp_username' => defined('EMAIL_SMTP_USERNAME') ? EMAIL_SMTP_USERNAME : '',
                'smtp_password' => defined('EMAIL_SMTP_PASSWORD') ? EMAIL_SMTP_PASSWORD : '',
                'from_address' => defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : 'noreply@localhost',
                'from_name' => defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'Club Dadvice'
            ],
            'security' => [
                'local_dev_mode' => defined('LOCAL_DEV_MODE') ? LOCAL_DEV_MODE : false
            ]
        ];
    }
    
    /**
     * Validate that all required configuration is present
     */
    public function validateConfiguration(): array {
        $config = $this->loadConfiguration();
        $validation_result = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];
        
        // Required database settings
        if (empty($config['database']['host'])) {
            $validation_result['errors'][] = 'Database host is required';
            $validation_result['valid'] = false;
        }
        
        if (empty($config['database']['name'])) {
            $validation_result['errors'][] = 'Database name is required';
            $validation_result['valid'] = false;
        }
        
        if (empty($config['database']['user'])) {
            $validation_result['errors'][] = 'Database user is required';
            $validation_result['valid'] = false;
        }
        
        // Required site settings
        if (empty($config['site']['name'])) {
            $validation_result['warnings'][] = 'Site name not configured, using default';
        }
        
        if (empty($config['site']['base_url'])) {
            $validation_result['warnings'][] = 'Base URL not configured, using default';
        }
        
        // Email configuration warnings
        if ($config['email']['use_smtp'] && empty($config['email']['smtp_username'])) {
            $validation_result['warnings'][] = 'SMTP enabled but no username configured';
        }
        
        return $validation_result;
    }
    
    /**
     * Get configuration value by key path (e.g., 'database.host')
     */
    public function get(string $key_path, $default = null) {
        $config = $this->loadConfiguration();
        $keys = explode('.', $key_path);
        $value = $config;
        
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $default;
            }
            $value = $value[$key];
        }
        
        return $value;
    }
    
    /**
     * Get database connection using configuration
     */
    public function getDBConnection(): PDO {
        $config = $this->loadConfiguration();
        
        try {
            $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['database']['user'], $config['database']['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch(PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Check if we're in local development mode
     */
    public function isLocalDevelopment(): bool {
        return $this->detectEnvironment() === 'local';
    }
    
    /**
     * Get environment information for debugging
     */
    public function getEnvironmentInfo(): array {
        return [
            'environment' => $this->detectEnvironment(),
            'php_sapi' => php_sapi_name(),
            'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A (CLI)',
            'loaded_constants' => count($this->loaded_constants),
            'loaded_functions' => count($this->loaded_functions),
            'config_file' => $this->isLocalDevelopment() ? 'config.local.php' : 'config.php'
        ];
    }
    
    /**
     * Prevent conflicts by checking if functions/constants already exist
     */
    public function preventConflicts(): array {
        $conflicts = [
            'constants' => [],
            'functions' => []
        ];
        
        // Check for constant conflicts
        $critical_constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SITE_NAME', 'BASE_URL'];
        foreach ($critical_constants as $constant) {
            if (defined($constant)) {
                $conflicts['constants'][] = $constant;
            }
        }
        
        // Check for function conflicts
        $critical_functions = ['getDBConnection', 'sanitizeInput', 'generateCSRFToken'];
        foreach ($critical_functions as $function) {
            if (function_exists($function)) {
                $conflicts['functions'][] = $function;
            }
        }
        
        return $conflicts;
    }
}

// Global helper functions that use the ConfigurationManager
if (!function_exists('getConfigManager')) {
    function getConfigManager(): ConfigurationManager {
        return ConfigurationManager::getInstance();
    }
}

if (!function_exists('getDBConnection')) {
    function getDBConnection(): PDO {
        return getConfigManager()->getDBConnection();
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input): string {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date): string {
        return date('F j, Y', strtotime($date));
    }
}

if (!function_exists('truncateText')) {
    function truncateText($text, $length = 150): string {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('requireCSRFToken')) {
    function requireCSRFToken(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!validateCSRFToken($token)) {
                throw new Exception('Invalid security token. Please refresh the page and try again.');
            }
        }
    }
}

if (!function_exists('checkRateLimit')) {
    function checkRateLimit($identifier, $action = 'general', $max_attempts = 5, $window_minutes = 15): bool {
        $key = 'rate_limit_' . $action . '_' . md5($identifier);
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
}
?>