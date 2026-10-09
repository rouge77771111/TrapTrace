<?php
/**
 * Health Check Endpoint
 * Used by Railway and monitoring services to check application status
 */

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'environment' => getenv('APP_ENV') ?: 'development'
];

// Check database connection
try {
    require_once __DIR__ . '/config/database.php';
    
    $stmt = $pdo->query('SELECT 1');
    $health['database'] = 'connected';
    $health['database_status'] = 'healthy';
    
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['database'] = 'disconnected';
    $health['database_status'] = 'unhealthy';
    
    if (getenv('APP_ENV') !== 'production') {
        $health['error'] = $e->getMessage();
    }
    
    http_response_code(503);
}

// Check if critical tables exist
if (isset($pdo)) {
    try {
        $tables = ['users', 'attack_events', 'packet_capture_logs', 'system_activity_logs'];
        $missingTables = [];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() === 0) {
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            $health['status'] = 'degraded';
            $health['warning'] = 'Some database tables are missing';
            if (getenv('APP_ENV') !== 'production') {
                $health['missing_tables'] = $missingTables;
            }
        }
        
    } catch (Exception $e) {
        // Ignore table check errors
    }
}

echo json_encode($health, JSON_PRETTY_PRINT);
