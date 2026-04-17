<?php

// ============================================================
// Handles all database interactions for the files table.
// ============================================================

class FileModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetches all files belonging to a user.
    // Returns an array of file rows, or empty array if none.
    public function get_files_by_user($user_id, $limit, $offset) {
        $stmt = $this->pdo->prepare("SELECT * FROM files WHERE user_id = ? LIMIT ? OFFSET ?");

        // By default PDO binds all values as strings, 
        // which MySQL doesn't accept.
        // bindValue() with PDO::PARAM_INT will tell PDO to treat these as integers.
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit,   PDO::PARAM_INT);
        $stmt->bindValue(3, $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetches a single file by ID and verifies ownership.
    // Returns the file array, or false if not found.
    public function get_file_by_id($file_id, $user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
        $stmt->execute([$file_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetches a public file by its share token.
    // Returns the file array or false if not found.
    // --------------------------------------------------------
    public function get_file_by_token($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM files WHERE share_token = ? AND visibility = 'public'");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Returns the total number of files for a user.
    public function count_files_by_user($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM files WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }

    // Inserts a new file record into the database.
    // Returns true on success, false on failure.
    public function create_file($user_id, $original_name, $stored_name, $db_path, $file_size) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO files (user_id, original_name, stored_name, file_path, file_size)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $original_name, $stored_name, $db_path, $file_size]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Updates the display name of a file.
    // Returns true on success, false on failure.
    public function rename_file($file_id, $new_name) {
        try {
            $stmt = $this->pdo->prepare("UPDATE files SET original_name = ? WHERE id = ?");
            $stmt->execute([$new_name, $file_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Deletes a file record from the database.
    // Returns true on success, false on failure.
    public function delete_file($file_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM files WHERE id = ?");
            $stmt->execute([$file_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Updates the visibility and share token of a file.
    // If set visibility to private, pass share_token as null.
    // Returns true on success, false on failure.
    public function set_visibility($file_id, $visibility, $share_token = null) {
        try {
            $stmt = $this->pdo->prepare("UPDATE files SET visibility = ?, share_token = ? WHERE id = ?");
            $stmt->execute([$visibility, $share_token, $file_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}