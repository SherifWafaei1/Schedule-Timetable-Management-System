<?php
/**
 * PHPUnit Bootstrap File
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define some dummy session data for testing
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// Load necessary constants
require_once __DIR__ . '/../config/constants.php';

// Mock some essential functions if needed, or load the real ones
require_once __DIR__ . '/../includes/functions.php';

// Load the composer autoloader if it exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Ensure classes are loaded
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../includes/classes/';
    
    // Check main classes and Export subdirectory
    $files = [
        $base_dir . $class . '.php',
        $base_dir . 'Export/' . $class . '.php'
    ];

    foreach ($files as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Setup mock database connection if necessary for unit tests
// For now, we'll let tests handle their own mocking
