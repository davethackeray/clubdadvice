-- Migration 001: User Management Schema Extensions
-- This migration adds user management capabilities to the existing Dadvice platform
-- Safe to run multiple times (uses IF NOT EXISTS and ADD COLUMN IF NOT EXISTS patterns)

-- ============================================================================
-- STEP 1: Extend existing articles table with engagement tracking columns
-- ============================================================================

-- Add view_count column to articles table (safe if column already exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'articles' 
     AND COLUMN_NAME = 'view_count') = 0,
    'ALTER TABLE articles ADD COLUMN view_count INT DEFAULT 0',
    'SELECT "view_count column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add bookmark_count column to articles table (safe if column already exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'articles' 
     AND COLUMN_NAME = 'bookmark_count') = 0,
    'ALTER TABLE articles ADD COLUMN bookmark_count INT DEFAULT 0',
    'SELECT "bookmark_count column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- STEP 2: Create users table with essential fields
-- ============================================================================

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
    
    -- Indexes for performance
    INDEX idx_users_email (email),
    INDEX idx_users_created_at (created_at),
    INDEX idx_users_last_login (last_login),
    INDEX idx_users_active (is_active),
    INDEX idx_users_email_verified (email_verified)
);

-- ============================================================================
-- STEP 3: Create user_bookmarks table for immediate user value
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_bookmarks (
    user_id INT NOT NULL,
    article_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    is_favorite BOOLEAN DEFAULT FALSE,
    
    PRIMARY KEY (user_id, article_id),
    
    -- Foreign key constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_bookmarks_user_created (user_id, created_at),
    INDEX idx_bookmarks_article (article_id),
    INDEX idx_bookmarks_favorites (user_id, is_favorite)
);

-- ============================================================================
-- STEP 4: Create user_interactions table for tracking engagement
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    article_id VARCHAR(255),
    interaction_type ENUM('view', 'bookmark', 'unbookmark', 'share', 'like', 'unlike') NOT NULL,
    interaction_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints (with SET NULL for user_id to preserve analytics if user deleted)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    
    -- Indexes for performance and analytics
    INDEX idx_interactions_user_created (user_id, created_at),
    INDEX idx_interactions_article_type (article_id, interaction_type),
    INDEX idx_interactions_type_created (interaction_type, created_at),
    INDEX idx_interactions_user_article (user_id, article_id)
);

-- ============================================================================
-- STEP 5: Create indexes on articles table for new columns
-- ============================================================================

-- Add indexes for the new engagement columns (safe if indexes already exist)
CREATE INDEX IF NOT EXISTS idx_articles_view_count ON articles(view_count);
CREATE INDEX IF NOT EXISTS idx_articles_bookmark_count ON articles(bookmark_count);
CREATE INDEX IF NOT EXISTS idx_articles_engagement ON articles(view_count, bookmark_count);

-- ============================================================================
-- STEP 6: Create triggers to automatically update article engagement counts
-- ============================================================================

-- Drop triggers if they exist (to allow re-running migration)
DROP TRIGGER IF EXISTS update_bookmark_count_on_insert;
DROP TRIGGER IF EXISTS update_bookmark_count_on_delete;
DROP TRIGGER IF EXISTS update_view_count_on_view;

-- Trigger to increment bookmark_count when bookmark is added
DELIMITER $$
CREATE TRIGGER update_bookmark_count_on_insert
    AFTER INSERT ON user_bookmarks
    FOR EACH ROW
BEGIN
    UPDATE articles 
    SET bookmark_count = bookmark_count + 1 
    WHERE id = NEW.article_id;
END$$
DELIMITER ;

-- Trigger to decrement bookmark_count when bookmark is removed
DELIMITER $$
CREATE TRIGGER update_bookmark_count_on_delete
    AFTER DELETE ON user_bookmarks
    FOR EACH ROW
BEGIN
    UPDATE articles 
    SET bookmark_count = bookmark_count - 1 
    WHERE id = OLD.article_id;
END$$
DELIMITER ;

-- Trigger to increment view_count when view interaction is recorded
DELIMITER $$
CREATE TRIGGER update_view_count_on_view
    AFTER INSERT ON user_interactions
    FOR EACH ROW
BEGIN
    IF NEW.interaction_type = 'view' THEN
        UPDATE articles 
        SET view_count = view_count + 1 
        WHERE id = NEW.article_id;
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- STEP 7: Migration completion log
-- ============================================================================

-- Create migration log table if it doesn't exist
CREATE TABLE IF NOT EXISTS migration_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(255) NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT TRUE,
    notes TEXT
);

-- Log this migration
INSERT INTO migration_log (migration_name, notes) 
VALUES ('001_user_management_schema', 'Added users table, user_bookmarks table, and extended articles table with engagement tracking');

-- ============================================================================
-- VERIFICATION QUERIES (commented out - uncomment to verify migration)
-- ============================================================================

/*
-- Verify articles table has new columns
DESCRIBE articles;

-- Verify users table was created
DESCRIBE users;

-- Verify user_bookmarks table was created
DESCRIBE user_bookmarks;

-- Verify user_interactions table was created
DESCRIBE user_interactions;

-- Check indexes were created
SHOW INDEX FROM articles WHERE Key_name LIKE 'idx_articles_%';
SHOW INDEX FROM users;
SHOW INDEX FROM user_bookmarks;
SHOW INDEX FROM user_interactions;

-- Check triggers were created
SHOW TRIGGERS LIKE 'update_%';

-- Verify migration was logged
SELECT * FROM migration_log WHERE migration_name = '001_user_management_schema';
*/

SELECT 'Migration 001_user_management_schema completed successfully!' as status;