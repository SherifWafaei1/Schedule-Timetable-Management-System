<?php
require_once 'config/database.php';
try {
    $db = Database::getInstance()->getConnection();
    echo "Connection successful!";
} catch (Exception $e) {
    echo "Caught: " . $e->getMessage() . "\n";
    // Let's try to get more details if possible by bypassing the Singleton for a second to see the raw PDO error
    $host = 'localhost';
    $db_name = 'timetable_management';
    $username = 'root';
    $password = '';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        echo "Raw PDO connection successful!";
    } catch (PDOException $pe) {
        echo "Raw PDO Error: " . $pe->getMessage();
    }
}
