<?php
require_once 'config/database.php';

echo "Database Setup Tool\n";
echo "-------------------\n";

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";
    
    $sqlFile = 'database_schema.sql';
    if (!file_exists($sqlFile)) {
        die("Error: $sqlFile not found.\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // Split into statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (empty($stmt)) continue;
        
        // Skip USE and CREATE DATABASE as we are already connected
        if (stripos($stmt, 'USE ') === 0) continue;
        if (stripos($stmt, 'CREATE DATABASE ') === 0) continue;
        
        try {
            $db->exec($stmt);
            echo "Executed: " . substr($stmt, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Ignore "Table already exists" errors (Code 42S01) and "Duplicate entry" (Code 23000)
            if ($e->getCode() == '42S01' || strpos($e->getMessage(), 'already exists') !== false) {
                 echo "Skipped (Exists): " . substr($stmt, 0, 50) . "...\n";
            } elseif ($e->getCode() == '23000') {
                 echo "Skipped (Duplicate): " . substr($stmt, 0, 50) . "...\n";
            } else {
                 echo "Error executing: " . substr($stmt, 0, 50) . "...\n";
                 echo "Message: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nSetup completed.\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>
