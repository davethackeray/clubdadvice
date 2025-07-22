<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Available PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";

// Test if we can connect to a SQLite database for development
try {
    $pdo = new PDO('sqlite:test.db');
    echo "✓ SQLite connection works\n";
    $pdo = null;
} catch (Exception $e) {
    echo "✗ SQLite connection failed: " . $e->getMessage() . "\n";
}

// Check if MySQL extension is available
if (extension_loaded('mysql') || extension_loaded('mysqli') || extension_loaded('pdo_mysql')) {
    echo "✓ MySQL extension available\n";
} else {
    echo "✗ No MySQL extension found\n";
}
?>