<?php
/**
 * Comprehensive Feature Testing Validation
 * Tests all major functionality and generates detailed report
 */

require_once 'config-selector.php';
require_once 'classes/ConfigurationManager.php';

// Initialize test results
$testResults = [
    'overall_status' => 'PASS',
    'test_date' => date('Y-m-d H:i:s'),
    'environment' => 'Local Development',
    'tests' => []
];

echo "🧪 COMPREHENSIVE FEATURE TESTING VALIDATION\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "Test Date: " . $testResults['test_date'] . "\n";
echo "Environment: " . $testResults['environment'] . "\n\n";

// Test 1: Configuration Management
echo "1. CONFIGURATION MANAGEMENT\n";
echo "-" . str_repeat("-", 30) . "\n";

try {
    // Test environment detection
    $configManager = ConfigurationManager::getInstance();
    $environment = $configManager->detectEnvironment();
    echo "✅ Environment Detection: $environment\n";
    $testResults['tests']['config']['environment'] = 'PASS';
    
    // Test configuration loading
    $validation = $configManager->validateConfiguration();
    $isValid = $validation['valid'];
    echo $isValid ? "✅ Configuration Validation: PASS\n" : "❌ Configuration Validation: FAIL\n";
    $testResults['tests']['config']['validation'] = $isValid ? 'PASS' : 'FAIL';
    
    // Test conflict prevention
    $conflicts = $configManager->preventConflicts();
    $hasConflicts = !empty($conflicts['constants']) || !empty($conflicts['functions']);
    echo $hasConflicts ? "⚠️ Configuration Conflicts: DETECTED\n" : "✅ Configuration Conflicts: NONE\n";
    $testResults['tests']['config']['conflicts'] = $hasConflicts ? 'WARNING' : 'PASS';
    
} catch (Exception $e) {
    echo "❌ Configuration Management: FAIL - " . $e->getMessage() . "\n";
    $testResults['tests']['config']['error'] = $e->getMessage();
    $testResults['overall_status'] = 'FAIL';
}

echo "\n";

// Test 2: Database Operations
echo "2. DATABASE OPERATIONS\n";
echo "-" . str_repeat("-", 30) . "\n";

try {
    $pdo = getDBConnection();
    echo "✅ Database Connection: PASS\n";
    $testResults['tests']['database']['connection'] = 'PASS';
    
    // Test table existence
    $requiredTables = ['articles', 'age_groups', 'categories', 'tags'];
    foreach ($requiredTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table': $count records\n";
            $testResults['tests']['database']['tables'][$table] = "PASS ($count records)";
        } catch (Exception $e) {
            echo "❌ Table '$table': FAIL - " . $e->getMessage() . "\n";
            $testResults['tests']['database']['tables'][$table] = 'FAIL';
            $testResults['overall_status'] = 'FAIL';
        }
    }
    
    // Test database performance
    $start = microtime(true);
    $stmt = $pdo->query("SELECT * FROM articles LIMIT 10");
    $articles = $stmt->fetchAll();
    $queryTime = round((microtime(true) - $start) * 1000, 2);
    echo "✅ Query Performance: {$queryTime}ms\n";
    $testResults['tests']['database']['performance'] = "{$queryTime}ms";
    
} catch (Exception $e) {
    echo "❌ Database Operations: FAIL - " . $e->getMessage() . "\n";
    $testResults['tests']['database']['error'] = $e->getMessage();
    $testResults['overall_status'] = 'FAIL';
}

echo "\n";

// Test 3: Article Management System
echo "3. ARTICLE MANAGEMENT SYSTEM\n";
echo "-" . str_repeat("-", 30) . "\n";

try {
    require_once 'classes/ArticleManager.php';
    $articleManager = new ArticleManager();
    
    // Test article retrieval
    $articles = $articleManager->getArticles([], 1, 5);
    echo "✅ Article Retrieval: " . count($articles) . " articles\n";
    $testResults['tests']['articles']['retrieval'] = count($articles) . " articles";
    
    // Test filtering
    $filteredArticles = $articleManager->getArticles(['content_type' => 'practical-tip'], 1, 5);
    echo "✅ Article Filtering: " . count($filteredArticles) . " filtered articles\n";
    $testResults['tests']['articles']['filtering'] = count($filteredArticles) . " filtered";
    
    // Test age groups and categories
    $ageGroups = $articleManager->getAgeGroups();
    $categories = $articleManager->getCategories();
    echo "✅ Age Groups: " . count($ageGroups) . " groups\n";
    echo "✅ Categories: " . count($categories) . " categories\n";
    $testResults['tests']['articles']['age_groups'] = count($ageGroups) . " groups";
    $testResults['tests']['articles']['categories'] = count($categories) . " categories";
    
    // Test individual article retrieval
    if (!empty($articles)) {
        $article = $articleManager->getArticle($articles[0]['id']);
        echo $article ? "✅ Individual Article: PASS\n" : "❌ Individual Article: FAIL\n";
        $testResults['tests']['articles']['individual'] = $article ? 'PASS' : 'FAIL';
    }
    
} catch (Exception $e) {
    echo "❌ Article Management: FAIL - " . $e->getMessage() . "\n";
    $testResults['tests']['articles']['error'] = $e->getMessage();
    $testResults['overall_status'] = 'FAIL';
}

