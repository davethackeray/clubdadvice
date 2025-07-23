<?php
require_once 'config-selector.php';

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!\n\n";
    
    // Show all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Existing tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll();
        foreach ($columns as $column) {
            echo "  • {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>