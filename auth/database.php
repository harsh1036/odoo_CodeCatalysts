<?php
// Database configuration
$host = "localhost";
$dbname = "renewable_cloth";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to create database and tables if they don't exist
function createDatabaseAndTables($pdo) {
    try {
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS renewable_cloth");
        $pdo->exec("USE renewable_cloth");
        
        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            user_id CHAR(36) PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            points INT DEFAULT 0,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_role (role),
            INDEX idx_created_at (created_at)
        )";
        $pdo->exec($sql);
        
        return true;
    } catch(PDOException $e) {
        die("Error creating database/tables: " . $e->getMessage());
    }
}

// Initialize database and tables
createDatabaseAndTables($pdo);
?> 