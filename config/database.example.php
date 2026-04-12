<?php

// ============================================================
// Database config example
// ============================================================

// Copy this file and rename to database.php, then fill in actual info before running.
$host    = 'localhost';
$db      = 'file_manager';
$user    = 'username';
$pass    = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}