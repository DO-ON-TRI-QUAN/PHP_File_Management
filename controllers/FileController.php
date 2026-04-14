<?php
// ============================================================
// Handles file upload, download, and delete actions.
// All actions require an active user session.
// ============================================================

session_start();
require_once '../config/database.php';
require_once '../utils/flash_messages.php';

$action = $_GET['action'] ?? '';

// --------------------------------------------------------
// UPLOAD
// --------------------------------------------------------
if ($action === 'upload') {
    if (!isset($_SESSION['user_id'])) {
        set_flash('error', 'Unauthorized. Please log in.');
        header("Location: ../public/index.php?page=login");
        exit;
    }

    if (!isset($_FILES['file'])) {
        set_flash('error', 'No file uploaded.');
        header("Location: ../public/index.php");
        exit;
    }

    $file = $_FILES['file'];

    // Check for upload errors reported by PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        set_flash('error', 'Upload failed. Please try again.');
        header("Location: ../public/index.php");
        exit;
    }

    $original_name = $file['name'];
    $tmp_name      = $file['tmp_name'];
    $file_size     = $file['size'];

    // Limit file size to 5MB
    if ($file_size > 5 * 1024 * 1024) {
        set_flash('error', 'File too large. Maximum size is 5MB.');
        header("Location: ../public/index.php");
        exit;
    }

    // Allowed MIME types
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];

    // Use finfo to detect the real MIME type server-side.
    // finfo reads the actual file so it prevents spoofing on client-side.
    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        set_flash('error', 'Invalid file type. Allowed: JPEG, PNG, PDF.');
        header("Location: ../public/index.php");
        exit;
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
        set_flash('success', 'File uploaded successfully.');
        header("Location: ../public/index.php");
        exit;
    } else {
        set_flash('error', 'Failed to move uploaded file.');
        header("Location: ../public/index.php");
        exit;    
    }
}

// --------------------------------------------------------
// DOWNLOAD
// --------------------------------------------------------
if ($action === 'download') {
    if (!isset($_SESSION['user_id'])) {
        set_flash('error', 'Unauthorized. Please log in.');
        header("Location: ../public/index.php?page=login");
        exit;
    }

    $file_id = $_GET['id'];

    // Fetch file and verify that it belongs to the current user
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $_SESSION['user_id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        set_flash('error', 'File not found or access denied.');
        header("Location: ../public/index.php");
        exit;
    }

    $file_path = '../' . $file['file_path'];

    if (!file_exists($file_path)) {
        set_flash('error', 'File missing from server.');
        header("Location: ../public/index.php");
        exit;    
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
        set_flash('error', 'Unauthorized. Please log in.');
        header("Location: ../public/index.php?page=login");
        exit;
    }

    $file_id = $_GET['id'];

    // Fetch file and verify ownership before deleting
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $_SESSION['user_id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        set_flash('error', 'File not found or access denied.');
        header("Location: ../public/index.php");
        exit;
    }

    $file_path = '../' . $file['file_path'];

    // Delete physical file from disk
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete file record from database
    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$file_id]);

    set_flash('success', 'File deleted successfully.');
    header("Location: ../public/index.php");
    exit;
}

// --------------------------------------------------------
// RENAME
// --------------------------------------------------------
if ($action === 'rename') {
    if (!isset($_SESSION['user_id'])) {
        set_flash('error', 'Unauthorized. Please log in.');
        header("Location: ../public/index.php?page=login");
        exit;
    }

    $file_id  = $_POST['id'];
    $new_name = trim($_POST['new_name']);

    if (!$new_name) {
        set_flash('error', 'New filename is required.');
        header("Location: ../public/index.php");
        exit;    
    }

    // Verify file ownership
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $_SESSION['user_id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        set_flash('error', 'File not found or access denied.');
        header("Location: ../public/index.php");
        exit;
    }

    // Preserve the original file extension
    $extension     = pathinfo($file['original_name'], PATHINFO_EXTENSION);
    $new_name      = pathinfo($new_name, PATHINFO_FILENAME); // Strip any extension typed by user
    $new_name      = rtrim($new_name, '.'); // Remove any trailing periods
    $full_new_name = $new_name . '.' . $extension;

    // Update display name only, stored_name and file on disk remain unchanged
    $stmt = $pdo->prepare("UPDATE files SET original_name = ? WHERE id = ?");
    $stmt->execute([$full_new_name, $file_id]);

    set_flash('success', 'File renamed successfully.');
    header("Location: ../public/index.php");
    exit;
}