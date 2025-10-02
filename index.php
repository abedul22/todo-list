<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle login
if ($_POST && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Username atau password salah!';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Include database connection and functions
require_once __DIR__ . '/operations/includes/database.php';
require_once __DIR__ . '/operations/includes/functions.php';

// Get tasks and statistics
$tasks = getTasks();
$stats = getTaskStats();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin To-Do List</title>
    <link rel="stylesheet" href="operations/assets/css/style.css">
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- Login Page -->
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Admin To-Do List</h1>
                    <p>Masuk untuk mengelola tugas Anda</p>
                </div>
                
                <?php if (isset($loginError)): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($loginError) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn-primary">Masuk</button>
                </form>
                <div class="demo-info">
                    Demo: admin / admin
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Dashboard -->
        <div class="dashboard-container">
            <div class="main-wrapper">
                <?php include __DIR__ . '/operations/includes/sidebar.php'; ?>
                <div class="content-wrapper">
                    <?php include __DIR__ . '/operations/includes/topbar.php'; ?>
                    <main class="main-content">
                        <div id="dashboardContent" class="page-content">
                            <?php include __DIR__ . '/operations/page/dashboard.php'; ?>
                        </div>
                        <div id="tasksContent" class="page-content hidden">
                            <?php include __DIR__ . '/operations/page/tasks.php'; ?>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/operations/includes/modal.php'; ?>
    <?php endif; ?>

    <script src="operations/assets/js/script.js"></script>
</body>
</html>