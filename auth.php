<?php

if (session_status() === PHP_SESSION_NONE) {
    
    // Secure session configuration for production
    if (getenv("APP_ENV") === "production") {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); // Requires HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
    }
    
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}