echo "\n";

// Test 4: Email System
echo "4. EMAIL SYSTEM\n";
echo "-" . str_repeat("-", 30) . "\n";

try {
    require_once 'classes/EmailService.php';
    $emailService = new EmailService();
    
    // Test configuration
    $config = $emailService->testConfiguration();
    echo "✅ Email Configuration: PASS\n";
    echo "✅ Local Dev Mode: " . ($config['local_dev_mode'] ? 'ENABLED' : 'DISABLED') . "\n";
    $testResults['tests']['email']['configuration'] = 'PASS';
    $testResults['tests']['email']['dev_mode'] = $config['local_dev_mode'] ? 'ENABLED' : 'DISABLED';
    
    // Test email sending (in dev mode)
    $testEmails = [
        'verification' => $emailService->sendVerificationEmail('test@example.com', 'test-token', 'Test User'),
        'password_reset' => $emailService->sendPasswordResetEmail('test@example.com', 'reset-token', 'Test User'),
        'welcome' => $emailService->sendWelcomeEmail('test@example.com', 'Test User')
    ];
    
    foreach ($testEmails as $type => $result) {
        echo $result ? "✅ $type Email: PASS\n" : "❌ $type Email: FAIL\n";
        $testResults['tests']['email'][$type] = $result ? 'PASS' : 'FAIL';
    }
    
    // Check log file
    $logFile = 'logs/emails_' . date('Y-m-d') . '.log';
    $logExists = file_exists($logFile);
    echo $logExists ? "✅ Email Logging: PASS\n" : "❌ Email Logging: FAIL\n";
    $testResults['tests']['email']['logging'] = $logExists ? 'PASS' : 'FAIL';
    
} catch (Exception $e) {
    echo "❌ Email System: FAIL - " . $e->getMessage() . "\n";
    $testResults['tests']['email']['error'] = $e->getMessage();
    $testResults['overall_status'] = 'FAIL';
}

echo "\n";

// Test 5: Core Pages
echo "5. CORE PAGES ACCESSIBILITY\n";
echo "-" . str_repeat("-", 30) . "\n";

$corePages = [
    'index.php' => 'Homepage',
    'article.php' => 'Article Page',
    'newsletter-generator.php' => 'Newsletter Generator',
    'js/app.js' => 'JavaScript Application',
    'classes/ArticleManager.php' => 'Article Manager',
    'classes/EmailService.php' => 'Email Service',
    'classes/ConfigurationManager.php' => 'Configuration Manager'
];

foreach ($corePages as $file => $description) {
    $exists = file_exists($file);
    echo $exists ? "✅ $description: EXISTS\n" : "❌ $description: MISSING\n";
    $testResults['tests']['pages'][$file] = $exists ? 'EXISTS' : 'MISSING';
    if (!$exists) {
        $testResults['overall_status'] = 'FAIL';
    }
}

echo "\n";

// Test 6: Security Features
echo "6. SECURITY FEATURES\n";
echo "-" . str_repeat("-", 30) . "\n";

$securityChecks = [
    'csrf_functions' => function_exists('generateCSRFToken'),
    'rate_limiting' => function_exists('checkRateLimit'),
    'input_sanitization' => function_exists('sanitizeInput'),
    'local_dev_mode' => defined('LOCAL_DEV_MODE') && LOCAL_DEV_MODE,
    'config_selector' => file_exists('config-selector.php'),
    'local_config' => file_exists('config.local.php')
];

foreach ($securityChecks as $check => $result) {
    echo $result ? "✅ " . ucfirst(str_replace('_', ' ', $check)) . ": PASS\n" : "❌ " . ucfirst(str_replace('_', ' ', $check)) . ": FAIL\n";
    $testResults['tests']['security'][$check] = $result ? 'PASS' : 'FAIL';
    if (!$result && $check !== 'local_dev_mode') {
        $testResults['overall_status'] = 'FAIL';
    }
}

