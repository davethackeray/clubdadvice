<?php
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    echo "✅ Database connection successful!\n";
    
    // Test creating database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS club_dadvice_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'club_dadvice_local' created/verified\n";
    
    // Test connecting to the specific database
    $pdo2 = new PDO('mysql:host=localhost;dbname=club_dadvice_local;charset=utf8mb4', 'root', '');
    echo "✅ Connected to club_dadvice_local database\n";
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>