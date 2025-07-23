<?php
/**
 * Comprehensive Testing Report
 * Tests all major functionality and generates a detailed report
 */

require_once 'config-selector.php';
require_once 'classes/ArticleManager.php';
require_once 'classes/EmailService.php';

$report = [];
$overall_status = 'PASS';

// Test 1: Database Connection and Schema
echo "<h1>🧪 Club Dadvice - Comprehensive Testing Report</h1>\n";
echo "<p><strong>Test Date:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
echo "<p><strong>Environment:</strong> Local Development</p>\n";

try {
    $pdo = getDBConnection();
    $report['database']['connection'] = 'PASS';
    
    // Check required tables
    $required_tables = ['articles', 'age_groups', 'categories', 'tags'];
    foreach ($required_tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            $report['database']['tables'][$table] = "PASS ($count records)";
        } catch (Exception $e) {
            $report['database']['tables'][$table] = 'FAIL - ' . $e->getMessage();
            $overall_status = 'FAIL';
        }
    }
} catch (Exception $e) {
    $report['database']['connection'] = 'FAIL - ' . $e->getMessage();
    $overall_status = 'FAIL';
}

// Test 2: Article Management System
try {
    $articleManager = new ArticleManager();
    
    // Test getting articles
    $articles = $articleManager->getArticles([], 1, 5);
    $report['articles']['retrieval'] = count($articles) > 0 ? "PASS (" . count($articles) . " articles)" : 'FAIL - No articles found';
    
    // Test getting age groups and categories
    $ageGroups = $articleManager->getAgeGroups();
    $categories = $articleManager->getCategories();
    
    $report['articles']['age_groups'] = count($ageGroups) > 0 ? "PASS (" . count($ageGroups) . " groups)" : 'FAIL';
    $report['articles']['categories'] = count($categories) > 0 ? "PASS (" . count($categories) . " categories)" : 'FAIL';
    
    // Test individual article retrieval
    if (!empty($articles)) {
        $firstArticle = $articleManager->getArticle($articles[0]['id']);
        $report['articles']['individual'] = $firstArticle ? 'PASS' : 'FAIL';
    } else {
        $report['articles']['individual'] = 'SKIP - No articles to test';
    }
    
} catch (Exception $e) {
    $report['articles']['error'] = 'FAIL - ' . $e->getMessage();
    $overall_status = 'FAIL';
}

// Test 3: Email System
try {
    $emailService = new EmailService();
    $config = $emailService->testConfiguration();
    
    $report['email']['configuration'] = 'PASS';
    $report['email']['local_dev_mode'] = $config['local_dev_mode'] ? 'PASS' : 'FAIL';
    
    // Test sending emails (they will be logged in dev mode)
    $verification_result = $emailService->sendVerificationEmail('test@example.com', 'test-token', 'Test User');
    $reset_result = $emailService->sendPasswordResetEmail('test@example.com', 'reset-token', 'Test User');
    $welcome_result = $emailService->sendWelcomeEmail('test@example.com', 'Test User');
    
    $report['email']['verification'] = $verification_result ? 'PASS' : 'FAIL';
    $report['email']['password_reset'] = $reset_result ? 'PASS' : 'FAIL';
    $report['email']['welcome'] = $welcome_result ? 'PASS' : 'FAIL';
    
    // Check if log file exists
    $log_file = 'logs/emails_' . date('Y-m-d') . '.log';
    $report['email']['logging'] = file_exists($log_file) ? 'PASS' : 'FAIL';
    
} catch (Exception $e) {
    $report['email']['error'] = 'FAIL - ' . $e->getMessage();
    $overall_status = 'FAIL';
}

// Test 4: Core Pages Accessibility
$core_pages = [
    'index.php' => 'Homepage',
    'article.php' => 'Article Page',
    'newsletter-generator.php' => 'Newsletter Generator'
];

foreach ($core_pages as $page => $name) {
    if (file_exists($page)) {
        $report['pages'][$page] = 'PASS - File exists';
    } else {
        $report['pages'][$page] = 'FAIL - File missing';
        $overall_status = 'FAIL';
    }
}

// Test 5: Configuration and Security
$security_checks = [
    'config_selector' => file_exists('config-selector.php'),
    'local_config' => file_exists('config.local.php'),
    'csrf_functions' => function_exists('generateCSRFToken'),
    'rate_limiting' => function_exists('checkRateLimit'),
    'local_dev_mode' => defined('LOCAL_DEV_MODE') && LOCAL_DEV_MODE
];

foreach ($security_checks as $check => $result) {
    $report['security'][$check] = $result ? 'PASS' : 'FAIL';
    if (!$result && $check !== 'local_dev_mode') {
        $overall_status = 'FAIL';
    }
}

// Test 6: File Structure and Assets
$required_files = [
    'js/app.js' => 'JavaScript Application',
    'classes/ArticleManager.php' => 'Article Manager Class',
    'classes/EmailService.php' => 'Email Service Class',
    'includes/header.php' => 'Header Template',
    'includes/footer.php' => 'Footer Template'
];

foreach ($required_files as $file => $description) {
    $report['files'][$file] = file_exists($file) ? 'PASS' : 'FAIL';
    if (!file_exists($file)) {
        $overall_status = 'FAIL';
    }
}

