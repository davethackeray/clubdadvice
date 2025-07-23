<?php
echo "<h1>Web Access Test</h1>";
echo "<p>✅ PHP is working!</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Server info: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] ?? 'Unknown' . "</p>";

echo "<h2>Available Test Pages:</h2>";
echo "<ul>";
echo "<li><a href='register.php'>User Registration</a></li>";
echo "<li><a href='login.php'>User Login</a></li>";
echo "<li><a href='test-auth-system.php'>Authentication System Test</a></li>";
echo "<li><a href='test-email-system.php'>Email System Test</a></li>";
echo "<li><a href='index.php'>Main Site</a></li>";
echo "</ul>";

echo "<h2>Database Status:</h2>";
try {
    require_once 'config.local.php';
    $pdo = getDBConnection();
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✅ Database connected with " . count($tables) . " tables</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>