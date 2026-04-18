<?php

// ============================================================
// Database config example
// ============================================================

// Placeholder credentials
$host    = 'localhost';
$db      = 'file_manager';
$user    = 'your_db_username';
$pass    = 'your_db_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}