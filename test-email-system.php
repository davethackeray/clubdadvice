<?php
/**
 * Email System Test
 * Tests email functionality in local development mode
 */

require_once 'config-selector.php';
require_once 'classes/EmailService.php';

echo "<h1>Email System Test</h1>\n";

try {
    $emailService = new EmailService();
    
    // Test configuration
    echo "<h2>Email Configuration</h2>\n";
    $config = $emailService->testConfiguration();
    
    foreach ($config as $key => $value) {
        $status = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
        echo "<p><strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> {$status}</p>\n";
    }
    
    // Test verification email
    echo "<h2>Testing Verification Email</h2>\n";
    $result = $emailService->sendVerificationEmail(
        'test@example.com', 
        'test-token-123', 
        'Test Dad'
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ Verification email logged successfully!</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Verification email failed!</p>\n";
    }
    
    // Test password reset email
    echo "<h2>Testing Password Reset Email</h2>\n";
    $result = $emailService->sendPasswordResetEmail(
        'test@example.com', 
        'reset-token-456', 
        'Test Dad'
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ Password reset email logged successfully!</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Password reset email failed!</p>\n";
    }
    
    // Test welcome email
    echo "<h2>Testing Welcome Email</h2>\n";
    $result = $emailService->sendWelcomeEmail(
        'test@example.com', 
        'Test Dad'
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ Welcome email logged successfully!</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Welcome email failed!</p>\n";
    }
    
    // Check log files
    echo "<h2>Email Logs</h2>\n";
    $log_file = 'logs/emails_' . date('Y-m-d') . '.log';
    
    if (file_exists($log_file)) {
        echo "<p style='color: green;'>✅ Email log file exists: {$log_file}</p>\n";
        $log_size = filesize($log_file);
        echo "<p>Log file size: " . number_format($log_size) . " bytes</p>\n";
        
        // Show last few lines
        $log_content = file_get_contents($log_file);
        $lines = explode("\n", $log_content);
        $last_lines = array_slice($lines, -10);
        
        echo "<h3>Recent Log Entries:</h3>\n";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars(implode("\n", $last_lines));
        echo "</pre>\n";
    } else {
        echo "<p style='color: orange;'>⚠️ No email log file found</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><a href='index.php'>← Back to site</a></p>\n";
?>