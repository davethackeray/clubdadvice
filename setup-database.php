<?php
/**
 * Database Setup and Migration Tool
 * Web-based interface for running database migrations
 */

require_once 'config.php';

// Security check - only allow in development or with proper authentication
$allowed_ips = ['127.0.0.1', '::1', 'localhost'];
$is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed_ips) || 
            in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']);

if (!$is_local && !isset($_GET['auth']) || $_GET['auth'] !== 'dadvice2024') {
    die('Access denied. This tool is only available locally or with proper authentication.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dadvice Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        button.danger { background: #dc3545; }
        button.danger:hover { background: #c82333; }
        .sql-output { max-height: 400px; overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🛠️ Dadvice Database Setup & Migration Tool</h1>
    
    <?php
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $message = '';
    $message_type = '';
    
    // Test database connection first
    try {
        $pdo = getDBConnection();
        $message = "✅ Database connection successful!";
        $message_type = "success";
    } catch (Exception $e) {
        $message = "❌ Database connection failed: " . $e->getMessage();
        $message_type = "error";
        $pdo = null;
    }
    
    if ($message) {
        echo "<div class='container $message_type'>$message</div>";
    }
    
    if ($pdo && $action) {
        switch ($action) {
            case 'run_migration':
                runUserManagementMigration($pdo);
                break;
            case 'show_structure':
                showDatabaseStructure($pdo);
                break;
            case 'verify_migration':
                verifyMigration($pdo);
                break;
            case 'reset_migration':
                resetMigration($pdo);
                break;
        }
    }
    
    if ($pdo) {
    ?>
    
    <div class="container">
        <h2>🚀 Migration Actions</h2>
        <p>Choose an action to perform on your database:</p>
        
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="run_migration">
            <button type="submit">Run User Management Migration</button>
        </form>
        
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="verify_migration">
            <button type="submit">Verify Migration</button>
        </form>
        
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="show_structure">
            <button type="submit">Show Database Structure</button>
        </form>
        
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="reset_migration">
            <button type="submit" class="danger" onclick="return confirm('Are you sure? This will remove user management tables!')">Reset Migration</button>
        </form>
    </div>
    
    <div class="container info">
        <h3>📋 Migration Details</h3>
        <p>This migration will:</p>
        <ul>
            <li>Add <code>view_count</code> and <code>bookmark_count</code> columns to the existing <code>articles</code> table</li>
            <li>Create a new <code>users</code> table for user authentication and profiles</li>
            <li>Create a <code>user_bookmarks</code> table for saving favorite articles</li>
            <li>Create a <code>user_interactions</code> table for tracking user engagement</li>
            <li>Add database triggers to automatically update article engagement counts</li>
            <li>Create proper indexes for optimal performance</li>
        </ul>
    </div>
    
    <?php
    }
    
    function runUserManagementMigration($pdo) {
        echo "<div class='container'>";
        echo "<h3>🔄 Running User Management Migration...</h3>";
        
        try {
            $pdo->beginTransaction();
            
            // Step 1: Add columns to articles table
            echo "<h4>Step 1: Extending articles table</h4>";
            
            // Check if columns already exist
            $stmt = $pdo->query("DESCRIBE articles");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('view_count', $columns)) {
                $pdo->exec("ALTER TABLE articles ADD COLUMN view_count INT DEFAULT 0");
                echo "✅ Added view_count column to articles table<br>";
            } else {
                echo "ℹ️ view_count column already exists<br>";
            }
            
            if (!in_array('bookmark_count', $columns)) {
                $pdo->exec("ALTER TABLE articles ADD COLUMN bookmark_count INT DEFAULT 0");
                echo "✅ Added bookmark_count column to articles table<br>";
            } else {
                echo "ℹ️ bookmark_count column already exists<br>";
            }
            
            // Step 2: Create users table
            echo "<h4>Step 2: Creating users table</h4>";
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    first_name VARCHAR(100),
                    last_name VARCHAR(100),
                    display_name VARCHAR(150),
                    location VARCHAR(100),
                    timezone VARCHAR(50) DEFAULT 'Europe/London',
                    newsletter_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'weekly',
                    email_verified BOOLEAN DEFAULT FALSE,
                    email_verification_token VARCHAR(255),
                    password_reset_token VARCHAR(255),
                    password_reset_expires TIMESTAMP NULL,
                    last_login TIMESTAMP NULL,
                    login_count INT DEFAULT 0,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    
                    INDEX idx_users_email (email),
                    INDEX idx_users_created_at (created_at),
                    INDEX idx_users_last_login (last_login),
                    INDEX idx_users_active (is_active),
                    INDEX idx_users_email_verified (email_verified)
                )
            ");
            echo "✅ Created users table<br>";
            
            // Step 3: Create user_bookmarks table
            echo "<h4>Step 3: Creating user_bookmarks table</h4>";
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS user_bookmarks (
                    user_id INT NOT NULL,
                    article_id VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    notes TEXT,
                    is_favorite BOOLEAN DEFAULT FALSE,
                    
                    PRIMARY KEY (user_id, article_id),
                    
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
                    
                    INDEX idx_bookmarks_user_created (user_id, created_at),
                    INDEX idx_bookmarks_article (article_id),
                    INDEX idx_bookmarks_favorites (user_id, is_favorite)
                )
            ");
            echo "✅ Created user_bookmarks table<br>";
            
            // Step 4: Create user_interactions table
            echo "<h4>Step 4: Creating user_interactions table</h4>";
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS user_interactions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    article_id VARCHAR(255),
                    interaction_type ENUM('view', 'bookmark', 'unbookmark', 'share', 'like', 'unlike') NOT NULL,
                    interaction_data JSON,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
                    
                    INDEX idx_interactions_user_created (user_id, created_at),
                    INDEX idx_interactions_article_type (article_id, interaction_type),
                    INDEX idx_interactions_type_created (interaction_type, created_at),
                    INDEX idx_interactions_user_article (user_id, article_id)
                )
            ");
            echo "✅ Created user_interactions table<br>";
            
            // Step 5: Create migration log table
            echo "<h4>Step 5: Creating migration log</h4>";
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migration_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration_name VARCHAR(255) NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    success BOOLEAN DEFAULT TRUE,
                    notes TEXT
                )
            ");
            
            // Log this migration
            $stmt = $pdo->prepare("INSERT INTO migration_log (migration_name, notes) VALUES (?, ?)");
            $stmt->execute(['001_user_management_schema', 'Added users table, user_bookmarks table, and extended articles table with engagement tracking']);
            echo "✅ Migration logged successfully<br>";
            
            // Step 6: Create indexes on articles table
            echo "<h4>Step 6: Creating performance indexes</h4>";
            try {
                $pdo->exec("CREATE INDEX idx_articles_view_count ON articles(view_count)");
                echo "✅ Created view_count index<br>";
            } catch (Exception $e) {
                echo "ℹ️ view_count index already exists<br>";
            }
            
            try {
                $pdo->exec("CREATE INDEX idx_articles_bookmark_count ON articles(bookmark_count)");
                echo "✅ Created bookmark_count index<br>";
            } catch (Exception $e) {
                echo "ℹ️ bookmark_count index already exists<br>";
            }
            
            $pdo->commit();
            echo "<div class='container success'><h4>🎉 Migration completed successfully!</h4></div>";
            
        } catch (Exception $e) {
            $pdo->rollback();
            echo "<div class='container error'><h4>❌ Migration failed: " . $e->getMessage() . "</h4></div>";
        }
        
        echo "</div>";
    }
    
    function verifyMigration($pdo) {
        echo "<div class='container'>";
        echo "<h3>🔍 Verifying Migration...</h3>";
        
        try {
            // Check users table
            $stmt = $pdo->query("DESCRIBE users");
            $users_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "✅ Users table exists with " . count($users_columns) . " columns<br>";
            
            // Check user_bookmarks table
            $stmt = $pdo->query("DESCRIBE user_bookmarks");
            $bookmarks_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "✅ User_bookmarks table exists with " . count($bookmarks_columns) . " columns<br>";
            
            // Check user_interactions table
            $stmt = $pdo->query("DESCRIBE user_interactions");
            $interactions_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "✅ User_interactions table exists with " . count($interactions_columns) . " columns<br>";
            
            // Check articles table extensions
            $stmt = $pdo->query("DESCRIBE articles");
            $articles_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (in_array('view_count', $articles_columns) && in_array('bookmark_count', $articles_columns)) {
                echo "✅ Articles table has been extended with engagement columns<br>";
            } else {
                echo "❌ Articles table is missing engagement columns<br>";
            }
            
            // Check migration log
            $stmt = $pdo->query("SELECT * FROM migration_log WHERE migration_name = '001_user_management_schema'");
            $log_entry = $stmt->fetch();
            
            if ($log_entry) {
                echo "✅ Migration logged at " . $log_entry['executed_at'] . "<br>";
            } else {
                echo "⚠️ Migration not found in log<br>";
            }
            
            echo "<div class='container success'><h4>✅ Migration verification completed!</h4></div>";
            
        } catch (Exception $e) {
            echo "<div class='container error'><h4>❌ Verification failed: " . $e->getMessage() . "</h4></div>";
        }
        
        echo "</div>";
    }
    
    function showDatabaseStructure($pdo) {
        echo "<div class='container'>";
        echo "<h3>🗄️ Database Structure</h3>";
        
        try {
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                echo "<h4>Table: $table</h4>";
                echo "<table>";
                echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                
                $stmt = $pdo->query("DESCRIBE $table");
                $columns = $stmt->fetchAll();
                
                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            }
            
        } catch (Exception $e) {
            echo "<div class='container error'>Error: " . $e->getMessage() . "</div>";
        }
        
        echo "</div>";
    }
    
    function resetMigration($pdo) {
        echo "<div class='container'>";
        echo "<h3>🔄 Resetting Migration...</h3>";
        
        try {
            $pdo->beginTransaction();
            
            // Drop tables in reverse order (due to foreign keys)
            $pdo->exec("DROP TABLE IF EXISTS user_interactions");
            echo "✅ Dropped user_interactions table<br>";
            
            $pdo->exec("DROP TABLE IF EXISTS user_bookmarks");
            echo "✅ Dropped user_bookmarks table<br>";
            
            $pdo->exec("DROP TABLE IF EXISTS users");
            echo "✅ Dropped users table<br>";
            
            // Remove columns from articles table
            try {
                $pdo->exec("ALTER TABLE articles DROP COLUMN view_count");
                echo "✅ Removed view_count column from articles<br>";
            } catch (Exception $e) {
                echo "ℹ️ view_count column didn't exist<br>";
            }
            
            try {
                $pdo->exec("ALTER TABLE articles DROP COLUMN bookmark_count");
                echo "✅ Removed bookmark_count column from articles<br>";
            } catch (Exception $e) {
                echo "ℹ️ bookmark_count column didn't exist<br>";
            }
            
            // Remove migration log entry
            $pdo->exec("DELETE FROM migration_log WHERE migration_name = '001_user_management_schema'");
            echo "✅ Removed migration log entry<br>";
            
            $pdo->commit();
            echo "<div class='container success'><h4>✅ Migration reset completed!</h4></div>";
            
        } catch (Exception $e) {
            $pdo->rollback();
            echo "<div class='container error'><h4>❌ Reset failed: " . $e->getMessage() . "</h4></div>";
        }
        
        echo "</div>";
    }
    ?>
    
    <div class="container info">
        <h3>📚 Next Steps</h3>
        <p>After running the migration successfully, you can:</p>
        <ol>
            <li>Start implementing user registration and login functionality</li>
            <li>Add bookmark buttons to your article pages</li>
            <li>Track user interactions for personalization</li>
            <li>Build user profile pages</li>
        </ol>
        
        <h4>🔗 Quick Links</h4>
        <ul>
            <li><a href="index.php">Return to Homepage</a></li>
            <li><a href="article.php">View Articles</a></li>
            <li><a href="setup-database.php?action=show_structure">View Database Structure</a></li>
        </ul>
    </div>
    
</body>
</html>