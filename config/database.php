<?php
/**
 * Database Configuration
 * 
 * This file supports TWO environments automatically:
 *
 * LOCAL DEVELOPMENT (XAMPP / WAMP):
 *   - When no environment variables are set, the fallback values are used.
 *   - Defaults: host=localhost, user=root, password=(empty), database=car_rental_system
 *   - If your MySQL runs on a non-default port (e.g. 3307), update the fallback port below.
 *
 * RAILWAY DEPLOYMENT:
 *   - Railway automatically injects MySQL environment variables when you
 *     add a MySQL service to your project. No manual configuration needed.
 *   - Variables used: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
 */

// Read Railway environment variables; fall back to local defaults
define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost');
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'car_rental_system');
$dbPort =         getenv('MYSQLPORT')     ?: 3307;  // Change 3307 to 3306 if your local MySQL uses the default port

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";port=$dbPort;dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // For backward compatibility during refactoring, assign to $conn if needed, 
    // but we will switch to $pdo everywhere.
    $conn = $pdo; 

} catch (PDOException $e) {
    // If connection fails, stop script
    die('Database connection failed: ' . $e->getMessage());
}
