<?php
/**
 * Database Configuration - ROBUST RAILWAY FIX
 * 
 * This script ensures a connection on Railway by checking ALL possible environment variables.
 * Priority:
 * 1. MYSQL_URL or DATABASE_URL (Parsed as a full connection string)
 * 2. Individual Railway Variables (MYSQLDATABASE, etc.)
 * 3. Local Fallbacks (XAMPP default)
 */

// 1. Try to parse a full connection URL first (Railway often provides this)
$dbUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
$urlConfig = [];

if ($dbUrl) {
    $parsedUrl = parse_url($dbUrl);
    if ($parsedUrl) {
        $urlConfig['host'] = $parsedUrl['host'] ?? null;
        $urlConfig['user'] = $parsedUrl['user'] ?? null;
        $urlConfig['pass'] = $parsedUrl['pass'] ?? null;
        $urlConfig['port'] = $parsedUrl['port'] ?? null;
        $urlConfig['path'] = $parsedUrl['path'] ?? null; // '/dbname'
    }
}

// 2. Define values with a cascade of fallbacks
// Host
$dbHost = $urlConfig['host'] 
    ?? $_ENV['MYSQLHOST'] ?? $_ENV['MYSQL_HOST'] 
    ?? getenv('MYSQLHOST') ?? getenv('MYSQL_HOST') 
    ?? 'localhost';

// User
$dbUser = $urlConfig['user'] 
    ?? $_ENV['MYSQLUSER'] ?? $_ENV['MYSQL_USER'] 
    ?? getenv('MYSQLUSER') ?? getenv('MYSQL_USER') 
    ?? 'root';

// Password
$dbPass = $urlConfig['pass'] 
    ?? $_ENV['MYSQLPASSWORD'] ?? $_ENV['MYSQL_PASSWORD'] 
    ?? getenv('MYSQLPASSWORD') ?? getenv('MYSQL_PASSWORD') 
    ?? '';

// Database Name (Handle the '/' from parse_url if needed)
$rawDbName = $urlConfig['path'] 
    ?? $_ENV['MYSQLDATABASE'] ?? $_ENV['MYSQL_DATABASE'] 
    ?? getenv('MYSQLDATABASE') ?? getenv('MYSQL_DATABASE') 
    ?? 'car_rental_system';
$dbName = ltrim($rawDbName, '/'); // Remove leading slash if from URL

// Port
$dbPort = $urlConfig['port'] 
    ?? $_ENV['MYSQLPORT'] ?? $_ENV['MYSQL_PORT'] 
    ?? getenv('MYSQLPORT') ?? getenv('MYSQL_PORT') 
    ?? 3306;

// 3. Define Constants
define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', $dbPort);

try {
    // 4. Validate Database Name
    if (empty(DB_NAME)) {
        throw new PDOException("Database name is missing! Check your Railway variables.");
    }

    // 5. Create DSN with explicit dbname
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = $pdo; // For backward compatibility

} catch (PDOException $e) {
    $safeMessage = str_replace([DB_PASS, DB_USER], ['***', '***'], $e->getMessage());
    die('<div style="color:red; font-family:sans-serif; padding:20px; border:1px solid red; background:#fff3f3;">
        <h2>Database Connection Failed</h2>
        <p><strong>Error:</strong> ' . htmlspecialchars($safeMessage) . '</p>
        <p><strong>Debug Info:</strong><br>
        Host: ' . htmlspecialchars(DB_HOST) . '<br>
        Port: ' . htmlspecialchars(DB_PORT) . '<br>
        Database: ' . htmlspecialchars(DB_NAME) . ' (Length: ' . strlen(DB_NAME) . ')</p>
    </div>');
}
