<?php
// ReWear Setup Script
// Created by Team CodeCatalysts

echo "<h2>ReWear Setup Script</h2>";
echo "<p>This script will help you set up the ReWear application.</p>";

// Test database connection
echo "<h3>1. Testing Database Connection</h3>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . $e->getMessage() . "</p>";
}

// Check if database exists
echo "<h3>2. Checking Database</h3>";
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $stmt = $pdo->query("SHOW DATABASES LIKE 'rewear_db'");
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Database 'rewear_db' exists!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Database 'rewear_db' does not exist. Please import the database.sql file.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking database: " . $e->getMessage() . "</p>";
}

// Check if tables exist
echo "<h3>3. Checking Tables</h3>";
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $tables = ['users', 'items', 'item_images', 'swap_requests'];
    $all_tables_exist = true;
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
            $all_tables_exist = false;
        }
    }
    
    if ($all_tables_exist) {
        echo "<p style='color: green;'><strong>✅ All tables exist!</strong></p>";
    } else {
        echo "<p style='color: orange;'><strong>⚠️ Some tables are missing. Please import the database.sql file.</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking tables: " . $e->getMessage() . "</p>";
}

// Check default users
echo "<h3>4. Checking Default Users</h3>";
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($users) . " user(s):</p>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li><strong>" . $user['username'] . "</strong> (" . $user['role'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ No users found. Please import the database.sql file.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking users: " . $e->getMessage() . "</p>";
}

// Setup instructions
echo "<h3>5. Setup Instructions</h3>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px;'>";
echo "<h4>To complete the setup:</h4>";
echo "<ol>";
echo "<li>Make sure XAMPP is running (Apache and MySQL)</li>";
echo "<li>Import the <code>database.sql</code> file into phpMyAdmin</li>";
echo "<li>Access the application at: <a href='index.php'>http://localhost/odoo/odoo_CodeCatalysts/</a></li>";
echo "<li>Use the demo credentials to test:</li>";
echo "<ul>";
echo "<li><strong>Admin:</strong> username: admin, password: admin123</li>";
echo "<li><strong>User:</strong> username: user1, password: user123</li>";
echo "</ul>";
echo "</ol>";
echo "</div>";

echo "<h3>6. File Structure Check</h3>";
$required_files = [
    'config/database.php',
    'auth/login.php',
    'auth/register.php',
    'auth/logout.php',
    'Admin/dashboard.php',
    'User/dashboard.php',
    'index.php',
    'database.sql'
];

$all_files_exist = true;
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file</p>";
    } else {
        echo "<p style='color: red;'>❌ $file (missing)</p>";
        $all_files_exist = false;
    }
}

if ($all_files_exist) {
    echo "<p style='color: green;'><strong>✅ All required files are present!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>❌ Some files are missing. Please check the file structure.</strong></p>";
}

echo "<hr>";
echo "<p><strong>Setup complete!</strong> If everything shows green checkmarks, your ReWear application is ready to use.</p>";
?> 