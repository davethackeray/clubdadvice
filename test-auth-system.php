<?php
/**
 * Authentication System Test
 * This file tests the user authentication functionality
 */

require_once 'config.php';

echo "<h1>Club Dadvice Authentication System Test</h1>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $pdo = getDBConnection();
    echo "✓ Database connection successful<br>";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Users table exists<br>";
        
        // Show users table structure
        $stmt = $pdo->query("DESCRIBE users");
        echo "<strong>Users table structure:</strong><br>";
        echo "<ul>";
        while ($row = $stmt->fetch()) {
            echo "<li>{$row['Field']} - {$row['Type']}</li>";
        }
        echo "</ul>";
    } else {
        echo "✗ Users table does not exist. Run the migration first.<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 2: User Class Loading
echo "<h2>2. User Class Test</h2>";
try {
    require_once 'classes/User.php';
    $user = new User();
    echo "✓ User class loaded successfully<br>";
    
    // Test login check
    $isLoggedIn = $user->isLoggedIn();
    echo "Current login status: " . ($isLoggedIn ? "Logged in" : "Not logged in") . "<br>";
    
    if ($isLoggedIn) {
        $currentUser = $user->getCurrentUser();
        echo "Current user: " . htmlspecialchars($currentUser['display_name']) . "<br>";
    }
    
} catch (Exception $e) {
    echo "✗ User class error: " . $e->getMessage() . "<br>";
}

// Test 3: Session Functionality
echo "<h2>3. Session Test</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "<br>";

// Test 4: File Structure
echo "<h2>4. File Structure Test</h2>";
$requiredFiles = [
    'register.php',
    'login.php',
    'logout.php',
    'profile.php',
    'forgot-password.php',
    'reset-password.php',
    'verify-email.php',
    'bookmarks.php',
    'classes/User.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists<br>";
    } else {
        echo "✗ $file missing<br>";
    }
}

// Test 5: Configuration
echo "<h2>5. Configuration Test</h2>";
echo "Site Name: " . SITE_NAME . "<br>";
echo "Base URL: " . BASE_URL . "<br>";
echo "Database Host: " . DB_HOST . "<br>";
echo "Database Name: " . DB_NAME . "<br>";

echo "<h2>Test Complete</h2>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>