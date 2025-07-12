<?php
require_once '../auth/session.php';
require_once '../auth/database.php';

$user = getCurrentUser();

// Check if user is logged in and is admin
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$item_id = $input['item_id'] ?? '';

if (empty($item_id)) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit();
}

// Database connection
$host = "localhost";
$dbname = "renewable_cloth";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if item exists
    $stmt = $pdo->prepare("SELECT item_id FROM items WHERE item_id = ?");
    $stmt->execute([$item_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit();
    }
    
    // Delete the item (cascade will handle related records)
    $stmt = $pdo->prepare("DELETE FROM items WHERE item_id = ?");
    $stmt->execute([$item_id]);
    
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 