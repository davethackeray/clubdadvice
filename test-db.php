<?php
echo "Starting database test...\n";

try {
    require_once 'config.local.php';
    echo "Config loaded\n";
    
    $pdo = getDBConnection();
    echo "Database connected\n";
    
    // Test simple query
    $result = $pdo->query("SELECT 1 as test");
    $row = $result->fetch();
    echo "Test query result: " . $row['test'] . "\n";
    
    echo "All tests passed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>