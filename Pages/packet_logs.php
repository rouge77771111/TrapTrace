<?php

require_once "../auth.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| PACKET STATISTICS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM packet_capture_logs
");

$totalPackets = (int) $stmt->fetchColumn();


$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM packet_capture_logs
    WHERE protocol = 'TCP'
");

$tcpPackets = (int) $stmt->fetchColumn();


$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM packet_capture_logs
    WHERE protocol = 'UDP'
");

$udpPackets = (int) $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| RECENT PACKETS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        id,
        source_ip,
        destination_ip,
        protocol,
        source_port,
        destination_port,
        packet_size,
        packet_info,
        captured_at
    FROM packet_capture_logs
    ORDER BY captured_at DESC
    LIMIT 100
");

$packets = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Packet Capture Logs | Trap&Trace</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #111827;
            color: white;
            padding: 25px 15px;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 12px;
        }

        .logo span {
            color: #3b82f6;
        }

        .menu-title {
            font-size: 11px;
            color: #9ca3af;
            margin: 20px 12px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #d1d5db;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .logout {
            margin-top: 25px;
        }

        .logout a {
            color: #fca5a5;
        }

        /* MAIN */

        .main {
            margin-left: 240px;
            padding: 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .page-title p {
            color: #6b7280;
            font-size: 14px;
        }

        .user-info {
            text-align: right;
        }

        .user-info strong {
            display: block;
            font-size: 14px;
        }

        .user-info span {
            color: #6b7280;
            font-size: 12px;
        }

        /* CAPTURE BUTTON */

        .capture-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .capture-info h2 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .capture-info p {
            color: #6b7280;
            font-size: 13px;
        }

        .capture-button {
            border: none;
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            min-width: 160px;
        }

        .capture-button:hover {
            background: #1d4ed8;
        }

        .capture-button:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .capture-message {
            margin-top: 12px;
            font-size: 13px;
            display: none;
        }

        .capture-message.success {
            display: block;
            color: #15803d;
        }

        .capture-message.error {
            display: block;
            color: #b91c1c;
        }

        /* CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
        }

        .blue {
            color: #2563eb;
        }

        .purple {
            color: #7c3aed;
        }

        /* TABLE */

        .table-panel {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h2 {
            font-size: 18px;
        }

        .table-header span {
            color: #6b7280;
            font-size: 12px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            text-align: left;
            padding: 12px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 13px 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .protocol {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .tcp {
            background: #dbeafe;
            color: #2563eb;
        }

        .udp {
            background: #ede9fe;
            color: #7c3aed;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .capture-section {
                flex-direction: column;
                align-items: flex-start;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
                gap: 15px;
            }

            .user-info {
                text-align: left;
            }

        }

    </style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        Trap<span>&</span>Trace
    </div>

    <div class="menu-title">
        Main
    </div>

    <a href="/dashboard.php">
        Dashboard
    </a>

    <a href="/Pages/monitoring.php">
        Monitoring
    </a>

    <a href="/Pages/alerts.php">
        Alerts
    </a>

    <a href="/Pages/packet_logs.php" class="active">
        Packet Logs
    </a>

    <a href="/Pages/attackers.php">
        Attackers
    </a>

    <a href="/Pages/logs.php">
        Logs
    </a>

    <div class="menu-title">
        Analysis
    </div>

    <a href="/Pages/analytics.php">
        Analytics
    </a>

    <a href="/Pages/reports.php">
        Reports
    </a>

    <div class="menu-title">
        System
    </div>

    <a href="/Pages/settings.php">
        Honeypot Configuration
    </a>

    <div class="logout">

        <a href="/logout.php">
            Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="topbar">

        <div class="page-title">

            <h1>
                Packet Capture Logs
            </h1>

            <p>
                Network packets captured by the honeypot
            </p>

        </div>

        <div class="user-info">

            <strong>
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </strong>

            <span>
                <?= htmlspecialchars($_SESSION["role"]) ?>
            </span>

        </div>

    </div>


    <!-- CAPTURE SECTION -->

    <div class="capture-section">

        <div class="capture-info">

            <h2>
                Network Packet Capture
            </h2>

            <p>
                Capture packets using TShark and automatically analyze
                the traffic for possible port scanning activity.
            </p>

            <div
                id="captureMessage"
                class="capture-message"
            ></div>

        </div>

        <button
            type="button"
            class="capture-button"
            id="captureButton"
        >
            Capture Packets
        </button>

    </div>


    <!-- STATISTICS -->

    <div class="cards">

        <div class="card">

            <div class="card-title">
                Total Packets
            </div>

            <div class="card-value">
                <?= $totalPackets ?>
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                TCP Packets
            </div>

            <div class="card-value blue">
                <?= $tcpPackets ?>
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                UDP Packets
            </div>

            <div class="card-value purple">
                <?= $udpPackets ?>
            </div>

        </div>

    </div>


    <!-- PACKET TABLE -->

    <div class="table-panel">

        <div class="table-header">

            <h2>
                Captured Packets
            </h2>

            <span>
                Latest 100 packets
            </span>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Time
                        </th>

                        <th>
                            Source IP
                        </th>

                        <th>
                            Destination IP
                        </th>

                        <th>
                            Protocol
                        </th>

                        <th>
                            Source Port
                        </th>

                        <th>
                            Destination Port
                        </th>

                        <th>
                            Packet Size
                        </th>

                        <th>
                            Information
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (empty($packets)): ?>

                    <tr>

                        <td colspan="8">
                            No packets captured yet.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($packets as $packet): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($packet["captured_at"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["source_ip"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["destination_ip"]) ?>
                            </td>

                            <td>

                                <?php

                                $protocol = strtoupper(
                                    $packet["protocol"] ?? "Unknown"
                                );

                                $protocolClass = strtolower($protocol);

                                ?>

                                <span
                                    class="protocol <?= htmlspecialchars($protocolClass) ?>"
                                >
                                    <?= htmlspecialchars($protocol) ?>
                                </span>

                            </td>

                            <td>
                                <?= $packet["source_port"] !== null
                                    ? htmlspecialchars($packet["source_port"])
                                    : "-"
                                ?>
                            </td>

                            <td>
                                <?= $packet["destination_port"] !== null
                                    ? htmlspecialchars($packet["destination_port"])
                                    : "-"
                                ?>
                            </td>

                            <td>
                                <?= $packet["packet_size"] !== null
                                    ? htmlspecialchars($packet["packet_size"]) . " bytes"
                                    : "-"
                                ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["packet_info"]) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<script>

const captureButton = document.getElementById("captureButton");
const captureMessage = document.getElementById("captureMessage");

captureButton.addEventListener("click", function () {

    captureButton.disabled = true;
    captureButton.textContent = "Capturing...";

    captureMessage.className = "capture-message";
    captureMessage.textContent = "";

    fetch("/API/capture_packets.php")

        .then(response => response.json())

        .then(data => {

            if (data.success) {

                let message =
                    "Capture completed. "
                    + data.saved_packets
                    + " packets saved.";

                if (
                    data.port_scan_detections !== undefined
                    && data.port_scan_detections > 0
                ) {

                    message +=
                        " "
                        + data.port_scan_detections
                        + " port scanning detection(s) generated.";

                }

                captureMessage.className =
                    "capture-message success";

                captureMessage.textContent = message;

                setTimeout(function () {

                    window.location.reload();

                }, 1500);

            } else {

                captureMessage.className =
                    "capture-message error";

                captureMessage.textContent =
                    data.message || "Packet capture failed.";

            }

        })

        .catch(error => {

            captureMessage.className =
                "capture-message error";

            captureMessage.textContent =
                "Unable to connect to the packet capture service.";

        })

        .finally(() => {

            setTimeout(function () {

                captureButton.disabled = false;
                captureButton.textContent = "Capture Packets";

            }, 1500);

        });

});

</script>

</body>

</html>