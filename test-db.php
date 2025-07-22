<?php
echo "Available PDO drivers:\n";
print_r(PDO::getAvailableDrivers());

echo "\nTesting database connection...\n";

require_once 'config.php';

try {
    $pdo = getDBConnection();
    echo "✓ Database connection successful!\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
    $result = $stmt->fetch();
    echo "✓ Found " . $result['count'] . " articles in database\n";
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}
?>