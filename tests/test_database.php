<?php
/**
 * Simple database connection test
 * Run this file to verify database connectivity and basic functionality
 */

require_once '../config.php';

echo "<h1>Club Dadvice - Database Test</h1>\n";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>\n";
    
    // Test basic queries
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
    $result = $stmt->fetch();
    echo "<p>📄 Total articles: {$result['count']}</p>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM age_groups");
    $result = $stmt->fetch();
    echo "<p>👶 Age groups: {$result['count']}</p>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "<p>📂 Categories: {$result['count']}</p>\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tags");
    $result = $stmt->fetch();
    echo "<p>🏷️ Tags: {$result['count']}</p>\n";
    
    echo "<p style='color: green;'>✅ All database tables accessible!</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><a href='../index.php'>← Back to site</a></p>\n";
?>