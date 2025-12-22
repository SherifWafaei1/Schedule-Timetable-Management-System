<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected.\n";
    
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables found in DB:\n";
    foreach ($tables as $t) {
        echo "- " . $t . "\n";
    }
    
    // Check specific tables counts if they exist
    $check = ['subjects', 'rooms', 'classes'];
    foreach ($check as $t) {
        if (in_array($t, $tables)) {
            $c = $db->query("SELECT COUNT(*) FROM $t")->fetchColumn();
            echo "$t count: $c\n";
        } else {
            echo "$t MISSING\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
