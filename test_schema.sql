-- Test file to verify the database schema works correctly
-- This file contains a minimal version of the schema for testing

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS renewable_cloth_test;
CREATE DATABASE renewable_cloth_test;
USE renewable_cloth_test;

-- Users table for authentication and user management
CREATE TABLE users (
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
);

-- Items table for clothing items
CREATE TABLE items (
    item_id CHAR(36) PRIMARY KEY,
    owner_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('Shirt', 'Pants', 'Jacket', 'Dress', 'Shoes', 'Accessories', 'Other') NOT NULL,
    size ENUM('XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size') NOT NULL,
    `condition` ENUM('New', 'Like New', 'Good', 'Fair', 'Used') NOT NULL,
    tags JSON,
    images JSON,
    status ENUM('Pending', 'Available', 'Swapped', 'Redeemed') DEFAULT 'Pending',
    point_value INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_owner_id (owner_id),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_point_value (point_value)
);

-- Insert test data
INSERT INTO users (user_id, name, email, password_hash, role, points) VALUES
(UUID(), 'Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 100);

-- Test successful creation
SELECT 'Schema created successfully!' as status; 

-- Admin user
INSERT INTO users (user_id, name, email, password_hash, role, points)
VALUES (
    'admin-uuid-001',
    'Admin User',
    'admin@renewablecloth.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin',
    1000
);

-- Regular user 1
INSERT INTO users (user_id, name, email, password_hash, role, points)
VALUES (
    'user-uuid-001',
    'John Doe',
    'john@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'user',
    500
);

-- Regular user 2
INSERT INTO users (user_id, name, email, password_hash, role, points)
VALUES (
    'user-uuid-002',
    'Jane Smith',
    'jane@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'user',
    750
); 