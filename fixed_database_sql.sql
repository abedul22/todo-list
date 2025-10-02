-- =============================================
-- Admin To-Do List Database Setup Script (Fixed Version)
-- Compatible with MySQL 5.7+ and MariaDB 10.2+
-- =============================================

-- Create database
CREATE DATABASE IF NOT EXISTS `todo_admin` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `todo_admin`;

-- =============================================
-- Drop existing objects (if any)
-- =============================================
SET FOREIGN_KEY_CHECKS = 0;
DROP PROCEDURE IF EXISTS GetTaskStats;
DROP PROCEDURE IF EXISTS LogActivity;
DROP VIEW IF EXISTS `task_summary`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `tasks`;
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- Create tasks table
-- =============================================
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `deadline` date NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','progress','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_deadline` (`deadline`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure created_at and updated_at columns exist
ALTER TABLE `tasks` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `tasks` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- =============================================
-- Create admin users table
-- =============================================
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `failed_login_attempts` int(3) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_status` (`status`),
  KEY `idx_last_login` (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Create activity logs table
-- =============================================
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint after table creation (safer approach)
ALTER TABLE `activity_logs` 
ADD CONSTRAINT `fk_activity_logs_user` 
FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

-- =============================================
-- Insert default admin user
-- Password: admin (hashed with bcrypt)
-- =============================================
INSERT INTO `admin_users` (`username`, `password`, `email`, `full_name`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@todolist.com', 'System Administrator', 'active');

-- =============================================
-- Insert sample tasks
-- =============================================
INSERT INTO `tasks` (`title`, `description`, `deadline`, `priority`, `status`) VALUES
('Desain UI Dashboard', 'Membuat mockup dan prototype untuk dashboard admin menggunakan Figma atau Adobe XD', '2024-12-25', 'high', 'progress'),
('Implementasi Database', 'Setup database MySQL dan membuat semua tabel yang diperlukan untuk sistem manajemen tugas', '2024-12-30', 'high', 'completed'),
('Testing Fitur Login', 'Melakukan pengujian komprehensif pada sistem login termasuk validasi keamanan', '2024-12-20', 'medium', 'completed'),
('Dokumentasi API', 'Membuat dokumentasi lengkap untuk semua endpoint API menggunakan Postman atau Swagger', '2025-01-05', 'low', 'pending'),
('Optimasi Performance', 'Mengoptimalkan query database dan memperbaiki loading time aplikasi', '2025-01-10', 'medium', 'progress'),
('Setup SSL Certificate', 'Mengonfigurasi SSL certificate untuk keamanan website', '2025-01-15', 'high', 'pending'),
('Backup System', 'Implementasi sistem backup otomatis untuk database dan file', '2025-01-20', 'medium', 'pending'),
('User Manual', 'Pembuatan manual penggunaan untuk administrator sistem', '2025-01-25', 'low', 'pending'),
('Security Audit', 'Melakukan audit keamanan menyeluruh terhadap aplikasi', '2025-02-01', 'high', 'pending'),
('Mobile Responsive Test', 'Testing responsivitas aplikasi di berbagai device dan browser', '2024-12-28', 'medium', 'progress');

-- =============================================
-- Add fulltext index for search functionality
-- =============================================
ALTER TABLE `tasks` ADD FULLTEXT(`title`, `description`);

-- =============================================
-- Create view for task summary (simplified)
-- =============================================
CREATE VIEW `task_summary` AS
SELECT 
    DATE(`created_at`) as `date`,
    COUNT(*) as `total_tasks`,
    SUM(CASE WHEN `status` = 'pending' THEN 1 ELSE 0 END) as `pending_tasks`,
    SUM(CASE WHEN `status` = 'progress' THEN 1 ELSE 0 END) as `progress_tasks`,
    SUM(CASE WHEN `status` = 'completed' THEN 1 ELSE 0 END) as `completed_tasks`,
    SUM(CASE WHEN `priority` = 'high' THEN 1 ELSE 0 END) as `high_priority_tasks`
FROM `tasks` 
GROUP BY DATE(`created_at`)
ORDER BY `date` DESC;

-- =============================================
-- Drop existing procedures if they exist
-- =============================================
DROP PROCEDURE IF EXISTS GetTaskStats;
DROP PROCEDURE IF EXISTS LogActivity;

-- =============================================
-- Create stored procedures (simplified syntax)
-- =============================================
DELIMITER $$

CREATE PROCEDURE GetTaskStats()
BEGIN
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'progress' THEN 1 ELSE 0 END) as progress,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_priority,
        SUM(CASE WHEN deadline < CURDATE() AND status != 'completed' THEN 1 ELSE 0 END) as overdue
    FROM tasks;
END$$

CREATE PROCEDURE LogActivity(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent)
    VALUES (p_user_id, p_action, p_description, p_ip_address, p_user_agent);
END$$

DELIMITER ;

-- =============================================
-- Insert sample activity logs
-- =============================================
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`) VALUES
(1, 'login', 'Admin user logged in', '127.0.0.1'),
(1, 'create_task', 'Created new task: Desain UI Dashboard', '127.0.0.1'),
(1, 'update_task', 'Updated task status to completed', '127.0.0.1');

-- =============================================
-- Test the setup
-- =============================================
SELECT 'Database setup completed successfully!' as status;

SELECT 
    TABLE_NAME as 'Table', 
    TABLE_ROWS as 'Rows'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'todo_admin'
ORDER BY TABLE_NAME;

