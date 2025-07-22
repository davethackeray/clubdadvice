<?php
/**
 * Local Development Setup Script for XAMPP
 * This script sets up the database and initial data for local testing
 */

// Use local configuration
require_once 'config.local.php';

echo "<h1>Club Dadvice - Local Development Setup</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
    .success { color: green; background: #f0f8f0; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: red; background: #fff0f0; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #f0f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { background: #f8f9fa; padding: 15px; margin: 15px 0; border-left: 4px solid #007cba; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

// Step 1: Test database connection
echo "<div class='step'>";
echo "<h2>Step 1: Testing Database Connection</h2>";

try {
    // First, try to connect without specifying database to create it if needed
    $pdo_root = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo_root->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ MySQL connection successful!</div>";
    
    // Create database if it doesn't exist
    $pdo_root->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<div class='success'>✅ Database '" . DB_NAME . "' created/verified</div>";
    
    // Now connect to the specific database
    $pdo = getDBConnection();
    echo "<div class='success'>✅ Connected to database: " . DB_NAME . "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
    echo "<div class='info'>
        <strong>XAMPP Setup Instructions:</strong><br>
        1. Make sure XAMPP is running<br>
        2. Start Apache and MySQL services<br>
        3. Open phpMyAdmin (http://localhost/phpmyadmin)<br>
        4. Default credentials: username='root', password='' (empty)
    </div>";
    exit;
}
echo "</div>";

// Step 2: Run database migrations
echo "<div class='step'>";
echo "<h2>Step 2: Setting Up Database Schema</h2>";

try {
    // Read and execute the migration file
    $migration_file = 'migrations/001_user_management_schema.sql';
    
    if (!file_exists($migration_file)) {
        throw new Exception("Migration file not found: " . $migration_file);
    }
    
    $sql_content = file_get_contents($migration_file);
    
    // Split SQL statements and execute them
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Some statements might fail if already executed, that's OK
                if (!strpos($e->getMessage(), 'already exists') && !strpos($e->getMessage(), 'Duplicate')) {
                    echo "<div class='error'>Warning: " . $e->getMessage() . "</div>";
                }
            }
        }
    }
    
    echo "<div class='success'>✅ Database schema migration completed</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Migration failed: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Step 3: Verify tables
echo "<div class='step'>";
echo "<h2>Step 3: Verifying Database Tables</h2>";

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='success'>✅ Found " . count($tables) . " tables:</div>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
    // Check specific required tables
    $required_tables = ['users', 'articles', 'bookmarks'];
    $missing_tables = [];
    
    foreach ($required_tables as $required_table) {
        if (!in_array($required_table, $tables)) {
            $missing_tables[] = $required_table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "<div class='success'>✅ All required tables present</div>";
    } else {
        echo "<div class='error'>❌ Missing tables: " . implode(', ', $missing_tables) . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Table verification failed: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Step 4: Test User class
echo "<div class='step'>";
echo "<h2>Step 4: Testing User Authentication System</h2>";

try {
    require_once 'classes/User.php';
    $user = new User();
    
    echo "<div class='success'>✅ User class loaded successfully</div>";
    
    // Test email validation
    $test_email = "test@example.com";
    $reflection = new ReflectionClass($user);
    $method = $reflection->getMethod('isValidEmail');
    $method->setAccessible(true);
    
    if ($method->invoke($user, $test_email)) {
        echo "<div class='success'>✅ Email validation working</div>";
    } else {
        echo "<div class='error'>❌ Email validation failed</div>";
    }
    
    // Test password validation
    $test_password = "TestPass123";
    $method = $reflection->getMethod('isValidPassword');
    $method->setAccessible(true);
    
    if ($method->invoke($user, $test_password)) {
        echo "<div class='success'>✅ Password validation working</div>";
    } else {
        echo "<div class='error'>❌ Password validation failed</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ User class test failed: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Step 5: Test Email Service
echo "<div class='step'>";
echo "<h2>Step 5: Testing Email Service</h2>";

try {
    require_once 'classes/EmailService.php';
    $emailService = new EmailService();
    
    echo "<div class='success'>✅ EmailService class loaded successfully</div>";
    
    // Test configuration
    $config = $emailService->testConfiguration();
    
    echo "<div class='info'>";
    echo "<strong>Email Configuration:</strong><br>";
    echo "SMTP Configured: " . ($config['smtp_configured'] ? 'Yes' : 'No') . "<br>";
    echo "SMTP Host: " . htmlspecialchars($config['smtp_host']) . "<br>";
    echo "From Email: " . htmlspecialchars($config['from_email']) . "<br>";
    echo "From Name: " . htmlspecialchars($config['from_name']) . "<br>";
    echo "</div>";
    
    echo "<div class='success'>✅ Email service configuration loaded</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Email service test failed: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Step 6: Create directories
echo "<div class='step'>";
echo "<h2>Step 6: Creating Required Directories</h2>";

$directories = ['cache', 'logs', 'sessions', 'templates/email'];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div class='success'>✅ Created directory: " . $dir . "</div>";
        } else {
            echo "<div class='error'>❌ Failed to create directory: " . $dir . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ Directory already exists: " . $dir . "</div>";
    }
}
echo "</div>";

// Step 7: Final summary
echo "<div class='step'>";
echo "<h2>🎉 Setup Complete!</h2>";
echo "<div class='success'>";
echo "<strong>Your local development environment is ready!</strong><br><br>";
echo "<strong>Next Steps:</strong><br>";
echo "1. Visit: <a href='register.php'>register.php</a> to test user registration<br>";
echo "2. Visit: <a href='login.php'>login.php</a> to test user login<br>";
echo "3. Visit: <a href='test-auth-system.php'>test-auth-system.php</a> to run comprehensive tests<br>";
echo "4. Visit: <a href='index.php'>index.php</a> to see the main site<br><br>";
echo "<strong>Database Info:</strong><br>";
echo "Host: " . DB_HOST . "<br>";
echo "Database: " . DB_NAME . "<br>";
echo "User: " . DB_USER . "<br>";
echo "</div>";
echo "</div>";

echo "<hr>";
echo "<p><em>Setup completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>