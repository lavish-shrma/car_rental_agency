<?php
/**
 * Database Configuration
 * 
 * PRODUCTION NOTE:
 * This app runs inside a container SEPARATE from the MySQL container.
 * Using "localhost" will NOT work in production — environment variables are required.
 * Railway injects: MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
 *
 * Priority order:
 * 1. MYSQL_URL / DATABASE_URL (full connection string, if Railway provides it)
 * 2. Individual Railway env vars (MYSQLHOST, MYSQLDATABASE, etc.)
 * 3. Local development fallback (localhost/root — only works on your own machine)
 *
 * SECURITY: Production sites must not expose internal paths, SQL queries,
 * hostnames, or credentials to visitors. Errors are logged, not displayed.
 */

// ── Disable error display in production ───────────────────────────────────────
// Prevents PHP from printing internal errors, paths, or queries to visitors.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

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
// Uses ?: (not ??) so empty strings fall through to the next option.

$dbHost = ($urlParts['host'] ?? '')
    ?: (getenv('MYSQLHOST') ?: '')
    ?: (getenv('MYSQL_HOST') ?: '')
    ?: 'localhost';  // Local fallback only — will not work in production

$dbUser = ($urlParts['user'] ?? '')
    ?: (getenv('MYSQLUSER') ?: '')
    ?: (getenv('MYSQL_USER') ?: '')
    ?: 'root';  // Local fallback only

$dbPass = ($urlParts['pass'] ?? '')
    ?: (getenv('MYSQLPASSWORD') ?: '')
    ?: (getenv('MYSQL_PASSWORD') ?: '')
    ?: '';  // Local fallback only

$dbName = ($urlParts['name'] ?? '')
    ?: (getenv('MYSQLDATABASE') ?: '')
    ?: (getenv('MYSQL_DATABASE') ?: '')
    ?: '';

$dbPort = ($urlParts['port'] ?? '')
    ?: (getenv('MYSQLPORT') ?: '')
    ?: (getenv('MYSQL_PORT') ?: '')
    ?: 3306;

// ── Step 3: Fail safely if database name is missing ───────────────────────────
// Do NOT guess the database name. If credentials are missing, stop immediately.
if (empty($dbName)) {
    // Log the real error for the developer
    error_log('FATAL: MYSQLDATABASE environment variable is not set. Cannot connect.');
    die('Service temporarily unavailable. Please try again in a few moments.');
}

// ── Step 4: Define constants ──────────────────────────────────────────────────
define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', (int) $dbPort);

// ── Step 5: Connect via PDO ───────────────────────────────────────────────────
try {
    // utf8mb4 prevents encoding issues; ERRMODE_EXCEPTION catches errors properly;
    // EMULATE_PREPARES=false reduces SQL injection edge cases.
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = $pdo;

} catch (PDOException $e) {
    // Log the real error internally — never show it to visitors
    error_log('Database connection failed: ' . $e->getMessage());
    die('Service temporarily unavailable. Please try again in a few moments.');
}
