<?php
// ============================================================
// Handles user registration and login.
// ============================================================

session_start();
require_once '../config/database.php';

$action = $_GET['action'] ?? '';

// --------------------------------------------------------
// REGISTER
// --------------------------------------------------------
if ($action === 'register') {
    $username      = trim($_POST['username']);
    $email         = trim($_POST['email']);
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!$username || !$email || !$hashed_password) {
        die("All fields are required.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        header("Location: ../public/index.php?page=login");
        exit;
    } catch (PDOException $e) {
        die("Error: Email might already exist.");
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
        header("Location: ../public/index.php");
        exit;
    } else {
        die("Invalid email or password.");
    }
}