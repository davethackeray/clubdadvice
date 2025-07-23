<?php
/**
 * Local Database Setup Script
 * Run this to set up the basic database structure for local development
 */

require_once 'config-selector.php';

echo "Setting up local database structure...\n";

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!\n";
    
    // Create age_groups table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS age_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            sort_order INT DEFAULT 0
        )
    ");
    echo "✅ Created age_groups table\n";
    
    // Create categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            description TEXT,
            sort_order INT DEFAULT 0
        )
    ");
    echo "✅ Created categories table\n";
    
    // Create tags table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )
    ");
    echo "✅ Created tags table\n";
    
    // Insert default age groups
    $pdo->exec("
        INSERT IGNORE INTO age_groups (name, display_name, sort_order) VALUES
        ('newborn', 'Newborn (0-3 months)', 1),
        ('baby', 'Baby (3-12 months)', 2),
        ('toddler', 'Toddler (1-3 years)', 3),
        ('preschooler', 'Preschooler (3-5 years)', 4),
        ('school-age', 'School Age (5-11 years)', 5),
        ('tween', 'Tween (11-13 years)', 6),
        ('teenager', 'Teenager (13-18 years)', 7),
        ('young-adult', 'Young Adult (18+ years)', 8),
        ('all-ages', 'All Ages', 9)
    ");
    echo "✅ Inserted default age groups\n";
    
    // Insert default categories
    $pdo->exec("
        INSERT IGNORE INTO categories (name, display_name, description, sort_order) VALUES
        ('sleep-solutions', 'Sleep Solutions', 'Tips and strategies for better sleep', 1),
        ('behaviour-management', 'Behaviour Management', 'Managing challenging behaviours', 2),
        ('emotional-intelligence', 'Emotional Intelligence', 'Building emotional skills', 3),
        ('communication-skills', 'Communication Skills', 'Improving parent-child communication', 4),
        ('educational-support', 'Educational Support', 'Supporting learning and development', 5),
        ('health-wellness', 'Health & Wellness', 'Physical and mental health advice', 6),
        ('family-dynamics', 'Family Dynamics', 'Building stronger family relationships', 7),
        ('screen-time-tech', 'Screen Time & Tech', 'Managing technology and screen time', 8),
        ('social-development', 'Social Development', 'Building social skills and friendships', 9),
        ('creativity-play', 'Creativity & Play', 'Encouraging creative expression', 10)
    ");
    echo "✅ Inserted default categories\n";
    
    echo "\n🎉 Local database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>