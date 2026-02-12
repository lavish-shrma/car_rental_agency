<?php
/**
 * Database Configuration - FINAL ROBUST FIX
 * 
 * Priority:
 * 1. MYSQL_URL / DATABASE_URL (full connection string)
 * 2. Individual Railway variables (MYSQLHOST, MYSQLDATABASE, etc.)
 * 3. Smart fallback: if host contains "railway", database = "railway"
 * 4. Local fallback: localhost defaults for XAMPP
 *
 * IMPORTANT: Uses ?: (not ??) so empty strings are treated as missing.
 */

// ── Step 1: Try MYSQL_URL / DATABASE_URL ──────────────────────────────────────
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

// ── Step 2: Resolve each value ────────────────────────────────────────────────
// Using ?: (not ??) so empty strings fall through to the next option.

$dbHost = ($urlParts['host'] ?? '')
    ?: (getenv('MYSQLHOST') ?: '')
    ?: (getenv('MYSQL_HOST') ?: '')
    ?: 'localhost';

$dbUser = ($urlParts['user'] ?? '')
    ?: (getenv('MYSQLUSER') ?: '')
    ?: (getenv('MYSQL_USER') ?: '')
    ?: 'root';

$dbPass = ($urlParts['pass'] ?? '')
    ?: (getenv('MYSQLPASSWORD') ?: '')
    ?: (getenv('MYSQL_PASSWORD') ?: '')
    ?: '';

$dbName = ($urlParts['name'] ?? '')
    ?: (getenv('MYSQLDATABASE') ?: '')
    ?: (getenv('MYSQL_DATABASE') ?: '')
    ?: '';

$dbPort = ($urlParts['port'] ?? '')
    ?: (getenv('MYSQLPORT') ?: '')
    ?: (getenv('MYSQL_PORT') ?: '')
    ?: 3306;

// ── Step 3: Smart fallback for database name ──────────────────────────────────
// If we're on Railway (host contains "railway") but dbName is still empty,
// Railway's default database is always called "railway".
if (empty($dbName)) {
    if (strpos($dbHost, 'railway') !== false) {
        $dbName = 'railway';
    } else {
        $dbName = 'car_rental_system'; // Local XAMPP default
    }
}

// ── Step 4: Define constants ──────────────────────────────────────────────────
define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', (int) $dbPort);

// ── Step 5: Connect ───────────────────────────────────────────────────────────
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
    $safeMsg = str_replace([DB_PASS, DB_USER], ['***', '***'], $e->getMessage());
    die('<div style="color:red; font-family:sans-serif; padding:20px; border:1px solid red; background:#fff3f3;">
        <h2>Database Connection Failed</h2>
        <p><strong>Error:</strong> ' . htmlspecialchars($safeMsg) . '</p>
        <p><strong>Debug:</strong><br>
        Host: ' . htmlspecialchars(DB_HOST) . '<br>
        Port: ' . DB_PORT . '<br>
        Database: ' . htmlspecialchars(DB_NAME) . ' (len=' . strlen(DB_NAME) . ')</p>
    </div>');
}
