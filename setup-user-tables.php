<?php
/**
 * Simple User Tables Setup Script
 * Creates the necessary user management tables for authentication
 */

require_once 'config.php';

try {
    $pdo = getDBConnection();
    echo "✓ Database connection established\n";
    
    // Read and execute the migration
    $migration_sql = file_get_contents('migrations/001_user_management_schema.sql');
    
    if (empty($migration_sql)) {
        throw new Exception("Migration file is empty or not found");
    }
    
    echo "Running user management schema migration...\n";
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Execute the migration (PDO can handle multiple statements)
        $pdo->exec($migration_sql);
        
        // Commit transaction
        $pdo->commit();
        echo "✓ Migration completed successfully!\n";
        
        // Verify tables were created
        echo "\nVerifying tables...\n";
        
        $tables = ['users', 'user_bookmarks', 'user_interactions'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✓ Table '$table' created successfully\n";
            } else {
                echo "✗ Table '$table' not found\n";
            }
        }
        
        // Check if articles table has new columns
        $stmt = $pdo->query("DESCRIBE articles");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('view_count', $columns) && in_array('bookmark_count', $columns)) {
            echo "✓ Articles table extended with engagement columns\n";
        } else {
            echo "✗ Articles table missing engagement columns\n";
        }
        
        echo "\n✓ User management schema setup completed!\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "✗ Setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>