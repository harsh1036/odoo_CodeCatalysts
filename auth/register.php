<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    $errors = [];
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If there are validation errors, redirect back with errors
    if (!empty($errors)) {
        $error_string = implode(', ', $errors);
        header("Location: ../index.php?error=" . urlencode($error_string));
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if username already exists
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?error=Username already exists");
            exit();
        }
        
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?error=Email already exists");
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $query = "INSERT INTO users (first_name, last_name, username, email, phone, password, role, points) 
                  VALUES (:first_name, :last_name, :username, :email, :phone, :password, 'user', 50)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            // Registration successful
            header("Location: ../index.php?success=Registration successful! Please login with your credentials.");
        } else {
            header("Location: ../index.php?error=Registration failed. Please try again.");
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