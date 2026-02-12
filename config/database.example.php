<?php
/**
 * Database Configuration — TEMPLATE
 * 
 * SETUP: Copy this file to database.php in the same directory:
 *        cp database.example.php database.php
 *
 * DO NOT commit database.php to version control.
 *
 * This file supports TWO environments automatically:
 *
 * LOCAL DEVELOPMENT (XAMPP / WAMP):
 *   - When no environment variables are set, the fallback values below are used.
 *   - Update the fallback values to match your local MySQL setup.
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
$dbPort =         getenv('MYSQLPORT')     ?: 3306;  // Change to 3307 if your local MySQL uses that port

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";port=$dbPort;dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = $pdo;

} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
