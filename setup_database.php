<?php
// Database setup script for Renewable Cloth website
// This script will create the database and import the schema

$host = "localhost";
$username = "root";
$password = "";

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL successfully!\n";
    
    // Read the SQL schema file
    $sql_file = 'database_schema.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        
        // Split the SQL into individual statements
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    echo "Executed: " . substr($statement, 0, 50) . "...\n";
                } catch (PDOException $e) {
                    echo "Error executing statement: " . $e->getMessage() . "\n";
                    echo "Statement: " . $statement . "\n";
                }
            }
        }
        
        echo "\nDatabase setup completed successfully!\n";
        echo "You can now use the website with the new schema.\n";
        
    } else {
        echo "Error: database_schema.sql file not found!\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 