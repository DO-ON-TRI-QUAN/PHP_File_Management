<?php
// ============================================================
// Main entry point. Handles session protection and routes...
// ...requests to the appropriate view.
// ============================================================

session_start();
require_once '../config/database.php';

$page = $_GET['page'] ?? 'home';

// Redirect unauthenticated users to login...
// ...except for login and register pages
if (!isset($_SESSION['user_id']) && !in_array($page, ['login', 'register'])) {
    header("Location: index.php?page=login");
    exit;
}

switch ($page) {

    case 'login':
        require '../views/login.php';
        break;

    case 'register':
        require '../views/register.php';
        break;

    case 'home':
        // Fetch current user's files to pass to the view
        $stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require '../views/home.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}