<?php
/**
 * Database Configuration
 * Schedule Time Table Management System
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'timetable_management';
    private $username = 'root';
    private $password = '';
    private $port = null;
    private $charset = 'utf8mb4';
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    public $conn;
    private static $instance = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        // Allow overriding via environment variables (Render, Docker, etc.)
        $this->host = getenv('DB_HOST') ?: $this->host;
        $this->db_name = getenv('DB_DATABASE') ?: $this->db_name;
        $this->username = getenv('DB_USERNAME') ?: $this->username;
        $this->password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : $this->password;
        $this->port = getenv('DB_PORT') ?: $this->port;

        // Support a DATABASE_URL environment variable (mysql://user:pass@host:port/db)
        $databaseUrl = getenv('DATABASE_URL');
        if ($databaseUrl) {
            $parts = parse_url($databaseUrl);
            if ($parts !== false) {
                if (!empty($parts['host'])) $this->host = $parts['host'];
                if (!empty($parts['port'])) $this->port = $parts['port'];
                if (!empty($parts['user'])) $this->username = $parts['user'];
                if (array_key_exists('pass', $parts)) $this->password = $parts['pass'];
                if (!empty($parts['path'])) $this->db_name = ltrim($parts['path'], '/');
            }
        }
    }

    /**
     * Get the Singleton instance of the Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get database connection
     */
    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }
        
        try {
            $dsn = "mysql:host=" . $this->host;
            if (!empty($this->port)) {
                $dsn .= ";port=" . $this->port;
            }
            $dsn .= ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            // In debug mode, surface the real PDO message to help with diagnosing on Render.
            $debug = getenv('APP_DEBUG');
            if ($debug === '1' || strtolower($debug) === 'true') {
                throw new Exception("Database connection failed: " . $exception->getMessage());
            }
            // Generic message for production
            throw new Exception("Database connection failed");
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
