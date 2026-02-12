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

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)$dbPort);

// Check connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset('utf8mb4');
