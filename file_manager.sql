-- ============================================================
-- FILE MANAGER DATABASE SCHEMA
-- ============================================================
-- Run this file once to set up the database and tables.
-- Import via phpMyAdmin or run: mysql -u root -p < file_manager.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS file_manager;
USE file_manager;

-- ============================================================
-- TABLE: users
-- ============================================================
-- Stores registered user accounts.
-- email is unique — used as the login identifier.
-- password stores a bcrypt hash, never plain text.
-- ============================================================

CREATE TABLE users (
    id         INT          AUTO_INCREMENT PRIMARY KEY, -- Unique user ID
    username   VARCHAR(50)  NOT NULL,                  -- Display name
    email      VARCHAR(100) NOT NULL UNIQUE,            -- Login identifier, must be unique
    password   VARCHAR(255) NOT NULL,                  -- bcrypt hashed password
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP  -- Account creation time
);

-- ============================================================
-- TABLE: files
-- ============================================================
-- Stores metadata for uploaded files.
-- The actual file is stored on disk in the uploads/ folder.
-- original_name — the filename as uploaded by the user
-- stored_name   — the unique filename used on disk (uniqid prefix)
-- file_path     — relative path stored in DB e.g. uploads/abc123_file.pdf
-- file_size     — size in bytes
-- visibility    — 'private' (owner only) or 'public' (anyone with link)
-- ============================================================

CREATE TABLE files (
    id            INT          AUTO_INCREMENT PRIMARY KEY, -- Unique file ID
    user_id       INT          NOT NULL,                   -- Owner — references users.id
    original_name VARCHAR(255) NOT NULL,                   -- Original filename from the user
    stored_name   VARCHAR(255) NOT NULL,                   -- Unique filename stored on disk
    file_path     VARCHAR(255) NOT NULL,                   -- Relative path for serving the file
    file_size     INT          NOT NULL,                   -- File size in bytes
    visibility    ENUM('private', 'public') DEFAULT 'private', -- Access control
    uploaded_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP, -- Upload timestamp

    -- If a user is deleted, all their files are automatically removed
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);