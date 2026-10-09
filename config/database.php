<?php

// Get environment variables with fallbacks
$host = getenv("MYSQLHOST") ?: "localhost";
$port = getenv("MYSQLPORT") ?: "3306";
$dbname = getenv("MYSQLDATABASE") ?: "traptrace";
$username = getenv("MYSQLUSER") ?: "root";
$password = getenv("MYSQLPASSWORD") ?: "";

// Check if we're using Railway MySQL URL format
$mysqlUrl = getenv("MYSQL_URL");
if ($mysqlUrl) {
    // Parse MySQL URL format: mysql://user:password@host:port/database
    $parsed = parse_url($mysqlUrl);
    if ($parsed) {
        $host = $parsed['host'] ?? $host;
        $port = $parsed['port'] ?? $port;
        $username = $parsed['user'] ?? $username;
        $password = $parsed['pass'] ?? $password;
        $dbname = ltrim($parsed['path'] ?? '', '/') ?: $dbname;
    }
}

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    // For production, add SSL if available
    if (getenv("APP_ENV") === "production" && getenv("MYSQL_SSL_CA")) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = getenv("MYSQL_SSL_CA");
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {
    // In production, don't expose detailed error messages
    if (getenv("APP_ENV") === "production") {
        error_log("Database connection failed: " . $e->getMessage());
        die("Unable to connect to the database. Please check your configuration.");
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}