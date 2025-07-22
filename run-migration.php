<?php
/**
 * Database Migration Runner
 * Safely executes database migrations for the Dadvice platform
 */

require_once 'config.php';

class MigrationRunner {
    private $connection;
    private $migrations_dir = 'migrations';
    private $use_pdo = false;
    
    public function __construct() {
        // Try PDO first, fall back to mysqli
        try {
            if (in_array('mysql', PDO::getAvailableDrivers())) {
                $this->connection = getDBConnection();
                $this->use_pdo = true;
                echo "✓ Database connection established (PDO)\n";
            } else {
                throw new Exception("PDO MySQL driver not available");
            }
        } catch (Exception $e) {
            // Fall back to mysqli
            try {
                $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ($this->connection->connect_error) {
                    throw new Exception("Connection failed: " . $this->connection->connect_error);
                }
                $this->connection->set_charset("utf8mb4");
                $this->use_pdo = false;
                echo "✓ Database connection established (MySQLi)\n";
            } catch (Exception $e2) {
                die("✗ Database connection failed: " . $e2->getMessage() . "\n");
            }
        }
    }
    
    /**
     * Run a specific migration file
     */
    public function runMigration($migration_file) {
        $migration_path = $this->migrations_dir . '/' . $migration_file;
        
        if (!file_exists($migration_path)) {
            throw new Exception("Migration file not found: $migration_path");
        }
        
        echo "Running migration: $migration_file\n";
        echo str_repeat("-", 50) . "\n";
        
        // Read the migration SQL
        $sql = file_get_contents($migration_path);
        
        if (empty($sql)) {
            throw new Exception("Migration file is empty: $migration_path");
        }
        
        try {
            // Begin transaction for safety
            if ($this->use_pdo) {
                $this->connection->beginTransaction();
            } else {
                $this->connection->autocommit(false);
            }
            
            // Split SQL into individual statements and execute
            $statements = $this->splitSqlStatements($sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || $this->isComment($statement)) {
                    continue;
                }
                
                echo "Executing: " . substr($statement, 0, 100) . "...\n";
                
                if ($this->use_pdo) {
                    $this->connection->exec($statement);
                } else {
                    if (!$this->connection->query($statement)) {
                        throw new Exception("Query failed: " . $this->connection->error);
                    }
                }
            }
            
            // Commit transaction
            if ($this->use_pdo) {
                $this->connection->commit();
            } else {
                $this->connection->commit();
                $this->connection->autocommit(true);
            }
            echo "✓ Migration completed successfully!\n\n";
            
        } catch (Exception $e) {
            // Rollback on error
            if ($this->use_pdo) {
                $this->connection->rollback();
            } else {
                $this->connection->rollback();
                $this->connection->autocommit(true);
            }
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }
    
    /**
     * Split SQL file into individual statements
     */
    private function splitSqlStatements($sql) {
        // Remove comments and normalize line endings
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        
        // Split by semicolon, but handle DELIMITER statements
        $statements = [];
        $current_statement = '';
        $delimiter = ';';
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Handle DELIMITER statements
            if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
                $delimiter = trim($matches[1]);
                continue;
            }
            
            $current_statement .= $line . "\n";
            
