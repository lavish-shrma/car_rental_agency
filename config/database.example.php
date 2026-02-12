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
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = $pdo;

} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
