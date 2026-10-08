<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit;
}

require_once "../config/database.php";

try {

    $stmt = $pdo->query("
        SELECT
            id,
            ip_address,
            username,
            attack_type,
            command,
            activity,
            risk_level,
            status,
            created_at
        FROM attack_events
        WHERE risk_level IN ('Critical', 'High', 'Medium')
        ORDER BY created_at DESC
        LIMIT 50
    ");

    $alerts = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "count" => count($alerts),
        "alerts" => $alerts
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve alerts."
    ]);

}