<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        header("Location: ../index.php?error=Please fill in all fields");
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if username exists (username or email)
        $query = "SELECT * FROM users WHERE (username = :username OR email = :email) AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['points'] = $user['points'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ../Admin/dashboard.php");
                } else {
                    header("Location: ../User/dashboard.php");
                }
                exit();
            } else {
                header("Location: ../index.php?error=Invalid password");
                exit();
            }
        } else {
            header("Location: ../index.php?error=User not found or account inactive");
            exit();
        }
        
    } catch (PDOException $e) {
        header("Location: ../index.php?error=Database error occurred");
        exit();
    }
} else {
    // If not POST request, redirect to index
    header("Location: ../index.php");
    exit();
}
?> 