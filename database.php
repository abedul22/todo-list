<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'todo_admin');

// Create database connection function
function getDatabaseConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    return $pdo;
}

function insertSampleData() {
    try {
        $pdo = getDatabaseConnection();

        $sampleTasks = [
            [
                'title' => 'Desain UI Dashboard',
                'description' => 'Membuat mockup dan prototype untuk dashboard admin',
                'deadline' => '2024-01-15',
                'priority' => 'high',
                'status' => 'progress'
            ],
            [
                'title' => 'Implementasi Database',
                'description' => 'Setup database MySQL dan tabel yang diperlukan',
                'deadline' => '2024-01-20',
                'priority' => 'high',
                'status' => 'pending'
            ],
            [
                'title' => 'Testing Fitur Login',
                'description' => 'Melakukan pengujian komprehensif pada sistem login',
                'deadline' => '2024-01-10',
                'priority' => 'medium',
                'status' => 'completed'
            ],
            [
                'title' => 'Dokumentasi API',
                'description' => 'Membuat dokumentasi lengkap untuk semua endpoint API',
                'deadline' => '2024-01-25',
                'priority' => 'low',
                'status' => 'pending'
            ],
            [
                'title' => 'Optimasi Performance',
                'description' => 'Mengoptimalkan query database dan loading time',
                'deadline' => '2024-01-30',
                'priority' => 'medium',
                'status' => 'progress'
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, deadline, priority, status) VALUES (?, ?, ?, ?, ?)");

        foreach ($sampleTasks as $task) {
            $stmt->execute([
                $task['title'],
                $task['description'],
                $task['deadline'],
                $task['priority'],
                $task['status']
            ]);
        }
    } catch (Exception $e) {
        error_log("Error inserting sample data: " . $e->getMessage());
    }
}

// Create tables if they don't exist
function createTables() {
    try {
        $pdo = getDatabaseConnection();

        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            deadline DATE NOT NULL,
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            status ENUM('pending', 'progress', 'completed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $pdo->exec($sql);

        // Insert sample data if table is empty
        $count = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
        if ($count == 0) {
            insertSampleData();
        }
    } catch (Exception $e) {
        error_log("Error creating tables: " . $e->getMessage());
    }
}



// Initialize database
createTables();
?>