-- Renewable Cloth Website Database Schema
-- This file contains all the necessary tables for the sustainable fashion platform

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS renewable_cloth;
CREATE DATABASE renewable_cloth;
USE renewable_cloth;

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

-- Swap requests table for item exchanges
CREATE TABLE swap_requests (
    request_id CHAR(36) PRIMARY KEY,
    from_user_id CHAR(36) NOT NULL,
    to_user_id CHAR(36) NOT NULL,
    offered_item_id CHAR(36) NOT NULL,
    requested_item_id CHAR(36) NOT NULL,
    status ENUM('Pending', 'Accepted', 'Rejected', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (offered_item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (requested_item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    INDEX idx_from_user_id (from_user_id),
    INDEX idx_to_user_id (to_user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Redemptions table for point-based item redemptions
CREATE TABLE redemptions (
    redemption_id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    item_id CHAR(36) NOT NULL,
    points_used INT NOT NULL,
    status ENUM('Pending Shipment', 'Shipped', 'Delivered', 'Completed', 'Cancelled') DEFAULT 'Pending Shipment',
    shipping_address TEXT,
    tracking_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_item_id (item_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Admin logs table for tracking administrative actions
CREATE TABLE admin_logs (
    log_id CHAR(36) PRIMARY KEY,
    admin_id CHAR(36) NOT NULL,
    action_type ENUM('Approved', 'Rejected', 'Deleted', 'Modified', 'Suspended') NOT NULL,
    item_id CHAR(36),
    user_id CHAR(36),
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at)
);

-- User profiles table for additional user information
CREATE TABLE user_profiles (
    profile_id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL UNIQUE,
    bio TEXT,
    location VARCHAR(255),
    phone VARCHAR(20),
    avatar_url VARCHAR(500),
    preferences JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Item reviews table for feedback on items
CREATE TABLE item_reviews (
    review_id CHAR(36) PRIMARY KEY,
    item_id CHAR(36) NOT NULL,
    reviewer_id CHAR(36) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_item_reviewer (item_id, reviewer_id),
    INDEX idx_item_id (item_id),
    INDEX idx_reviewer_id (reviewer_id),
    INDEX idx_rating (rating)
);

-- Notifications table for user notifications
CREATE TABLE notifications (
    notification_id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    `type` ENUM('swap_request', 'swap_accepted', 'swap_rejected', 'points_earned', 'item_approved', 'item_rejected', 'redemption_status') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_id CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- Points transactions table for tracking point changes
CREATE TABLE point_transactions (
    transaction_id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    amount INT NOT NULL,
    `type` ENUM('earned', 'spent', 'refunded', 'bonus', 'penalty') NOT NULL,
    description VARCHAR(255),
    related_item_id CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (related_item_id) REFERENCES items(item_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
);

-- Categories table for item categorization
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, description, icon) VALUES
('Shirt', 'T-shirts, polo shirts, button-down shirts', 'fas fa-tshirt'),
('Pants', 'Jeans, trousers, shorts, leggings', 'fas fa-socks'),
('Jacket', 'Coats, jackets, blazers, hoodies', 'fas fa-mitten'),
('Dress', 'Dresses, skirts, formal wear', 'fas fa-female'),
('Shoes', 'Sneakers, boots, sandals, formal shoes', 'fas fa-shoe-prints'),
('Accessories', 'Hats, scarves, belts, jewelry', 'fas fa-hat-cowboy'),
('Other', 'Miscellaneous clothing items', 'fas fa-ellipsis-h');

-- Create indexes for better performance
CREATE INDEX idx_items_category_status ON items(category, status);
CREATE INDEX idx_swap_requests_status_created ON swap_requests(status, created_at);
CREATE INDEX idx_redemptions_status_created ON redemptions(status, created_at);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);

-- Create views for common queries
CREATE VIEW available_items AS
SELECT i.*, u.name as owner_name, u.points as owner_points
FROM items i
JOIN users u ON i.owner_id = u.user_id
WHERE i.status = 'Available'
ORDER BY i.created_at DESC;

CREATE VIEW user_swap_history AS
SELECT 
    sr.*,
    i1.title as offered_item_title,
    i2.title as requested_item_title,
    u1.name as from_user_name,
    u2.name as to_user_name
FROM swap_requests sr
JOIN items i1 ON sr.offered_item_id = i1.item_id
JOIN items i2 ON sr.requested_item_id = i2.item_id
JOIN users u1 ON sr.from_user_id = u1.user_id
JOIN users u2 ON sr.to_user_id = u2.user_id
ORDER BY sr.created_at DESC;

CREATE VIEW user_redemptions AS
SELECT 
    r.*,
    i.title as item_title,
    i.category as item_category,
    u.name as user_name
FROM redemptions r
JOIN items i ON r.item_id = i.item_id
JOIN users u ON r.user_id = u.user_id
ORDER BY r.created_at DESC;

-- Create stored procedures for common operations

-- Procedure to create a new user
DELIMITER //
CREATE PROCEDURE CreateUser(
    IN p_user_id CHAR(36),
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_password_hash VARCHAR(255),
    IN p_role ENUM('user', 'admin') DEFAULT 'user'
)
BEGIN
    INSERT INTO users (user_id, name, email, password_hash, role)
    VALUES (p_user_id, p_name, p_email, p_password_hash, p_role);
    
    INSERT INTO user_profiles (profile_id, user_id)
    VALUES (UUID(), p_user_id);
END //
DELIMITER ;

-- Procedure to add points to a user
DELIMITER //
CREATE PROCEDURE AddPoints(
    IN p_user_id CHAR(36),
    IN p_amount INT,
    IN p_type ENUM('earned', 'spent', 'refunded', 'bonus', 'penalty'),
    IN p_description VARCHAR(255),
    IN p_related_item_id CHAR(36) DEFAULT NULL
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Update user points
    UPDATE users SET points = points + p_amount WHERE user_id = p_user_id;
    
    -- Record transaction
    INSERT INTO point_transactions (transaction_id, user_id, amount, type, description, related_item_id)
    VALUES (UUID(), p_user_id, p_amount, p_type, p_description, p_related_item_id);
    
    COMMIT;
END //
DELIMITER ;

-- Procedure to create a swap request
DELIMITER //
CREATE PROCEDURE CreateSwapRequest(
    IN p_request_id CHAR(36),
    IN p_from_user_id CHAR(36),
    IN p_to_user_id CHAR(36),
    IN p_offered_item_id CHAR(36),
    IN p_requested_item_id CHAR(36)
)
BEGIN
    DECLARE offered_owner_id CHAR(36);
    DECLARE requested_owner_id CHAR(36);
    
    -- Get item owners
    SELECT owner_id INTO offered_owner_id FROM items WHERE item_id = p_offered_item_id;
    SELECT owner_id INTO requested_owner_id FROM items WHERE item_id = p_requested_item_id;
    
    -- Validate ownership
    IF offered_owner_id != p_from_user_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User does not own the offered item';
    END IF;
    
    IF requested_owner_id != p_to_user_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Requested item owner mismatch';
    END IF;
    
    -- Create swap request
    INSERT INTO swap_requests (request_id, from_user_id, to_user_id, offered_item_id, requested_item_id)
    VALUES (p_request_id, p_from_user_id, p_to_user_id, p_offered_item_id, p_requested_item_id);
    
    -- Create notification
    INSERT INTO notifications (notification_id, user_id, type, title, message, related_id)
    VALUES (UUID(), p_to_user_id, 'swap_request', 'New Swap Request', 
            CONCAT('You have received a new swap request for your item.'), p_request_id);
END //
DELIMITER ;

-- Procedure to redeem an item with points
DELIMITER //
CREATE PROCEDURE RedeemItem(
    IN p_redemption_id CHAR(36),
    IN p_user_id CHAR(36),
    IN p_item_id CHAR(36),
    IN p_shipping_address TEXT
)
BEGIN
    DECLARE item_points INT;
    DECLARE user_points INT;
    DECLARE item_status VARCHAR(20);
    
    -- Get item details
    SELECT point_value, status INTO item_points, item_status 
    FROM items WHERE item_id = p_item_id;
    
    -- Get user points
    SELECT points INTO user_points FROM users WHERE user_id = p_user_id;
    
    -- Validate redemption
    IF item_status != 'Available' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item is not available for redemption';
    END IF;
    
    IF user_points < item_points THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insufficient points for redemption';
    END IF;
    
    -- Create redemption
    INSERT INTO redemptions (redemption_id, user_id, item_id, points_used, shipping_address)
    VALUES (p_redemption_id, p_user_id, p_item_id, item_points, p_shipping_address);
    
    -- Update item status
    UPDATE items SET status = 'Redeemed' WHERE item_id = p_item_id;
    
    -- Deduct points
    CALL AddPoints(p_user_id, -item_points, 'spent', 
                   CONCAT('Redeemed item: ', (SELECT title FROM items WHERE item_id = p_item_id)), p_item_id);
END //
DELIMITER ;

-- Create triggers for automatic actions

-- Trigger to update item status when swap is completed
DELIMITER //
CREATE TRIGGER after_swap_completed
AFTER UPDATE ON swap_requests
FOR EACH ROW
BEGIN
    IF NEW.status = 'Completed' AND OLD.status != 'Completed' THEN
        UPDATE items SET status = 'Swapped' WHERE item_id IN (NEW.offered_item_id, NEW.requested_item_id);
    END IF;
END //
DELIMITER ;

-- Trigger to create notification when item is approved/rejected
DELIMITER //
CREATE TRIGGER after_admin_action
AFTER INSERT ON admin_logs
FOR EACH ROW
BEGIN
    DECLARE item_owner_id CHAR(36);
    DECLARE notification_title VARCHAR(255);
    DECLARE notification_message TEXT;
    
    IF NEW.item_id IS NOT NULL THEN
        SELECT owner_id INTO item_owner_id FROM items WHERE item_id = NEW.item_id;
        
        IF NEW.action_type = 'Approved' THEN
            SET notification_title = 'Item Approved';
            SET notification_message = 'Your item has been approved and is now available.';
        ELSEIF NEW.action_type = 'Rejected' THEN
            SET notification_title = 'Item Rejected';
            SET notification_message = CONCAT('Your item has been rejected. Reason: ', COALESCE(NEW.reason, 'No reason provided'));
        END IF;
        
        INSERT INTO notifications (notification_id, user_id, type, title, message, related_id)
        VALUES (UUID(), item_owner_id, 
                CASE WHEN NEW.action_type = 'Approved' THEN 'item_approved' ELSE 'item_rejected' END,
                notification_title, notification_message, NEW.item_id);
    END IF;
END //
DELIMITER ;

-- Insert default admin user (password: admin123)
INSERT INTO users (user_id, name, email, password_hash, role, points) VALUES
(UUID(), 'Admin User', 'admin@renewablecloth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1000);

-- Insert sample data for testing
INSERT INTO users (user_id, name, email, password_hash, role, points) VALUES
(UUID(), 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 500),
(UUID(), 'Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 750);

-- Grant permissions (adjust as needed for your setup)
-- GRANT ALL PRIVILEGES ON renewable_cloth.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES; 