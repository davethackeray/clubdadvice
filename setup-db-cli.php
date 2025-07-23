<?php
/**
 * Command Line Database Setup for XAMPP
 */

require_once 'config.local.php';

echo "=== Club Dadvice Database Setup ===\n";

try {
    $pdo = getDBConnection();
    echo "✅ Connected to database: " . DB_NAME . "\n";
    
    // Create articles table
    echo "Creating articles table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            summary TEXT,
            full_content LONGTEXT,
            content_type ENUM('advice', 'story', 'tip', 'interview') DEFAULT 'advice',
            urgency ENUM('low', 'medium', 'high') DEFAULT 'medium',
            quote_highlight TEXT,
            takeaways JSON,
            source JSON,
            age_groups JSON,
            categories JSON,
            view_count INT DEFAULT 0,
            bookmark_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_articles_created_at (created_at),
            INDEX idx_articles_content_type (content_type),
            INDEX idx_articles_urgency (urgency)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Articles table created\n";
    
    // Create users table
    echo "Creating users table...\n";
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
            password_reset_expires DATETIME,
            remember_token VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            login_count INT DEFAULT 0,
            INDEX idx_users_email (email),
            INDEX idx_users_created_at (created_at),
            INDEX idx_users_last_login (last_login),
            INDEX idx_users_active (is_active),
            INDEX idx_users_email_verified (email_verified),
            INDEX idx_users_email_verification_token (email_verification_token),
            INDEX idx_users_password_reset_token (password_reset_token),
            INDEX idx_users_remember_token (remember_token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Users table created\n";
    
    // Create bookmarks table
    echo "Creating bookmarks table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_bookmarks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            article_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_bookmark (user_id, article_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
            INDEX idx_bookmarks_user_id (user_id),
            INDEX idx_bookmarks_article_id (article_id),
            INDEX idx_bookmarks_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ User bookmarks table created\n";
    
    // Create user interactions table
    echo "Creating user interactions table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_interactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            article_id INT NOT NULL,
            interaction_type ENUM('view', 'bookmark', 'unbookmark') NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
            INDEX idx_interactions_user_id (user_id),
            INDEX idx_interactions_article_id (article_id),
            INDEX idx_interactions_type (interaction_type),
            INDEX idx_interactions_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ User interactions table created\n";
    
    // Insert sample article data
    echo "Inserting sample data...\n";
    $sample_article = $pdo->prepare("
        INSERT IGNORE INTO articles (id, title, summary, full_content, content_type, urgency, age_groups, categories) 
        VALUES (1, ?, ?, ?, 'advice', 'medium', ?, ?)
    ");
    
    $sample_article->execute([
        'Building Confidence in Your Child',
        'Learn practical strategies to help your child develop self-confidence and resilience.',
        'Building confidence in children is one of the most important gifts we can give them. Confidence affects every aspect of a child\'s life - from their willingness to try new things to their ability to bounce back from setbacks. Here are some proven strategies to help build your child\'s confidence...',
        json_encode([['name' => '5-12', 'display_name' => '5-12 years']]),
        json_encode([['name' => 'emotional-development', 'display_name' => 'Emotional Development']])
    ]);
    echo "✅ Sample article inserted\n";
    
    // Verify tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\n=== Database Setup Complete! ===\n";
    echo "Created " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "  - " . $table . "\n";
    }
    
    echo "\nNext steps:\n";
    echo "1. Test authentication: php test-auth-system.php\n";
    echo "2. Start XAMPP Apache server\n";
    echo "3. Visit http://localhost/parentTime/register.php\n";
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>