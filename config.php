<?php
/**
 * Configuration file for Admin To-Do List System
 * 
 * @author Your Name
 * @version 1.0
 */

// Prevent direct access
if (!defined('APP_ACCESS')) {
    die('Direct access not permitted');
}

// Application Settings
define('APP_NAME', 'Admin To-Do List');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Your Name');
define('APP_URL', 'http://localhost/todo-admin');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'todo_admin');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_NAME', 'todo_admin_session');
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_SECURE', false); // Set to true if using HTTPS
define('SESSION_HTTPONLY', true);

// Security Configuration
define('CSRF_TOKEN_NAME', '_token');
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOGIN_LOCKOUT_TIME', 300); // 5 minutes

// Application Features
define('ENABLE_REGISTRATION', false);
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_FILE_UPLOADS', false);
define('ENABLE_API', false);

// File Upload Settings (if enabled)
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', 'uploads/');

// Email Configuration (if enabled)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@example.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@example.com');
define('MAIL_FROM_NAME', APP_NAME);

// Pagination Settings
define('TASKS_PER_PAGE', 25);
define('LOGS_PER_PAGE', 50);

// Date and Time Settings
define('DEFAULT_TIMEZONE', 'Asia/Jakarta');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// Cache Settings
define('ENABLE_CACHE', false);
define('CACHE_LIFETIME', 3600);
define('CACHE_PATH', 'cache/');

// Debug Settings
define('DEBUG_MODE', true); // Set to false in production
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', 'logs/error.log');

// API Settings (if enabled)
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per hour
define('API_TOKEN_LIFETIME', 7200); // 2 hours

// Theme Settings
define('DEFAULT_THEME', 'blue');
define('AVAILABLE_THEMES', ['blue', 'dark', 'green', 'purple']);

// Language Settings
define('DEFAULT_LANGUAGE', 'id');
define('AVAILABLE_LANGUAGES', ['id', 'en']);

// Backup Settings
define('ENABLE_AUTO_BACKUP', false);
define('BACKUP_FREQUENCY', 'daily'); // daily, weekly, monthly
define('BACKUP_PATH', 'backups/');
define('BACKUP_RETENTION_DAYS', 30);

// Notification Settings
define('ENABLE_PUSH_NOTIFICATIONS', false);
define('NOTIFICATION_SOUND', true);
define('DESKTOP_NOTIFICATIONS', false);

// Advanced Settings
define('ENABLE_TWO_FACTOR', false);
define('ENABLE_AUDIT_LOG', true);
define('ENABLE_MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'System sedang dalam pemeliharaan. Silakan coba lagi nanti.');

// Performance Settings
define('ENABLE_COMPRESSION', true);
define('ENABLE_MINIFICATION', false); // Set to true in production
define('OPTIMIZE_IMAGES', false);

// Integration Settings
define('GOOGLE_ANALYTICS_ID', '');
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Custom Settings
define('COMPANY_NAME', 'Your Company');
define('COMPANY_ADDRESS', 'Your Address');
define('COMPANY_PHONE', '+62-xxx-xxxx-xxxx');
define('COMPANY_EMAIL', 'info@yourcompany.com');
define('COMPANY_WEBSITE', 'https://yourcompany.com');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Set session parameters
ini_set('session.name', SESSION_NAME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_httponly', SESSION_HTTPONLY);
ini_set('session.cookie_secure', SESSION_SECURE);

// Memory and execution limits
ini_set('memory_limit', '128M');
ini_set('max_execution_time', '30');

// File upload limits
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

/**
 * Environment-specific configuration
 * Uncomment the appropriate section based on your environment
 */

// Development Environment
if (DEBUG_MODE) {
    define('ENV', 'development');
    // Additional development settings
}

// Production Environment
/*
if (!DEBUG_MODE) {
    define('ENV', 'production');
    // Override settings for production
    define('ENABLE_CACHE', true);
    define('ENABLE_MINIFICATION', true);
    define('LOG_ERRORS', true);
}
*/

// Custom functions for configuration
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

function isFeatureEnabled($feature) {
    return getConfig($feature, false) === true;
}

function isDevelopment() {
    return getConfig('DEBUG_MODE', false) === true;
}

function isProduction() {
    return !isDevelopment();
}

// Autoload configuration validation
function validateConfig() {
    $required = [
        'DB_HOST', 'DB_USER', 'DB_NAME'
    ];
    
    foreach ($required as $config) {
        if (!defined($config) || empty(constant($config))) {
            die("Configuration error: $config is required");
        }
    }
}

// Initialize configuration
validateConfig();
?>