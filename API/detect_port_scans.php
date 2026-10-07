<?php

require_once "../auth.php";
require_once "../config/database.php";

header("Content-Type: application/json; charset=UTF-8");

/*
|--------------------------------------------------------------------------
| PORT SCANNING DETECTION
|--------------------------------------------------------------------------
| Detects a source IP that connects to multiple destination ports
| within a short period of time.
|
| Detection rule:
| - Same source IP
| - At least 5 different destination ports
| - Within the last 5 minutes
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            source_ip,
            COUNT(*) AS connection_count,
            COUNT(DISTINCT destination_port) AS unique_ports,
            MIN(captured_at) AS first_seen,
            MAX(captured_at) AS last_seen
        FROM packet_capture_logs
        WHERE captured_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
          AND destination_port IS NOT NULL
        GROUP BY source_ip
        HAVING COUNT(DISTINCT destination_port) >= 5
        ORDER BY unique_ports DESC
    ");

    $scans = $stmt->fetchAll();

    $detected = 0;

    foreach ($scans as $scan) {

        $sourceIp = $scan["source_ip"];
        $uniquePorts = (int) $scan["unique_ports"];
        $connectionCount = (int) $scan["connection_count"];

        /*
        |--------------------------------------------------------------------------
        | Avoid creating duplicate port-scan events
        |--------------------------------------------------------------------------
        */

        $checkStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM attack_events
            WHERE ip_address = ?
              AND attack_type = 'Port Scanning'
              AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");

        $checkStmt->execute([$sourceIp]);

        $existingEvent = (int) $checkStmt->fetchColumn();

        if ($existingEvent > 0) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Record detected port scanning attack
        |--------------------------------------------------------------------------
        */

        $insertStmt = $pdo->prepare("
            INSERT INTO attack_events
            (
                ip_address,
                username,
                attack_type,
                command,
                activity,
                risk_level,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $insertStmt->execute([
            $sourceIp,
            null,
            "Port Scanning",
            "Multiple connection attempts across " . $uniquePorts . " destination ports.",
            "Possible port scanning activity detected from the same source IP. "
            . $connectionCount
            . " connection attempts were observed across "
            . $uniquePorts
            . " different destination ports within 5 minutes.",
            "High",
            "Detected"
        ]);

        $detected++;
    }

    echo json_encode([
        "success" => true,
        "message" => "Port scanning detection completed.",
        "sources_checked" => count($scans),
        "new_detections" => $detected
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to perform port scanning detection."
    ], JSON_PRETTY_PRINT);
}