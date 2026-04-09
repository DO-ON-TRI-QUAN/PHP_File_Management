<?php
// ============================================================
// Handles file upload, download, and delete actions.
// All actions require an active user session.
// ============================================================

session_start();
require_once '../config/database.php';

$action = $_GET['action'] ?? '';

// --------------------------------------------------------
// UPLOAD
// --------------------------------------------------------
if ($action === 'upload') {
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    if (!isset($_FILES['file'])) {
        die("No file uploaded");
    }

    $file = $_FILES['file'];

    // Check for upload errors reported by PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Upload failed");
    }

    $original_name = $file['name'];
    $tmp_name      = $file['tmp_name'];
    $file_size     = $file['size'];

    // Limit file size to 5MB
    if ($file_size > 5 * 1024 * 1024) {
        die("File too large. Maximum size is 5MB.");
    }

    // Allowed MIME types
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file['type'], $allowed_types)) {
        die("Invalid file type. Allowed: JPEG, PNG, PDF.");
    }

    // Generate a unique filename to avoid collisions in the uploads folder
    $stored_name = uniqid() . '_' . basename($original_name);
    $upload_dir  = '../uploads/';
    $file_path   = $upload_dir . $stored_name; // Physical path on disk
    $db_path     = 'uploads/' . $stored_name;  // Relative path stored in DB

    // Move uploaded file from PHP temp folder to uploads directory
    if (move_uploaded_file($tmp_name, $file_path)) {
        $stmt = $pdo->prepare("
            INSERT INTO files (user_id, original_name, stored_name, file_path, file_size)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $original_name,
            $stored_name,
            $db_path,
            $file_size
        ]);
        header("Location: ../public/index.php");
        exit;
    } else {
        die("Failed to move uploaded file.");
    }
}

// --------------------------------------------------------
// DOWNLOAD
// --------------------------------------------------------
if ($action === 'download') {
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    $file_id = $_GET['id'];

    // Fetch file and verify it belongs to the current user
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $_SESSION['user_id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        die("File not found or access denied");
    }

    $file_path = '../' . $file['file_path'];

    if (!file_exists($file_path)) {
        die("File missing from server");
    }

    // Send file to browser as a download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
}

// --------------------------------------------------------
// DELETE
// --------------------------------------------------------
if ($action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    $file_id = $_GET['id'];

    // Fetch file and verify ownership before deleting
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $_SESSION['user_id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        die("File not found or access denied");
    }

    $file_path = '../' . $file['file_path'];

    // Delete physical file from disk
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete file record from database
    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$file_id]);

    header("Location: ../public/index.php");
    exit;
}