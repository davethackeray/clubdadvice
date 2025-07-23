<?php
require_once 'config-selector.php';

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!\n";
    
    // Read and execute the schema
    $schema = file_get_contents('database_schema.sql');
    
    // Remove the CREATE DATABASE and USE statements for local setup
    $schema = preg_replace('/CREATE DATABASE.*?;/', '', $schema);
    $schema = preg_replace('/USE.*?;/', '', $schema);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "✅ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (Exception $e) {
                echo "⚠️ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Database schema setup completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>