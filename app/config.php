<?php
// Database Configuration
// Support both environment variables and default values
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'dvwa');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'dvwa123');
define('DB_NAME', getenv('DB_NAME') ?: 'dvwa');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
$mysqli = @new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT
);

// Connection error handling
// Don't die immediately - let login.php handle the setup flow
if ($mysqli->connect_error) {
    $mysqli = null;
} else {
    // Set charset
    $mysqli->set_charset("utf8mb4");
}

// Security Headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
?>