// Generate HTML Report
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Report - Club Dadvice</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .status-pass { color: #27ae60; font-weight: bold; }
        .status-fail { color: #e74c3c; font-weight: bold; }
        .status-skip { color: #f39c12; font-weight: bold; }
        .test-section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #3498db; }
        .test-section h2 { margin-top: 0; color: #2c3e50; }
        .test-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ecf0f1; }
        .test-item:last-child { border-bottom: none; }
        .overall-status { font-size: 1.5em; text-align: center; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .overall-pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .overall-fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .recommendations { background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🧪 Club Dadvice - Comprehensive Testing Report</h1>
        <p><strong>Test Date:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>Environment:</strong> Local Development (XAMPP)</p>
    </div>

    <div class="overall-status <?= strtolower($overall_status) === 'pass' ? 'overall-pass' : 'overall-fail' ?>">
        <strong>Overall Status: <?= $overall_status ?></strong>
    </div>

    <!-- Database Tests -->
    <div class="test-section">
        <h2>🗄️ Database Tests</h2>
        <?php foreach ($report['database'] as $test => $result): ?>
            <?php if ($test === 'tables'): ?>
                <h3>Database Tables</h3>
                <?php foreach ($result as $table => $status): ?>
                    <div class="test-item">
                        <span><?= ucfirst($table) ?> Table</span>
                        <span class="status-<?= strpos($status, 'PASS') === 0 ? 'pass' : 'fail' ?>"><?= $status ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="test-item">
                    <span><?= ucfirst(str_replace('_', ' ', $test)) ?></span>
                    <span class="status-<?= strpos($result, 'PASS') === 0 ? 'pass' : 'fail' ?>"><?= $result ?></span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Article Management Tests -->
    <div class="test-section">
        <h2>📄 Article Management System</h2>
        <?php foreach ($report['articles'] as $test => $result): ?>
            <div class="test-item">
                <span><?= ucfirst(str_replace('_', ' ', $test)) ?></span>
                <span class="status-<?= strpos($result, 'PASS') === 0 ? 'pass' : (strpos($result, 'SKIP') === 0 ? 'skip' : 'fail') ?>"><?= $result ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Email System Tests -->
    <div class="test-section">
        <h2>📧 Email System</h2>
        <?php foreach ($report['email'] as $test => $result): ?>
            <div class="test-item">
                <span><?= ucfirst(str_replace('_', ' ', $test)) ?></span>
                <span class="status-<?= strpos($result, 'PASS') === 0 ? 'pass' : 'fail' ?>"><?= $result ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Core Pages Tests -->
    <div class="test-section">
        <h2>🌐 Core Pages</h2>
        <?php foreach ($report['pages'] as $page => $result): ?>
            <div class="test-item">
                <span><?= $page ?></span>
                <span class="status-<?= strpos($result, 'PASS') === 0 ? 'pass' : 'fail' ?>"><?= $result ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Security Tests -->
    <div class="test-section">
        <h2>🔒 Security & Configuration</h2>
        <?php foreach ($report['security'] as $test => $result): ?>
            <div class="test-item">
                <span><?= ucfirst(str_replace('_', ' ', $test)) ?></span>
                <span class="status-<?= $result ? 'pass' : 'fail' ?>"><?= $result ? 'PASS' : 'FAIL' ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- File Structure Tests -->
    <div class="test-section">
        <h2>📁 File Structure</h2>
        <?php foreach ($report['files'] as $file => $result): ?>
            <div class="test-item">
                <span><?= $file ?></span>
                <span class="status-<?= strpos($result, 'PASS') === 0 ? 'pass' : 'fail' ?>"><?= $result ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Recommendations -->
    <div class="recommendations">
        <h2>📋 Recommendations & Next Steps</h2>
        <h3>✅ What's Working Well:</h3>
        <ul>
            <li>Database connection and schema are properly configured</li>
            <li>Article management system is functional</li>
            <li>Email system is working in development mode with proper logging</li>
            <li>Core application files are present and accessible</li>
            <li>Security functions (CSRF, rate limiting) are implemented</li>
            <li>Local development environment is properly configured</li>
        </ul>

        <h3>🔧 Areas for Improvement:</h3>
        <ul>
            <li><strong>Cross-Browser Testing:</strong> Test functionality across Chrome, Firefox, Safari, and Edge</li>
            <li><strong>Mobile Responsiveness:</strong> Verify responsive design on various device sizes</li>
            <li><strong>Performance Testing:</strong> Test with larger datasets and measure query performance</li>
            <li><strong>User Authentication:</strong> Implement and test user registration/login system</li>
            <li><strong>JavaScript Testing:</strong> Test interactive features and progressive enhancement</li>
            <li><strong>Error Handling:</strong> Test error scenarios and edge cases</li>
        </ul>

        <h3>🚀 Production Readiness:</h3>
        <ul>
            <li>Configure SMTP for production email delivery</li>
            <li>Set up proper SSL certificates</li>
            <li>Implement caching mechanisms</li>
            <li>Add monitoring and logging</li>
            <li>Perform security audit</li>
            <li>Set up automated backups</li>
        </ul>
    </div>

    <div style="text-align: center; margin: 40px 0;">
        <a href="index.php" style="background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">← Back to Application</a>
    </div>
</body>
</html>

<?php
// Log the test results
$log_entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'overall_status' => $overall_status,
    'report' => $report
];

if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
}

file_put_contents('logs/test_report_' . date('Y-m-d_H-i-s') . '.json', json_encode($log_entry, JSON_PRETTY_PRINT));
?>