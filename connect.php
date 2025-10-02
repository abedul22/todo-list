<?php
/**
 * Database Connection File for Admin To-Do List System
 * 
 * This file provides secure database connection using PDO
 * with proper error handling and security measures.
 * 
 * @author Your Name
 * @version 1.0
 * @created 2024
 */

// Prevent direct access
if (!defined('APP_ACCESS')) {
    define('APP_ACCESS', true);
}

// Database Configuration
class DatabaseConfig {
    // Database connection parameters
    private const DB_HOST = 'localhost';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_NAME = 'todo_admin';
    private const DB_CHARSET = 'utf8mb4';
    private const DB_PORT = 3306;
    
    // Connection options
    private const DB_OPTIONS = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_TIMEOUT            => 30,
        PDO::ATTR_PERSISTENT         => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ];
    
    private static $instance = null;
    private $connection = null;
    private $isConnected = false;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get singleton instance
     * 
     * @return DatabaseConfig
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * 
     * @return void
     * @throws PDOException
     */
    private function connect() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                self::DB_HOST,
                self::DB_PORT,
                self::DB_NAME,
                self::DB_CHARSET
            );
            
            $this->connection = new PDO($dsn, self::DB_USER, self::DB_PASS, self::DB_OPTIONS);
            $this->isConnected = true;
            
            // Log successful connection (in development only)
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Database connected successfully to " . self::DB_NAME);
            }
            
        } catch (PDOException $e) {
            $this->isConnected = false;
            $this->logError('Database connection failed', $e);
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get database connection
     * 
     * @return PDO
     * @throws Exception
     */
    public function getConnection() {
        if (!$this->isConnected || $this->connection === null) {
            throw new Exception('Database connection not available');
        }
        return $this->connection;
    }
    
    /**
     * Check if database is connected
     * 
     * @return bool
     */
    public function isConnected() {
        return $this->isConnected && $this->connection !== null;
    }
    
    /**
     * Test database connection
     * 
     * @return bool
     */
    public function testConnection() {
        try {
            if ($this->connection) {
                $stmt = $this->connection->query('SELECT 1');
                return $stmt !== false;
            }
            return false;
        } catch (PDOException $e) {
            $this->logError('Connection test failed', $e);
            return false;
        }
    }
    
    /**
     * Get database statistics
     * 
     * @return array
     */
    public function getStats() {
        try {
            $stats = [];
            
            // Get database size
            $stmt = $this->connection->prepare("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS db_size_mb,
                    COUNT(*) as table_count
                FROM information_schema.tables 
                WHERE table_schema = ?
            ");
            $stmt->execute([self::DB_NAME]);
            $dbInfo = $stmt->fetch();
            
            $stats['database_size_mb'] = $dbInfo['db_size_mb'] ?? 0;
            $stats['table_count'] = $dbInfo['table_count'] ?? 0;
            
            // Get table row counts
            $tables = ['tasks', 'admin_users', 'activity_logs'];
            foreach ($tables as $table) {
                $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM `{$table}`");
                $stmt->execute();
                $result = $stmt->fetch();
                $stats["{$table}_count"] = $result['count'] ?? 0;
            }
            
            return $stats;
        } catch (PDOException $e) {
            $this->logError('Failed to get database stats', $e);
            return [];
        }
    }
    
    /**
     * Close database connection
     * 
     * @return void
     */
    public function closeConnection() {
        $this->connection = null;
        $this->isConnected = false;
    }
    
    /**
     * Log database errors
     * 
     * @param string $message
     * @param PDOException $exception
     * @return void
     */
    private function logError($message, PDOException $exception) {
        $errorMsg = sprintf(
            "[%s] %s - Error: %s (Code: %s)",
            date('Y-m-d H:i:s'),
            $message,
            $exception->getMessage(),
            $exception->getCode()
        );
        
        // Log to file if logging is enabled
        if (defined('LOG_ERRORS') && LOG_ERRORS) {
            $logFile = defined('ERROR_LOG_FILE') ? ERROR_LOG_FILE : 'logs/database_errors.log';
            error_log($errorMsg . PHP_EOL, 3, $logFile);
        }
        
        // Also log to PHP error log
        error_log($errorMsg);
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Clean up on destruction
     */
    public function __destruct() {
        $this->closeConnection();
    }
}

// Global convenience functions
if (!function_exists('getDbConnection')) {
    /**
     * Get database connection instance
     * 
     * @return PDO
     * @throws Exception
     */
    function getDbConnection() {
        return DatabaseConfig::getInstance()->getConnection();
    }
}

if (!function_exists('testDbConnection')) {
    /**
     * Test database connection
     * 
     * @return bool
     */
    function testDbConnection() {
        try {
            return DatabaseConfig::getInstance()->testConnection();
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getDbStats')) {
    /**
     * Get database statistics
     * 
     * @return array
     */
    function getDbStats() {
        try {
            return DatabaseConfig::getInstance()->getStats();
        } catch (Exception $e) {
            return [];
        }
    }
}

// Initialize database connection (lazy loading)
try {
    // Create PDO instance for backward compatibility
    $pdo = getDbConnection();
    
    // Set global variables for compatibility with existing code
    $GLOBALS['pdo'] = $pdo;
    
    // Create database tables if they don't exist
    if (!function_exists('initializeDatabase')) {
        /**
         * Initialize database tables
         * 
         * @return bool
         */
        function initializeDatabase() {
            try {
                $pdo = getDbConnection();
                
                // Check if tables exist
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as table_count 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name IN ('tasks', 'admin_users', 'activity_logs')
                ");
                $stmt->execute();
                $result = $stmt->fetch();
                
                if ($result['table_count'] < 3) {
                    // Tables don't exist, create them
                    $sqlFile = __DIR__ . '/database_setup.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        $pdo->exec($sql);
                        return true;
                    }
                }
                return true;
            } catch (Exception $e) {
                error_log("Failed to initialize database: " . $e->getMessage());
                return false;
            }
        }
    }
    
    // Auto-initialize database in development mode
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        initializeDatabase();
    }
    
} catch (Exception $e) {
    // Handle connection error gracefully
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die("Database connection failed: " . $e->getMessage());
    } else {
        // In production, show generic error
        die("Database service temporarily unavailable. Please try again later.");
    }
}

/**
 * Database utility class for common operations
 */
class DatabaseUtils {
    private static $pdo;
    
    public static function init() {
        self::$pdo = getDbConnection();
    }
    
    /**
     * Execute a prepared statement safely
     * 
     * @param string $sql
     * @param array $params
     * @return PDOStatement|false
     */
    public static function execute($sql, $params = []) {
        try {
            if (!self::$pdo) self::init();
            
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get single row
     * 
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public static function fetchOne($sql, $params = []) {
        $stmt = self::execute($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    /**
     * Get multiple rows
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::execute($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Get last inserted ID
     * 
     * @return string
     */
    public static function getLastInsertId() {
        if (!self::$pdo) self::init();
        return self::$pdo->lastInsertId();
    }
    
    /**
     * Begin transaction
     * 
     * @return bool
     */
    public static function beginTransaction() {
        if (!self::$pdo) self::init();
        return self::$pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     * 
     * @return bool
     */
    public static function commit() {
        if (!self::$pdo) self::init();
        return self::$pdo->commit();
    }
    
    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public static function rollback() {
        if (!self::$pdo) self::init();
        return self::$pdo->rollback();
    }
}

// Health check endpoint (optional)
if (isset($_GET['health']) && $_GET['health'] === 'db') {
    header('Content-Type: application/json');
    
    $health = [
        'status' => 'ok',
        'timestamp' => date('c'),
        'database' => [
            'connected' => testDbConnection(),
            'stats' => getDbStats()
        ]
    ];
    
    if (!$health['database']['connected']) {
        $health['status'] = 'error';
        http_response_code(503);
    }
    
    echo json_encode($health, JSON_PRETTY_PRINT);
    exit;
}

// Auto-include config if available
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

?>