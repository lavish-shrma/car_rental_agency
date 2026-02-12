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
// Priority: $_ENV > $_SERVER > getenv() > Local Defaults
// We check both 'MYSQL...' and 'MYSQL_...' formats to be safe.

$dbHost = $_ENV['MYSQLHOST']     ?? $_ENV['MYSQL_HOST']     ?? $_SERVER['MYSQLHOST']     ?? $_SERVER['MYSQL_HOST']     ?? getenv('MYSQLHOST')     ?? getenv('MYSQL_HOST')     ?? 'localhost';
$dbUser = $_ENV['MYSQLUSER']     ?? $_ENV['MYSQL_USER']     ?? $_SERVER['MYSQLUSER']     ?? $_SERVER['MYSQL_USER']     ?? getenv('MYSQLUSER')     ?? getenv('MYSQL_USER')     ?? 'root';
$dbPass = $_ENV['MYSQLPASSWORD'] ?? $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQLPASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? getenv('MYSQLPASSWORD') ?? getenv('MYSQL_PASSWORD') ?? '';
$dbName = $_ENV['MYSQLDATABASE'] ?? $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQLDATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? getenv('MYSQLDATABASE') ?? getenv('MYSQL_DATABASE') ?? 'car_rental_system';
$dbPort = $_ENV['MYSQLPORT']     ?? $_ENV['MYSQL_PORT']     ?? $_SERVER['MYSQLPORT']     ?? $_SERVER['MYSQL_PORT']     ?? getenv('MYSQLPORT')     ?? getenv('MYSQL_PORT')     ?? 3307;

define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', $dbPort);

try {
    // Create PDO connection
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
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
