<?php
// ============================================================
// Main entry point. Handles session protection and routes
// requests to the appropriate view.
// ============================================================

session_start();
require_once '../config/database.php';
require_once '../utils/flash_messages.php';

// Pages accessible without login
const PUBLIC_PAGES   = ['login', 'register', 'share'];

const FILES_PER_PAGE = 10;

$page = $_GET['page'] ?? 'home';

// Redirect unauthenticated users to login, except for public pages
if (!isset($_SESSION['user_id']) && !in_array($page, PUBLIC_PAGES)) {
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
        require_once '../utils/helpers.php';
        require_once '../models/FileModel.php';

        $file_model   = new FileModel($pdo);
        $current_page = max(1, (int) ($_GET['p'] ?? 1)); // Minimum page is 1
        $offset       = ($current_page - 1) * FILES_PER_PAGE;
        $total_files  = $file_model->count_files_by_user($_SESSION['user_id']);
        $total_pages  = (int) ceil($total_files / FILES_PER_PAGE);
        $files        = $file_model->get_files_by_user($_SESSION['user_id'], FILES_PER_PAGE, $offset);

        require '../views/home.php';
        break;

    case 'share':
        // Public file download via share token, no login required
        require '../controllers/FileController.php';
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