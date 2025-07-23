<?php
/**
 * Database Reset Script
 * Handles tablespace issues and recreates database cleanly
 */

require_once 'config-selector.php';

echo "🔄 RESETTING DATABASE\n";
echo "=" . str_repeat("=", 30) . "\n";

try {
    // Connect to MySQL server (not specific database)
    $dsn = "mysql:host=" . DB_HOST;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✅ Connected to MySQL server\n";
    
    // Drop database completely
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    echo "🗑️ Dropped database: " . DB_NAME . "\n";
    
    // Create fresh database
    $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Created database: " . DB_NAME . "\n";
    
    // Switch to new database
    $pdo->exec("USE " . DB_NAME);
    
    // Create tables
    echo "\n📋 Creating tables...\n";
    
    // Articles table
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
    
    // Age groups table
    $pdo->exec("
        CREATE TABLE age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            sort_order INT DEFAULT 0
        )
    ");
    echo "✅ Created age_groups table\n";
    
    // Categories table
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
    
    // Tags table
    $pdo->exec("
        CREATE TABLE tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )
    ");
    echo "✅ Created tags table\n";
    
    // Insert default age groups
    $ageGroups = [
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
    foreach ($ageGroups as $group) {
        $stmt->execute($group);
    }
    echo "✅ Inserted " . count($ageGroups) . " age groups\n";
    
    // Insert default categories
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
        ['creativity-play', 'Creativity & Play', 'Encouraging creative expression', 10]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, display_name, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    echo "✅ Inserted " . count($categories) . " categories\n";
    
    // Insert sample articles for testing
    $sampleArticles = [
        [
            'sample-article-1',
            'The Power of Bedtime Routines',
            'Discover how consistent bedtime routines can transform your family\'s sleep patterns and create lasting positive habits.',
            'Creating a consistent bedtime routine is one of the most powerful tools parents have for ensuring good sleep for the whole family. Research shows that children who follow regular bedtime routines fall asleep faster, sleep more soundly, and wake up more refreshed. The key is consistency and making the routine enjoyable rather than a battle. Start with a warm bath, followed by quiet activities like reading or gentle music. Keep the same order every night, and involve your child in choosing some elements of the routine. This gives them a sense of control while maintaining the structure they need. Remember, the goal isn\'t perfection - it\'s progress. Even small improvements in your bedtime routine can lead to significant improvements in sleep quality for everyone.',
            'practical-tip',
            'timeless',
            85,
            90,
            95
        ],
        [
            'sample-article-2',
            'Teaching Emotional Regulation Through Play',
            'Learn how to use play-based activities to help children understand and manage their emotions effectively.',
            'Play is a child\'s natural language, and it\'s also one of the most effective ways to teach emotional regulation. When children are playing, they\'re relaxed and open to learning. You can use simple games to help them identify emotions, practice coping strategies, and build emotional intelligence. Try emotion charades where you act out different feelings, or create an emotion thermometer to help them rate their feelings from 1-10. Board games are excellent for teaching patience and handling disappointment. Building blocks can teach persistence and problem-solving. The key is to be present and engaged, using these moments to guide them through emotional challenges in a safe, supportive environment.',
            'expert-technique',
            'trending',
            78,
            85,
            88
        ],
        [
            'sample-article-3',
            'The Magic of One-on-One Time',
            'Why spending individual time with each child can strengthen your relationship and improve behavior.',
            'In busy families, it\'s easy for children to feel lost in the shuffle. One-on-one time with each child, even just 15 minutes a day, can work wonders for your relationship and their behavior. This doesn\'t have to be elaborate - it could be as simple as having them help you cook dinner, going for a walk together, or reading a book before bed. The key is that it\'s uninterrupted time focused entirely on them. During this time, let them lead the conversation or activity. Listen actively and show genuine interest in what they\'re sharing. This special time helps children feel valued and heard, which often reduces attention-seeking behaviors and strengthens your bond.',
            'heartwarming-story',
            'timeless',
            92,
            80,
            90
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO articles (id, title, summary, full_content, content_type, urgency, engagement_score, practical_score, universal_appeal) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($sampleArticles as $article) {
        $stmt->execute($article);
    }
    echo "✅ Inserted " . count($sampleArticles) . " sample articles\n";
    
    echo "\n🎉 Database reset completed successfully!\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Tables created: articles, age_groups, categories, tags\n";
    echo "Sample data inserted for testing\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>