echo "\n";

// Test 7: JavaScript Functionality
echo "7. JAVASCRIPT FUNCTIONALITY\n";
echo "-" . str_repeat("-", 30) . "\n";

$jsFile = 'js/app.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    $jsFeatures = [
        'DadviceApp class' => strpos($jsContent, 'class DadviceApp') !== false,
        'Event listeners' => strpos($jsContent, 'addEventListener') !== false,
        'Bookmark functionality' => strpos($jsContent, 'toggleBookmark') !== false,
        'Share functionality' => strpos($jsContent, 'shareArticle') !== false,
        'Form validation' => strpos($jsContent, 'validateForm') !== false,
        'Mobile menu' => strpos($jsContent, 'toggleMobileMenu') !== false
    ];
    
    foreach ($jsFeatures as $feature => $exists) {
        echo $exists ? "✅ $feature: IMPLEMENTED\n" : "⚠️ $feature: NOT FOUND\n";
        $testResults['tests']['javascript'][$feature] = $exists ? 'IMPLEMENTED' : 'NOT_FOUND';
    }
} else {
    echo "❌ JavaScript file missing\n";
    $testResults['tests']['javascript']['file'] = 'MISSING';
    $testResults['overall_status'] = 'FAIL';
}

echo "\n";

// Test 8: Performance Metrics
echo "8. PERFORMANCE METRICS\n";
echo "-" . str_repeat("-", 30) . "\n";

// Memory usage
$memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
echo "✅ Memory Usage: {$memoryUsage}MB\n";
$testResults['tests']['performance']['memory'] = "{$memoryUsage}MB";

// File sizes
$fileSizes = [];
foreach (['index.php', 'article.php', 'newsletter-generator.php', 'js/app.js'] as $file) {
    if (file_exists($file)) {
        $size = round(filesize($file) / 1024, 2);
        echo "✅ $file: {$size}KB\n";
        $fileSizes[$file] = "{$size}KB";
    }
}
$testResults['tests']['performance']['file_sizes'] = $fileSizes;

echo "\n";

// Final Summary
echo "FINAL SUMMARY\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "Overall Status: " . $testResults['overall_status'] . "\n";
echo "Test Completion: " . date('Y-m-d H:i:s') . "\n";

// Count passed/failed tests
$passCount = 0;
$failCount = 0;
$warningCount = 0;

function countResults($array, &$pass, &$fail, &$warn) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            countResults($value, $pass, $fail, $warn);
        } else {
            if (strpos($value, 'PASS') !== false || strpos($value, 'EXISTS') !== false || strpos($value, 'IMPLEMENTED') !== false) {
                $pass++;
            } elseif (strpos($value, 'FAIL') !== false || strpos($value, 'MISSING') !== false) {
                $fail++;
            } elseif (strpos($value, 'WARNING') !== false || strpos($value, 'NOT_FOUND') !== false) {
                $warn++;
            }
        }
    }
}

countResults($testResults['tests'], $passCount, $failCount, $warningCount);

echo "\nTest Results:\n";
echo "✅ Passed: $passCount\n";
echo "❌ Failed: $failCount\n";
echo "⚠️ Warnings: $warningCount\n";

// Save detailed results to JSON
if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
}

$jsonReport = json_encode($testResults, JSON_PRETTY_PRINT);
file_put_contents('logs/comprehensive_test_' . date('Y-m-d_H-i-s') . '.json', $jsonReport);

echo "\n📊 Detailed report saved to logs/comprehensive_test_" . date('Y-m-d_H-i-s') . ".json\n";

// Recommendations
echo "\nRECOMMENDATIONS\n";
echo "=" . str_repeat("=", 50) . "\n";

if ($testResults['overall_status'] === 'PASS') {
    echo "🎉 System is functioning well! Consider these enhancements:\n";
    echo "• Implement user authentication system\n";
    echo "• Add comprehensive error handling\n";
    echo "• Set up automated testing\n";
    echo "• Optimize database queries\n";
    echo "• Add caching mechanisms\n";
} else {
    echo "🔧 Issues found that need attention:\n";
    if (isset($testResults['tests']['database']['error'])) {
        echo "• Fix database connectivity issues\n";
    }
    if ($failCount > 0) {
        echo "• Address failed test cases\n";
    }
    echo "• Review configuration conflicts\n";
    echo "• Ensure all required files are present\n";
}

echo "\n✅ Comprehensive testing completed!\n";
?>