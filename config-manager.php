<?php
/**
 * Configuration Manager Bootstrap
 * Clean, conflict-free configuration loading
 */

require_once __DIR__ . '/classes/ConfigurationManager.php';

// Initialize the configuration manager
$configManager = ConfigurationManager::getInstance();

// Make configuration available globally
$GLOBALS['config'] = $configManager->loadConfiguration();

// Validate configuration
$validation = $configManager->validateConfiguration();
if (!$validation['valid']) {
    error_log("Configuration validation failed: " . implode(', ', $validation['errors']));
}

// Log warnings in development
if ($configManager->isLocalDevelopment() && !empty($validation['warnings'])) {
    foreach ($validation['warnings'] as $warning) {
        error_log("Configuration warning: " . $warning);
    }
}
?>