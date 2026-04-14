<?php
// ============================================================
// Handles user registration and login.
// ============================================================

session_start();
require_once '../config/database.php';
require_once '../utils/flash_messages.php';

$action = $_GET['action'] ?? '';

// --------------------------------------------------------
// REGISTER
// --------------------------------------------------------
if ($action === 'register') {
    $username      = trim($_POST['username']);
    $email         = trim($_POST['email']);
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!$username || !$email || !$hashed_password) {
        set_flash('error', 'All fields are required.');
        header("Location: ../public/index.php?page=register");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        set_flash('success', 'Account created successfully. Please log in.');
        header("Location: ../public/index.php?page=login");
        exit;
    } catch (PDOException $e) {
        set_flash('error', 'Email already exists.');
        header("Location: ../public/index.php?page=register");    
        exit;
    }
}

// --------------------------------------------------------
// LOGIN
// --------------------------------------------------------
if ($action === 'login') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password and start session
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        set_flash('success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
        header("Location: ../public/index.php");
        exit;
    } else {
        set_flash('error', 'Invalid email or password.');
        header("Location: ../public/index.php?page=login");
    }
}