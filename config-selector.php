<?php
/**
 * Configuration Selector
 * Automatically detects environment and loads appropriate config
 */

// Detect if we're in local development environment
$is_local = (
    (isset($_SERVER['HTTP_HOST']) && (
        $_SERVER['HTTP_HOST'] === 'localhost' ||
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        strpos($_SERVER['HTTP_HOST'], 'localhost:') !== false
    )) ||
    !isset($_SERVER['HTTP_HOST']) // CLI environment
);

if ($is_local && file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
} else {
    require_once __DIR__ . '/config.php';
}
?>