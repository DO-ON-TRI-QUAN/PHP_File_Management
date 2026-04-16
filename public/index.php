<?php
// ============================================================
// Main entry point. Handles session protection and routes
// requests to the appropriate view.
// ============================================================

session_start();
require_once '../config/database.php';
require_once '../utils/flash_messages.php';

$page = $_GET['page'] ?? 'home';

// Redirect unauthenticated users to login
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
        require_once '../utils/helpers.php';
        // Fetch current user's files to pass to the view
        $stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require '../views/home.php';
        break;

    case 'share':
        require_once '../models/FileModel.php';
        $file_model = new FileModel($pdo);
        $token      = $_GET['token'] ?? '';

        if (!$token) {
            set_flash('error', 'Invalid share link.');
            header("Location: index.php?page=login");
            exit;
        }

        $file = $file_model->get_file_by_token($token);

        if (!$file) {
            set_flash('error', 'File not found or link has been disabled.');
            header("Location: index.php?page=login");
            exit;
        }

        $file_path = '../' . $file['file_path'];

        if (!file_exists($file_path)) {
            set_flash('error', 'File missing from server.');
            header("Location: index.php?page=login");
            exit;
        }

        // Serve file as download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;


    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}