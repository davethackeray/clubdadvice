<?php
/**
 * Security Setup Verification Script
 * Quick verification that all security enhancements are properly configured
 */

require_once 'config.php';

echo "<h1>🔒 Security Setup Verification</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
    .check { color: green; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .section { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
    pre { background: #e9ecef; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

$checks_passed = 0;
$total_checks = 0;

function checkResult($condition, $success_msg, $failure_msg) {
    global $checks_passed, $total_checks;
    $total_checks++;
    if ($condition) {
        echo "<span class='check'>✓ $success_msg</span><br>";
        $checks_passed++;
        return true;
    } else {
        echo "<span class='error'>✗ $failure_msg</span><br>";
        return false;
    }
}

function warningResult($condition, $warning_msg) {
    if (!$condition) {
        echo "<span class='warning'>⚠️ $warning_msg</span><br>";
    }
}

echo "<div class='section'>";
echo "<h2>1. File Structure Check</h2>";

$required_files = [
    'classes/User.php' => 'User class exists',
    'classes/EmailService.php' => 'EmailService class exists',
    'test-auth-system.php' => 'Auth system test exists',
    'test-email-system.php' => 'Email system test exists',
    'SECURITY_ENHANCEMENTS.md' => 'Security documentation exists'
];

foreach ($required_files as $file => $description) {
    checkResult(file_exists($file), $description, "$description - FILE MISSING");
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>2. Configuration Check</h2>";

checkResult(defined('SITE_NAME'), 'Site name configured', 'Site name not configured');
checkResult(defined('BASE_URL'), 'Base URL configured', 'Base URL not configured');
checkResult(defined('EMAIL_FROM_ADDRESS'), 'Email from address configured', 'Email from address not configured');
checkResult(defined('EMAIL_SMTP_HOST'), 'SMTP host configured', 'SMTP host not configured');

warningResult(EMAIL_USE_SMTP, 'SMTP is disabled - using mail() function');
warningResult(!empty(EMAIL_SMTP_USERNAME), 'SMTP username not configured');
warningResult(!empty(EMAIL_SMTP_PASSWORD), 'SMTP password not configured');

echo "</div>";

echo "<div class='section'>";
echo "<h2>3. Security Functions Check</h2>";

checkResult(function_exists('generateCSRFToken'), 'CSRF token generation function exists', 'CSRF token function missing');
checkResult(function_exists('validateCSRFToken'), 'CSRF token validation function exists', 'CSRF validation function missing');
checkResult(function_exists('requireCSRFToken'), 'CSRF requirement function exists', 'CSRF requirement function missing');
checkResult(function_exists('checkRateLimit'), 'Rate limiting function exists', 'Rate limiting function missing');
checkResult(function_exists('clearRateLimit'), 'Rate limit clearing function exists', 'Rate limit clearing function missing');

echo "</div>";

echo "<div class='section'>";
echo "<h2>4. Directory Permissions Check</h2>";

$cache_dir = 'cache';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
    echo "<span class='check'>✓ Created cache directory</span><br>";
} else {
    echo "<span class='check'>✓ Cache directory exists</span><br>";
}

checkResult(is_writable($cache_dir), 'Cache directory is writable', 'Cache directory is not writable - rate limiting may fail');

echo "</div>";

echo "<div class='section'>";
echo "<h2>5. Database Connection Check</h2>";

try {
    $pdo = getDBConnection();
    echo "<span class='check'>✓ Database connection successful</span><br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    checkResult($stmt->rowCount() > 0, 'Users table exists', 'Users table missing');
    
    // Check for required columns
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $required_columns = ['email_verified', 'email_verification_token', 'password_reset_token', 'password_reset_expires'];
        foreach ($required_columns as $column) {
            checkResult(in_array($column, $columns), "Column '$column' exists", "Column '$column' missing");
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>6. Class Loading Check</h2>";

try {
    require_once 'classes/User.php';
    $user = new User();
    echo "<span class='check'>✓ User class loads successfully</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>✗ User class loading failed: " . $e->getMessage() . "</span><br>";
}

try {
    require_once 'classes/EmailService.php';
    $emailService = new EmailService();
    echo "<span class='check'>✓ EmailService class loads successfully</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>✗ EmailService class loading failed: " . $e->getMessage() . "</span><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>7. Session Check</h2>";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

checkResult(session_status() === PHP_SESSION_ACTIVE, 'Sessions are working', 'Sessions not working');

// Test CSRF token generation
try {
    $token = generateCSRFToken();
    checkResult(!empty($token), 'CSRF token generation works', 'CSRF token generation failed');
    checkResult(validateCSRFToken($token), 'CSRF token validation works', 'CSRF token validation failed');
} catch (Exception $e) {
    echo "<span class='error'>✗ CSRF token system error: " . $e->getMessage() . "</span><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>8. Rate Limiting Test</h2>";

try {
    $test_identifier = 'test_' . uniqid();
    checkRateLimit($test_identifier, 'test', 5, 1);
    echo "<span class='check'>✓ Rate limiting system works</span><br>";
    
    clearRateLimit($test_identifier, 'test');
    echo "<span class='check'>✓ Rate limit clearing works</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>✗ Rate limiting error: " . $e->getMessage() . "</span><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>📊 Summary</h2>";

$percentage = round(($checks_passed / $total_checks) * 100);
$status_class = $percentage >= 90 ? 'check' : ($percentage >= 70 ? 'warning' : 'error');

echo "<p><span class='$status_class'>Security Setup: $checks_passed/$total_checks checks passed ($percentage%)</span></p>";

if ($percentage >= 90) {
    echo "<p><span class='check'>🎉 Excellent! Your security setup is ready for production.</span></p>";
} elseif ($percentage >= 70) {
    echo "<p><span class='warning'>⚠️ Good setup, but some issues need attention before production.</span></p>";
} else {
    echo "<p><span class='error'>❌ Several critical issues need to be resolved before deployment.</span></p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>🔗 Next Steps</h2>";
echo "<ul>";
echo "<li><a href='test-auth-system.php'>Test Authentication System</a></li>";
echo "<li><a href='test-email-system.php'>Test Email System</a></li>";
echo "<li><a href='register.php'>Test User Registration</a></li>";
echo "<li><a href='login.php'>Test User Login</a></li>";
echo "<li>Review <code>SECURITY_ENHANCEMENTS.md</code> for detailed documentation</li>";
echo "</ul>";
echo "</div>";

echo "<p><small>Generated on " . date('Y-m-d H:i:s') . "</small></p>";
?>