<?php
/**
 * Database Configuration - TEMPLATE
 * 
 * SETUP: Copy this file to database.php
 * 
 * See database.php for full documentation.
 * Railway variables: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
 */

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

$dbUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
$urlParts = [];
if (!empty($dbUrl)) {
    $parsed = parse_url($dbUrl);
    if ($parsed) {
        $urlParts['host'] = $parsed['host'] ?? '';
        $urlParts['user'] = $parsed['user'] ?? '';
        $urlParts['pass'] = $parsed['pass'] ?? '';
        $urlParts['port'] = $parsed['port'] ?? '';
        $urlParts['name'] = ltrim($parsed['path'] ?? '', '/');
    }
}

$dbHost = ($urlParts['host'] ?? '') ?: (getenv('MYSQLHOST') ?: '') ?: (getenv('MYSQL_HOST') ?: '') ?: 'localhost';
$dbUser = ($urlParts['user'] ?? '') ?: (getenv('MYSQLUSER') ?: '') ?: (getenv('MYSQL_USER') ?: '') ?: 'root';
$dbPass = ($urlParts['pass'] ?? '') ?: (getenv('MYSQLPASSWORD') ?: '') ?: (getenv('MYSQL_PASSWORD') ?: '') ?: '';
$dbName = ($urlParts['name'] ?? '') ?: (getenv('MYSQLDATABASE') ?: '') ?: (getenv('MYSQL_DATABASE') ?: '') ?: '';
$dbPort = ($urlParts['port'] ?? '') ?: (getenv('MYSQLPORT') ?: '') ?: (getenv('MYSQL_PORT') ?: '') ?: 3306;

if (empty($dbName)) {
    error_log('FATAL: MYSQLDATABASE environment variable is not set.');
    die('Service temporarily unavailable. Please try again in a few moments.');
}

define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', (int) $dbPort);

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = $pdo;
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Service temporarily unavailable. Please try again in a few moments.');
}
