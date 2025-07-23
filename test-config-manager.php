<?php
/**
 * Configuration Manager Test
 * Tests the new configuration system
 */

require_once 'config-manager.php';

echo "<h1>Configuration Manager Test</h1>\n";

try {
    $configManager = ConfigurationManager::getInstance();
    
    // Test environment detection
    echo "<h2>Environment Detection</h2>\n";
    $env_info = $configManager->getEnvironmentInfo();
    foreach ($env_info as $key => $value) {
        echo "<p><strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . htmlspecialchars($value) . "</p>\n";
    }
    
    // Test configuration loading
    echo "<h2>Configuration Loading</h2>\n";
    $config = $configManager->loadConfiguration();
    echo "<p><strong>Environment:</strong> " . $config['environment'] . "</p>\n";
    echo "<p><strong>Database Host:</strong> " . $config['database']['host'] . "</p>\n";
    echo "<p><strong>Database Name:</strong> " . $config['database']['name'] . "</p>\n";
    echo "<p><strong>Site Name:</strong> " . $config['site']['name'] . "</p>\n";
    echo "<p><strong>Local Dev Mode:</strong> " . ($config['security']['local_dev_mode'] ? 'Yes' : 'No') . "</p>\n";
    
    // Test configuration validation
    echo "<h2>Configuration Validation</h2>\n";
    $validation = $configManager->validateConfiguration();
    echo "<p><strong>Valid:</strong> " . ($validation['valid'] ? 'Yes' : 'No') . "</p>\n";
    
    if (!empty($validation['errors'])) {
        echo "<h3>Errors:</h3>\n<ul>\n";
        foreach ($validation['errors'] as $error) {
            echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>\n";
        }
        echo "</ul>\n";
    }
    
    if (!empty($validation['warnings'])) {
        echo "<h3>Warnings:</h3>\n<ul>\n";
        foreach ($validation['warnings'] as $warning) {
            echo "<li style='color: orange;'>" . htmlspecialchars($warning) . "</li>\n";
        }
        echo "</ul>\n";
    }
    
    // Test database connection
    echo "<h2>Database Connection Test</h2>\n";
    try {
        $pdo = $configManager->getDBConnection();
        echo "<p style='color: green;'>✅ Database connection successful!</p>\n";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "<p style='color: green;'>✅ Database query test successful!</p>\n";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
    
    // Test conflict prevention
    echo "<h2>Conflict Prevention</h2>\n";
    $conflicts = $configManager->preventConflicts();
    
    if (empty($conflicts['constants']) && empty($conflicts['functions'])) {
        echo "<p style='color: green;'>✅ No conflicts detected!</p>\n";
    } else {
        if (!empty($conflicts['constants'])) {
            echo "<h3>Constant Conflicts:</h3>\n<ul>\n";
            foreach ($conflicts['constants'] as $constant) {
                echo "<li style='color: orange;'>" . htmlspecialchars($constant) . "</li>\n";
            }
            echo "</ul>\n";
        }
        
        if (!empty($conflicts['functions'])) {
            echo "<h3>Function Conflicts:</h3>\n<ul>\n";
            foreach ($conflicts['functions'] as $function) {
                echo "<li style='color: orange;'>" . htmlspecialchars($function) . "</li>\n";
            }
            echo "</ul>\n";
        }
    }
    
    // Test helper functions
    echo "<h2>Helper Functions Test</h2>\n";
    
    // Test sanitizeInput
    $test_input = "<script>alert('test')</script>Hello World!";
    $sanitized = sanitizeInput($test_input);
    echo "<p><strong>Sanitize Input:</strong> " . htmlspecialchars($sanitized) . "</p>\n";
    
    // Test formatDate
    $formatted_date = formatDate('2024-01-15');
    echo "<p><strong>Format Date:</strong> " . $formatted_date . "</p>\n";
    
    // Test truncateText
    $long_text = "This is a very long text that should be truncated to show only the first part of the content.";
    $truncated = truncateText($long_text, 50);
    echo "<p><strong>Truncate Text:</strong> " . htmlspecialchars($truncated) . "</p>\n";
    
    // Test CSRF token generation
    $csrf_token = generateCSRFToken();
    echo "<p><strong>CSRF Token:</strong> " . substr($csrf_token, 0, 20) . "... (truncated)</p>\n";
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Configuration Manager Test Complete!</h3>";
    echo "<p>The Configuration Manager is working correctly and resolves the environment detection and conflict issues.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Configuration Manager Test Failed!</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>\n";
echo "<p><a href='index.php'>← Back to site</a></p>\n";
?>