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

$interface = "4";
$packetCount = 10;

try {

    /*
    |--------------------------------------------------------------------------
    | CHECK ENVIRONMENT - Packet capture not available in production
    |--------------------------------------------------------------------------
    */
    
    if (getenv("APP_ENV") === "production" || getenv("RAILWAY_ENVIRONMENT")) {
        throw new Exception("Packet capture is not available in containerized environments like Railway. This feature requires direct network interface access and TShark installation, which are not supported in cloud platforms. Use this feature in local development environments only.");
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK IF TSHARK IS AVAILABLE
    |--------------------------------------------------------------------------
    */

    $tsharkCheck = shell_exec('tshark --version 2>&1');
    
    if ($tsharkCheck === null || stripos($tsharkCheck, 'TShark') === false) {
        throw new Exception("TShark is not installed or not accessible. Please install Wireshark/TShark to use packet capture functionality.");
    }

    /*
    |--------------------------------------------------------------------------
    | CAPTURE NETWORK PACKETS USING TSHARK
    |--------------------------------------------------------------------------
    */

    $command = 'tshark -i ' . escapeshellarg($interface)
        . ' -c ' . escapeshellarg($packetCount)
        . ' -T fields'
        . ' -e ip.src'
        . ' -e ip.dst'
        . ' -e _ws.col.Protocol'
        . ' -e tcp.srcport'
        . ' -e tcp.dstport'
        . ' -e udp.srcport'
        . ' -e udp.dstport'
        . ' -e frame.len'
        . ' -e _ws.col.Info'
        . ' -E separator="|"'
        . ' -E quote=d'
        . ' -E occurrence=f'
        . ' 2>&1';

    $output = shell_exec($command);

    if ($output === null) {
        throw new Exception("TShark command failed to execute. This may require administrator privileges.");
    }

    // Check for common TShark error messages
    if (stripos($output, 'permission denied') !== false) {
        throw new Exception("Permission denied. TShark requires administrator/root privileges to capture packets.");
    }

    if (stripos($output, 'no such device') !== false || stripos($output, 'does not exist') !== false) {
        throw new Exception("Network interface not found. Please check the interface configuration.");
    }

    if (trim($output) === "") {
        throw new Exception("No packets were captured. The network interface may be inactive or no traffic is present.");
    }

    $lines = preg_split("/\r\n|\n|\r/", trim($output));

    /*
    |--------------------------------------------------------------------------
    | SAVE CAPTURED PACKETS
    |--------------------------------------------------------------------------
    */

    $insertStmt = $pdo->prepare("
        INSERT INTO packet_capture_logs
        (
            source_ip,
            destination_ip,
            protocol,
            source_port,
            destination_port,
            packet_size,
            packet_info
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $savedPackets = 0;

    foreach ($lines as $line) {

        if (trim($line) === "") {
            continue;
        }

        $fields = str_getcsv($line, "|", '"');

        if (count($fields) < 8) {
            continue;
        }

        $sourceIp = trim($fields[0]);
        $destinationIp = trim($fields[1]);
        $protocol = trim($fields[2]);

        $tcpSourcePort = trim($fields[3]);
        $tcpDestinationPort = trim($fields[4]);

        $udpSourcePort = trim($fields[5]);
        $udpDestinationPort = trim($fields[6]);

        $packetSize = trim($fields[7]);
        $packetInfo = trim($fields[8] ?? "");

        if ($sourceIp === "" || $destinationIp === "") {
            continue;
        }

        $sourcePort = null;
        $destinationPort = null;

        if ($tcpSourcePort !== "") {

            $sourcePort = (int) $tcpSourcePort;

        } elseif ($udpSourcePort !== "") {

            $sourcePort = (int) $udpSourcePort;

        }

        if ($tcpDestinationPort !== "") {

            $destinationPort = (int) $tcpDestinationPort;

        } elseif ($udpDestinationPort !== "") {

            $destinationPort = (int) $udpDestinationPort;

        }

        $insertStmt->execute([
            $sourceIp,
            $destinationIp,
            $protocol !== "" ? $protocol : "Unknown",
            $sourcePort,
            $destinationPort,
            $packetSize !== "" ? (int) $packetSize : null,
            $packetInfo !== "" ? $packetInfo : "Packet captured by TShark."
        ]);

        $savedPackets++;
    }

    /*
    |--------------------------------------------------------------------------
    | PORT SCANNING DETECTION
    |--------------------------------------------------------------------------
    | Detects a source IP connecting to at least 5 different
    | destination ports within the last 5 minutes.
    */

    $scanStmt = $pdo->query("
        SELECT
            source_ip,
            COUNT(*) AS connection_count,
            COUNT(DISTINCT destination_port) AS unique_ports
        FROM packet_capture_logs
        WHERE captured_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
          AND destination_port IS NOT NULL
        GROUP BY source_ip
        HAVING COUNT(DISTINCT destination_port) >= 5
        ORDER BY unique_ports DESC
    ");

    $scans = $scanStmt->fetchAll();

    $portScanDetections = 0;

    foreach ($scans as $scan) {

        $sourceIp = $scan["source_ip"];
        $uniquePorts = (int) $scan["unique_ports"];
        $connectionCount = (int) $scan["connection_count"];

        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE DETECTIONS
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
        | RECORD PORT SCANNING ATTACK
        |--------------------------------------------------------------------------
        */

        $attackStmt = $pdo->prepare("
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

        $attackStmt->execute([
            $sourceIp,
            null,
            "Port Scanning",
            "Multiple connection attempts across "
                . $uniquePorts
                . " destination ports.",
            "Possible port scanning activity detected from the same source IP. "
                . $connectionCount
                . " connection attempts were observed across "
                . $uniquePorts
                . " different destination ports within 5 minutes.",
            "High",
            "Detected"
        ]);

        $portScanDetections++;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        "success" => true,
        "message" => "Packet capture and port scanning detection completed.",
        "captured_packets" => count($lines),
        "saved_packets" => $savedPackets,
        "port_scan_detections" => $portScanDetections
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}