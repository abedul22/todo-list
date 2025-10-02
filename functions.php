<?php
// Get all tasks with optional filters
function getTasks($search = '', $status = '', $priority = '') {
    try {
        $pdo = getDatabaseConnection();

        $sql = "SELECT * FROM tasks WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($priority)) {
            $sql .= " AND priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting tasks: " . $e->getMessage());
        return [];
    }
}

// Get task by ID
function getTaskById($id) {
    try {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting task by ID: " . $e->getMessage());
        return null;
    }
}

// Add new task
function addTask($data) {
    try {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, deadline, priority, status) VALUES (?, ?, ?, ?, ?)");

        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['deadline'],
            $data['priority'],
            $data['status']
        ]);
    } catch (Exception $e) {
        error_log("Error adding task: " . $e->getMessage());
        return false;
    }
}

// Update task
function updateTask($id, $data) {
    try {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, deadline = ?, priority = ?, status = ? WHERE id = ?");

        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['deadline'],
            $data['priority'],
            $data['status'],
            $id
        ]);
    } catch (Exception $e) {
        error_log("Error updating task: " . $e->getMessage());
        return false;
    }
}

// Delete task
function deleteTask($id) {
    try {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Error deleting task: " . $e->getMessage());
        return false;
    }
}

// Get task statistics
function getTaskStats() {
    try {
        $pdo = getDatabaseConnection();

        $stats = [
            'total' => 0,
            'pending' => 0,
            'progress' => 0,
            'completed' => 0
        ];

        // Get total count
        $stmt = $pdo->query("SELECT COUNT(*) FROM tasks");
        $stats['total'] = $stmt->fetchColumn();

        // Get counts by status
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['status']] = $row['count'];
        }

        return $stats;
    } catch (Exception $e) {
        error_log("Error getting task stats: " . $e->getMessage());
        return ['total' => 0, 'pending' => 0, 'progress' => 0, 'completed' => 0];
    }
}

// Format date for display
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Get priority text
function getPriorityText($priority) {
    $priorities = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi'
    ];
    
    return $priorities[$priority] ?? $priority;
}

// Get status text
function getStatusText($status) {
    $statuses = [
        'pending' => 'Pending',
        'progress' => 'Dalam Progress',
        'completed' => 'Selesai'
    ];
    
    return $statuses[$status] ?? $status;
}

// Validate task data
function validateTaskData($data) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors[] = 'Judul tugas harus diisi';
    }
    
    if (empty($data['deadline'])) {
        $errors[] = 'Deadline harus diisi';
    }
    
    if (!in_array($data['priority'], ['low', 'medium', 'high'])) {
        $errors[] = 'Prioritas tidak valid';
    }
    
    if (!in_array($data['status'], ['pending', 'progress', 'completed'])) {
        $errors[] = 'Status tidak valid';
    }
    
    return $errors;
}

// Sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>