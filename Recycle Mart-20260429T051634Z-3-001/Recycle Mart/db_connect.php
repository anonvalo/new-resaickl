<?php
// Hostinger uses localhost for the host
$host = 'localhost'; 

// Your exact database credentials from your screenshot
$dbname = 'u882780388_recycle';
$username = 'u882780388_mart';
$password = 'A3FfQGuG$v8tsrD';

try {
    // Create the secure PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If the connection fails, stop the page and show an error
    die("Database Connection Failed: " . $e->getMessage());
}
?>