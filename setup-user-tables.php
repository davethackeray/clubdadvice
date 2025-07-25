<?php
require_once 'config.local.php';

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!\n";
    
    // Create a simple articles table for testing
    $pdo->exec("DROP TABLE IF EXISTS articles");
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
    
    // Create age_groups table
    $pdo->exec("DROP TABLE IF EXISTS age_groups");
    $pdo->exec("
        CREATE TABLE age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            display_name VARCHAR(100) NOT NULL
        )
    ");
    echo "✅ Created age_groups table\n";
    
    // Create categories table
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("
        CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            display_name VARCHAR(100) NOT NULL
        )
    ");
    echo "✅ Created categories table\n";
    
    // Create tags table
    $pdo->exec("DROP TABLE IF EXISTS tags");
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
    echo "✅ Inserted age groups\n";
    
    $pdo->exec("INSERT INTO categories (name, display_name) VALUES 
        ('sleep-solutions', 'Sleep Solutions'),
        ('behaviour-management', 'Behaviour Management'),
        ('educational-support', 'Educational Support')
    ");
    echo "✅ Inserted categories\n";
    
    $pdo->exec("INSERT INTO articles (id, title, summary, full_content) VALUES 
        ('test-1', 'Sample Article 1', 'This is a test article', 'Full content here...'),
        ('test-2', 'Sample Article 2', 'Another test article', 'More full content...'),
        ('test-3', 'Sample Article 3', 'Third test article', 'Even more content...')
    ");
    echo "✅ Inserted sample articles\n";
    
    echo "\n🎉 Database setup completed successfully!\n";
    
    // Verify the setup
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
    $count = $stmt->fetch()['count'];
    echo "📊 Articles in database: $count\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>