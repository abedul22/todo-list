<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Include required files
require_once 'operations/includes/database.php';
require_once 'operations/includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_task':
            $id = $_GET['id'] ?? 0;
            $task = getTaskById($id);
            
            if ($task) {
                echo json_encode(['success' => true, 'data' => $task]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tugas tidak ditemukan']);
            }
            break;
            
        case 'add_task':
            $data = [
                'title' => sanitizeInput($_POST['title'] ?? ''),
                'deadline' => sanitizeInput($_POST['deadline'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'priority' => $_POST['priority'] ?? 'medium',
                'status' => $_POST['status'] ?? 'pending'
            ];
            
            // Validate data
            $errors = validateTaskData($data);
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            try {
                if (addTask($data)) {
                    echo json_encode(['success' => true, 'message' => 'Tugas berhasil ditambahkan']);
                } else {
                    error_log('Failed to add task: ' . json_encode($data));
                    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan tugas']);
                }
            } catch (Exception $e) {
                error_log('Exception when adding task: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error saat menambahkan tugas: ' . $e->getMessage()]);
            }
            break;
            
        case 'update_task':
            $id = $_POST['id'] ?? 0;
            $data = [
                'title' => sanitizeInput($_POST['title'] ?? ''),
                'deadline' => sanitizeInput($_POST['deadline'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'priority' => $_POST['priority'] ?? 'medium',
                'status' => $_POST['status'] ?? 'pending'
            ];
            
            // Validate data
            $errors = validateTaskData($data);
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            if (updateTask($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Tugas berhasil diupdate']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengupdate tugas']);
            }
            break;
            
        case 'delete_task':
            $id = $_POST['id'] ?? 0;
            
            if ($id && deleteTask($id)) {
                echo json_encode(['success' => true, 'message' => 'Tugas berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus tugas']);
            }
            break;
            
        case 'get_stats':
            $stats = getTaskStats();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'search_tasks':
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $priority = $_GET['priority'] ?? '';
            
            $tasks = getTasks($search, $status, $priority);
            echo json_encode(['success' => true, 'data' => $tasks]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>