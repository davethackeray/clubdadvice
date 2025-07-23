<?php
require_once 'config.local.php';

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server\n";
    
    // Drop and recreate the database
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    echo "🗑️ Dropped database " . DB_NAME . "\n";
    
    $pdo->exec("CREATE DATABASE " . DB_NAME);
    echo "✅ Created database " . DB_NAME . "\n";
    
    $pdo->exec("USE " . DB_NAME);
    echo "✅ Using database " . DB_NAME . "\n";
    
    // Now create tables
    $pdo->exec("
        CREATE TABLE articles (
            id VARCHAR(255) PRIMARY KEY,
            title VARCHAR(500) NOT NULL,
            summary TEXT,
            full_content LONGTEXT,
            content_type VARCHAR(50) DEFAULT 'practical-tip',
            engagement_score INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Created articles table\n";
    
    $pdo->exec("
        CREATE TABLE age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            display_name VARCHAR(100) NOT NULL
        )
    ");
    echo "✅ Created age_groups table\n";
    
    $pdo->exec("
        CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            display_name VARCHAR(100) NOT NULL
        )
    ");
    echo "✅ Created categories table\n";
    
    $pdo->exec("
        CREATE TABLE tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");
    echo "✅ Created tags table\n";
    
    // Insert sample data
    $pdo->exec("INSERT INTO age_groups (name, display_name) VALUES 
        ('toddler', 'Toddler (1-3 years)'),
        ('preschooler', 'Preschooler (3-5 years)'),
        ('school-age', 'School Age (5-11 years)')
    ");
    
    $pdo->exec("INSERT INTO categories (name, display_name) VALUES 
        ('sleep-solutions', 'Sleep Solutions'),
        ('behaviour-management', 'Behaviour Management'),
        ('educational-support', 'Educational Support')
    ");
    
    $pdo->exec("INSERT INTO articles (id, title, summary, full_content) VALUES 
        ('test-1', 'Getting Your Toddler to Sleep', 'Practical sleep strategies', 'Detailed content about sleep routines...'),
        ('test-2', 'Managing Toddler Tantrums', 'Expert tantrum management', 'Full guide to handling meltdowns...'),
        ('test-3', 'Educational Activities for Preschoolers', 'Fun learning activities', 'Creative ways to support learning...')
    ");
    
    echo "✅ Inserted sample data\n";
    echo "\n🎉 Database reset and setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>