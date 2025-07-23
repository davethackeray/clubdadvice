<?php
/**
 * Quick Database Setup - Works around tablespace issues
 */

require_once 'config-selector.php';

echo "🔄 QUICK DATABASE SETUP\n";
echo "=" . str_repeat("=", 30) . "\n";

try {
    $pdo = getDBConnection();
    echo "✅ Connected to database: " . DB_NAME . "\n";
    
    // Drop tables in correct order (foreign keys first)
    $tables = ['articles', 'age_groups', 'categories', 'tags'];
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $table");
            echo "🗑️ Dropped table: $table\n";
        } catch (Exception $e) {
            echo "⚠️ Could not drop $table: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n📋 Creating tables...\n";
    
    // Create articles table
    $pdo->exec("
        CREATE TABLE articles (
            id VARCHAR(255) PRIMARY KEY,
            title VARCHAR(500) NOT NULL,
            summary TEXT,
            full_content LONGTEXT,
            content_type VARCHAR(50) NOT NULL DEFAULT 'practical-tip',
            urgency VARCHAR(20) DEFAULT 'timeless',
            engagement_score INT DEFAULT 0,
            practical_score INT DEFAULT 0,
            universal_appeal INT DEFAULT 0,
            quote_highlight TEXT,
            newsletter_priority INT DEFAULT 0,
            app_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Created articles table\n";
    
    // Create age_groups table
    $pdo->exec("
        CREATE TABLE age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB
    ");
    echo "✅ Created age_groups table\n";
    
    // Create categories table
    $pdo->exec("
        CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            description TEXT,
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB
    ");
    echo "✅ Created categories table\n";
    
    // Create tags table
    $pdo->exec("
        CREATE TABLE tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        ) ENGINE=InnoDB
    ");
    echo "✅ Created tags table\n";
    
    // Insert age groups
    $ageGroups = [
        ['newborn', 'Newborn (0-3 months)', 1],
        ['baby', 'Baby (3-12 months)', 2],
        ['toddler', 'Toddler (1-3 years)', 3],
        ['preschooler', 'Preschooler (3-5 years)', 4],
        ['school-age', 'School Age (5-11 years)', 5],
        ['all-ages', 'All Ages', 9]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO age_groups (name, display_name, sort_order) VALUES (?, ?, ?)");
    foreach ($ageGroups as $group) {
        $stmt->execute($group);
    }
    echo "✅ Inserted " . count($ageGroups) . " age groups\n";
    
    // Insert categories
    $categories = [
        ['sleep-solutions', 'Sleep Solutions', 'Tips and strategies for better sleep', 1],
        ['behaviour-management', 'Behaviour Management', 'Managing challenging behaviours', 2],
        ['emotional-intelligence', 'Emotional Intelligence', 'Building emotional skills', 3],
        ['communication-skills', 'Communication Skills', 'Improving parent-child communication', 4],
        ['family-dynamics', 'Family Dynamics', 'Building stronger family relationships', 5]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, display_name, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "✅ Inserted " . count($categories) . " categories\n";
    
    // Insert sample articles
    $articles = [
        [
            'bedtime-routines-power',
            'The Power of Bedtime Routines',
            'Discover how consistent bedtime routines can transform your family\'s sleep patterns.',
            'Creating a consistent bedtime routine is one of the most powerful tools parents have for ensuring good sleep for the whole family. Research shows that children who follow regular bedtime routines fall asleep faster, sleep more soundly, and wake up more refreshed.',
            'practical-tip',
            'timeless',
            85, 90, 95
        ],
        [
            'emotional-regulation-play',
            'Teaching Emotional Regulation Through Play',
            'Learn how to use play-based activities to help children manage their emotions.',
            'Play is a child\'s natural language, and it\'s also one of the most effective ways to teach emotional regulation. When children are playing, they\'re relaxed and open to learning.',
            'expert-technique',
            'trending',
            78, 85, 88
        ],
        [
            'one-on-one-time-magic',
            'The Magic of One-on-One Time',
            'Why spending individual time with each child strengthens relationships.',
            'In busy families, it\'s easy for children to feel lost in the shuffle. One-on-one time with each child, even just 15 minutes a day, can work wonders.',
            'heartwarming-story',
            'timeless',
            92, 80, 90
        ],
        [
            'toddler-tantrums-guide',
            'Understanding Toddler Tantrums',
            'A practical guide to handling toddler meltdowns with patience and understanding.',
            'Toddler tantrums are a normal part of development, but they can be challenging for parents. Understanding why tantrums happen and having strategies to handle them can make a huge difference.',
            'problem-solution',
            'urgent',
            88, 92, 85
        ],
        [
            'screen-time-balance',
            'Finding Screen Time Balance',
            'Practical strategies for managing technology use in modern families.',
            'Screen time doesn\'t have to be the enemy. With thoughtful planning and clear boundaries, technology can be a positive part of your family\'s life.',
            'practical-tip',
            'trending',
            82, 88, 90
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO articles (id, title, summary, full_content, content_type, urgency, engagement_score, practical_score, universal_appeal) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($articles as $article) {
        $stmt->execute($article);
    }
    echo "✅ Inserted " . count($articles) . " sample articles\n";
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "Ready for testing!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>