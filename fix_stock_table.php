<?php
// Database connection parameters
$host = 'localhost';
$database = 'apotek_systems_dbms';
$username = 'root';
$password = '';

echo "Connecting to database: $database on $host as $username\n";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully\n";

// SQL commands to fix the stock_adjustment_logs table
$sql = file_get_contents(__DIR__ . '/sql/fix_stock_adjustment_logs.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "SQL executed successfully\n";
    
    // Process all result sets
    do {
        // Check if there are more results
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
} else {
    echo "Error executing SQL: " . $conn->error . "\n";
}

$conn->close();
echo "Done!\n"; 