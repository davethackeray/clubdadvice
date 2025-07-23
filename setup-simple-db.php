<?php
require_once 'config-selector.php';

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!\n";
    
    // Drop all tables first to start fresh
    $tables_to_drop = ['related_topics', 'takeaways', 'article_tags', 'article_categories', 'article_age_groups', 'sources', 'tags', 'categories', 'age_groups', 'articles'];
    
    foreach ($tables_to_drop as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $table");
            echo "🗑️ Dropped table: $table\n";
        } catch (Exception $e) {
            // Ignore errors for non-existent tables
        }
    }
    
    // Create tables in correct order
    echo "\n📋 Creating tables...\n";
    
    // 1. Articles table (main table)
    $pdo->exec("
        CREATE TABLE articles (
            id VARCHAR(255) PRIMARY KEY,
            title VARCHAR(500) NOT NULL,
            summary TEXT,
            full_content LONGTEXT,
            content_type ENUM('heartwarming-story', 'practical-tip', 'expert-technique', 'real-life-hack', 'research-insight', 'problem-solution', 'funny-moment', 'aha-moment') NOT NULL,
            urgency ENUM('timeless', 'trending', 'seasonal', 'urgent') DEFAULT 'timeless',
            engagement_score INT DEFAULT 0,
            practical_score INT DEFAULT 0,
            universal_appeal INT DEFAULT 0,
            quote_highlight TEXT,
            newsletter_priority INT DEFAULT 0,
            app_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Created articles table\n";
    
    // 2. Age groups table
    $pdo->exec("
        CREATE TABLE age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            sort_order INT DEFAULT 0
        )
    ");
    echo "✅ Created age_groups table\n";
    
    // 3. Categories table
    $pdo->exec("
        CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            description TEXT,
            sort_order INT DEFAULT 0
        )
    ");
    echo "✅ Created categories table\n";
    
    // 4. Tags table
    $pdo->exec("
        CREATE TABLE tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )
    ");
    echo "✅ Created tags table\n";
    
    // 5. Insert default data
    echo "\n📝 Inserting default data...\n";
    
    // Insert age groups
    $age_groups = [
        ['newborn', 'Newborn (0-3 months)', 1],
        ['baby', 'Baby (3-12 months)', 2],
        ['toddler', 'Toddler (1-3 years)', 3],
        ['preschooler', 'Preschooler (3-5 years)', 4],
        ['school-age', 'School Age (5-11 years)', 5],
        ['tween', 'Tween (11-13 years)', 6],
        ['teenager', 'Teenager (13-18 years)', 7],
        ['young-adult', 'Young Adult (18+ years)', 8],
        ['all-ages', 'All Ages', 9]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO age_groups (name, display_name, sort_order) VALUES (?, ?, ?)");
    foreach ($age_groups as $group) {
        $stmt->execute($group);
    }
    echo "✅ Inserted " . count($age_groups) . " age groups\n";
    
    // Insert categories
    $categories = [
        ['sleep-solutions', 'Sleep Solutions', 'Tips and strategies for better sleep', 1],
        ['behaviour-management', 'Behaviour Management', 'Managing challenging behaviours', 2],
        ['emotional-intelligence', 'Emotional Intelligence', 'Building emotional skills', 3],
        ['communication-skills', 'Communication Skills', 'Improving parent-child communication', 4],
        ['educational-support', 'Educational Support', 'Supporting learning and development', 5],
        ['health-wellness', 'Health & Wellness', 'Physical and mental health advice', 6],
        ['family-dynamics', 'Family Dynamics', 'Building stronger family relationships', 7],
        ['screen-time-tech', 'Screen Time & Tech', 'Managing technology and screen time', 8],
        ['social-development', 'Social Development', 'Building social skills and friendships', 9],
        ['creativity-play', 'Creativity & Play', 'Encouraging creative expression', 10],
        ['nutrition-feeding', 'Nutrition & Feeding', 'Healthy eating and feeding advice', 11],
        ['safety-protection', 'Safety & Protection', 'Keeping children safe', 12],
        ['special-needs', 'Special Needs', 'Supporting children with special needs', 13],
        ['working-parent-tips', 'Working Parent Tips', 'Balancing work and parenting', 14],
        ['self-care-parent', 'Parent Self-Care', 'Taking care of yourself as a parent', 15],
        ['relationship-partnership', 'Relationship & Partnership', 'Maintaining relationships while parenting', 16]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, display_name, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "✅ Inserted " . count($categories) . " categories\n";
    
    // Insert some sample articles
    $sample_articles = [
        [
            'sample-article-1',
            'Getting Your Toddler to Sleep Through the Night',
            'Practical strategies that actually work for establishing healthy sleep routines.',
            'Full content about sleep strategies...',
            'practical-tip',
            'timeless',
            85,
            90,
            95
        ],
        [
            'sample-article-2',
            'The Magic of Bedtime Stories',
            'How reading together creates lasting bonds and improves language development.',
            'Full content about bedtime stories...',
            'heartwarming-story',
            'timeless',
            78,
            70,
            88
        ],
        [
            'sample-article-3',
            'Dealing with Toddler Tantrums',
            'Expert techniques for managing meltdowns with patience and understanding.',
            'Full content about tantrum management...',
            'expert-technique',
            'trending',
            92,
            95,
            85
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO articles (id, title, summary, full_content, content_type, urgency, engagement_score, practical_score, universal_appeal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($sample_articles as $article) {
        $stmt->execute($article);
    }
    echo "✅ Inserted " . count($sample_articles) . " sample articles\n";
    
    // Create indexes for performance
    echo "\n🚀 Creating indexes...\n";
    $pdo->exec("CREATE INDEX idx_articles_content_type ON articles(content_type)");
    $pdo->exec("CREATE INDEX idx_articles_urgency ON articles(urgency)");
    $pdo->exec("CREATE INDEX idx_articles_created_at ON articles(created_at)");
    $pdo->exec("CREATE INDEX idx_articles_engagement_score ON articles(engagement_score)");
    echo "✅ Created performance indexes\n";
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "📊 Summary:\n";
    echo "- Articles: " . count($sample_articles) . " sample articles\n";
    echo "- Age Groups: " . count($age_groups) . " groups\n";
    echo "- Categories: " . count($categories) . " categories\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>