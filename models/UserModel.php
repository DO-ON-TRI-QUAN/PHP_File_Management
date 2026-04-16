<?php

// ============================================================
// Handles all database interactions for the users table.
// ============================================================

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Inserts a new user into the database.
    // Returns true on success, false if email already exists.
    public function create_user($username, $email, $hashed_password) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Fetches a single user row by email address.
    // Returns the user array, or false if not found.
    public function get_user_by_email($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Fetches a single user row by ID.
    // Returns the user array, or false if not found.
    public function get_user_by_id($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}