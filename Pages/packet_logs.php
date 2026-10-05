<?php

require_once "../auth.php";
require_once "../config/database.php";

/* =========================
   PACKET STATISTICS
========================= */

$totalPackets = $pdo
    ->query("SELECT COUNT(*) FROM packet_capture_logs")
    ->fetchColumn();

$tcpPackets = $pdo
    ->query("
        SELECT COUNT(*)
        FROM packet_capture_logs
        WHERE protocol = 'TCP'
    ")
    ->fetchColumn();

$udpPackets = $pdo
    ->query("
        SELECT COUNT(*)
        FROM packet_capture_logs
        WHERE protocol = 'UDP'
    ")
    ->fetchColumn();

/* =========================
   PACKET LOGS
========================= */

$packets = $pdo
    ->query("
        SELECT *
        FROM packet_capture_logs
        ORDER BY captured_at DESC
        LIMIT 100
    ")
    ->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Trap&Trace - Packet Logs</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #1f2937;
        }

        /* =========================
           SIDEBAR
        ========================= */

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

        .sidebar-logo {
            text-align: center;
            padding: 10px 0 30px;
        }

        .sidebar-logo h2 {
            font-size: 27px;
        }

        .sidebar-logo span {
            color: #3b82f6;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-menu a {
            display: block;
            padding: 13px 15px;
            border-radius: 7px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout {
            margin-top: 25px;
        }

        .logout a {
            color: #fca5a5;
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        .main-content {
            margin-left: 240px;
            padding: 30px;
        }

        .top-header {
            margin-bottom: 30px;
        }

        .top-header h1 {
            font-size: 28px;
            color: #111827;
        }

        .top-header p {
            margin-top: 6px;
            color: #6b7280;
            font-size: 14px;
        }

        /* =========================
           STATISTICS
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            color: #6b7280;
            font-size: 13px;
            font-weight: normal;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .tcp {
            color: #2563eb;
        }

        .udp {
            color: #7c3aed;
        }

        /* =========================
           PACKET TABLE
        ========================= */

        .packets-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .packets-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .packets-header h2 {
            font-size: 20px;
        }

        .packets-header span {
            color: #6b7280;
            font-size: 13px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            text-align: left;
            padding: 13px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 14px 13px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        /* =========================
           PROTOCOL BADGES
        ========================= */

        .protocol {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .protocol-tcp {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .protocol-udp {
            background: #ede9fe;
            color: #6d28d9;
        }

        .protocol-other {
            background: #e5e7eb;
            color: #374151;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .stats-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="sidebar-logo">

        <h2>
            Trap<span>&</span>Trace
        </h2>

    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="../dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="monitoring.php">
                Monitoring
            </a>
        </li>

        <li>
            <a href="alerts.php">
                Alerts
            </a>
        </li>

        <li>
            <a
                href="packet_logs.php"
                class="active"
            >
                Packet Logs
            </a>
        </li>

        <li>
            <a href="attackers.php">
                Attackers
            </a>
        </li>

        <li>
            <a href="logs.php">
                Logs
            </a>
        </li>

        <li>
            <a href="analytics.php">
                Analytics
            </a>
        </li>

        <li>
            <a href="reports.php">
                Reports
            </a>
        </li>

        <li>
            <a href="settings.php">
                Honeypot Configuration
            </a>
        </li>

        <li class="logout">
            <a href="../logout.php">
                Logout
            </a>
        </li>

    </ul>

</aside>


<!-- =========================
     MAIN CONTENT
========================= -->

<main class="main-content">

    <header class="top-header">

        <h1>
            Packet Capture Logs
        </h1>

        <p>
            Network packets captured by the honeypot
        </p>

    </header>


    <!-- =========================
         STATISTICS
    ========================== -->

    <section class="stats-grid">

        <div class="stat-card">

            <h3>
                Total Packets
            </h3>

            <div class="stat-number">
                <?= $totalPackets ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                TCP Packets
            </h3>

            <div class="stat-number tcp">
                <?= $tcpPackets ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                UDP Packets
            </h3>

            <div class="stat-number udp">
                <?= $udpPackets ?>
            </div>

        </div>

    </section>


    <!-- =========================
         PACKET TABLE
    ========================== -->

    <section class="packets-card">

        <div class="packets-header">

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

                <?php if (count($packets) > 0): ?>

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

                                $protocolClass = "protocol-other";

                                if (strtoupper($packet["protocol"]) === "TCP") {
                                    $protocolClass = "protocol-tcp";
                                } elseif (strtoupper($packet["protocol"]) === "UDP") {
                                    $protocolClass = "protocol-udp";
                                }

                                ?>

                                <span class="protocol <?= $protocolClass ?>">

                                    <?= htmlspecialchars($packet["protocol"]) ?>

                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars($packet["source_port"] ?? "-") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["destination_port"] ?? "-") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["packet_size"] ?? "-") ?>
                                bytes
                            </td>

                            <td>
                                <?= htmlspecialchars($packet["packet_info"] ?? "-") ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            style="text-align:center; padding:30px;"
                        >
                            No packet capture logs recorded.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>

</html>