            // Check if statement ends with current delimiter
            if (substr(rtrim($line), -strlen($delimiter)) === $delimiter) {
                $statement = trim(substr($current_statement, 0, -strlen($delimiter)));
                if (!empty($statement)) {
                    $statements[] = $statement;
                }
                $current_statement = '';
            }
        }
        
        // Add any remaining statement
        if (!empty(trim($current_statement))) {
            $statements[] = trim($current_statement);
        }
        
        return $statements;
    }
    
    /**
     * Check if a line is a comment
     */
    private function isComment($line) {
        $line = trim($line);
        return empty($line) || 
               substr($line, 0, 2) === '--' || 
               substr($line, 0, 2) === '/*' ||
               substr($line, 0, 1) === '#';
    }
    
    /**
     * Verify migration was successful
     */
    public function verifyMigration() {
        echo "Verifying migration...\n";
        echo str_repeat("-", 30) . "\n";
        
        try {
            // Check if users table exists
            $users_columns = $this->getTableColumns('users');
            echo "✓ Users table created with columns: " . implode(', ', $users_columns) . "\n";
            
            // Check if user_bookmarks table exists
            $bookmarks_columns = $this->getTableColumns('user_bookmarks');
            echo "✓ User_bookmarks table created with columns: " . implode(', ', $bookmarks_columns) . "\n";
            
            // Check if articles table has new columns
            $articles_columns = $this->getTableColumns('articles');
            
            if (in_array('view_count', $articles_columns) && in_array('bookmark_count', $articles_columns)) {
                echo "✓ Articles table extended with view_count and bookmark_count columns\n";
            } else {
                echo "✗ Articles table missing new columns\n";
            }
            
            // Check if migration was logged
            $log_entry = $this->queryRow("SELECT * FROM migration_log WHERE migration_name = '001_user_management_schema'");
            
            if ($log_entry) {
                echo "✓ Migration logged successfully at " . $log_entry['executed_at'] . "\n";
            } else {
                echo "✗ Migration not found in log\n";
            }
            
            echo "\n✓ Migration verification completed!\n";
            
        } catch (Exception $e) {
            echo "✗ Verification failed: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Get table columns (works with both PDO and MySQLi)
     */
    private function getTableColumns($table_name) {
        $columns = [];
        
        if ($this->use_pdo) {
            $stmt = $this->connection->query("DESCRIBE $table_name");
            while ($row = $stmt->fetch()) {
                $columns[] = $row['Field'];
            }
        } else {
            $result = $this->connection->query("DESCRIBE $table_name");
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
        }
        
        return $columns;
    }
    
    /**
     * Execute a query and return a single row (works with both PDO and MySQLi)
     */
    private function queryRow($sql) {
        if ($this->use_pdo) {
            $stmt = $this->connection->query($sql);
            return $stmt->fetch();
        } else {
            $result = $this->connection->query($sql);
            return $result ? $result->fetch_assoc() : null;
        }
    }
    
    /**
     * Show current database structure
     */
    public function showDatabaseStructure() {
        echo "Current Database Structure:\n";
        echo str_repeat("=", 40) . "\n";
        
        try {
            $tables = [];
            
            if ($this->use_pdo) {
                $stmt = $this->connection->query("SHOW TABLES");
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                    $tables[] = $row[0];
                }
            } else {
                $result = $this->connection->query("SHOW TABLES");
                while ($row = $result->fetch_array()) {
                    $tables[] = $row[0];
                }
            }
            
            foreach ($tables as $table) {
                echo "\nTable: $table\n";
                echo str_repeat("-", 20) . "\n";
                
                if ($this->use_pdo) {
                    $stmt = $this->connection->query("DESCRIBE $table");
                    while ($column = $stmt->fetch()) {
                        echo sprintf("  %-20s %s\n", $column['Field'], $column['Type']);
                    }
                } else {
                    $result = $this->connection->query("DESCRIBE $table");
                    while ($column = $result->fetch_assoc()) {
                        echo sprintf("  %-20s %s\n", $column['Field'], $column['Type']);
                    }
                }
            }
            
        } catch (Exception $e) {
            echo "Error showing database structure: " . $e->getMessage() . "\n";
        }
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    echo "Dadvice Database Migration Runner\n";
    echo str_repeat("=", 50) . "\n\n";
    
    try {
        $runner = new MigrationRunner();
        
        // Run the user management migration
        $runner->runMigration('001_user_management_schema.sql');
        
        // Verify the migration
        $runner->verifyMigration();
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Migration completed successfully!\n";
        echo "You can now use user authentication and bookmarking features.\n";
        
    } catch (Exception $e) {
        echo "✗ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "This script must be run from the command line.\n";
    echo "Usage: php run-migration.php\n";
}
?>