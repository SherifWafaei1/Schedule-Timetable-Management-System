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

        // If a port is provided and host is localhost, force TCP by using 127.0.0.1
        // This avoids PDO trying to use a Unix socket which yields "No such file or directory" inside containers.
        if (!empty($this->port) && ($this->host === 'localhost' || $this->host === '::1')) {
            $this->host = '127.0.0.1';
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
            // Log full PDO message for debugging
            error_log("Connection error: " . $exception->getMessage());

            // If the error indicates a missing socket, add a hint to check DB_HOST/DB_PORT
            if (strpos($exception->getMessage(), 'No such file or directory') !== false || strpos($exception->getMessage(), "Can't connect to") !== false) {
                error_log("DB connection hint: If your DB is remote, set DB_HOST to the remote host (not 'localhost').\n" .
                          "If using a Render managed database, use the provided host and port and set DB_PORT=3306.\n" .
                          "To force TCP on localhost, set DB_PORT and/or set DB_FORCE_TCP=true.\n");
            }
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
