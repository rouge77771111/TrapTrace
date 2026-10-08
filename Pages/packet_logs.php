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
   RECENT PACKETS
========================= */

$packets = $pdo
    ->query("
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

    <title>Packet Capture Logs | Trap&Trace</title>

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


        /* =========================
           HEADER
        ========================= */

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

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

        .user-info {
            background: white;

            padding: 10px 15px;

            border-radius: 8px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

            font-size: 14px;
        }


        /* =========================
           CAPTURE SECTION
        ========================= */

        .capture-section {
            background: white;

            border-radius: 12px;

            padding: 25px;

            margin-bottom: 25px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }

        .capture-info h2 {
            font-size: 20px;
            color: #111827;

            margin-bottom: 6px;
        }

        .capture-info p {
            color: #6b7280;
            font-size: 13px;
        }

        .capture-button {
            border: none;

            padding: 12px 20px;

            background: #2563eb;
            color: white;

            border-radius: 7px;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;
        }

        .capture-button:hover {
            background: #1d4ed8;
        }

        .capture-button:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .capture-message {
            margin-top: 10px;
            font-size: 13px;
        }

        .capture-message.success {
            color: #15803d;
        }

        .capture-message.error {
            color: #b91c1c;
        }


        /* =========================
           STAT CARDS
        ========================= */

        .cards {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }

        .card {
            background: white;

            border-radius: 12px;

            padding: 24px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            color: #6b7280;

            font-size: 13px;

            margin-bottom: 12px;
        }

        .card-value {
            font-size: 30px;
            font-weight: bold;

            color: #111827;
        }

        .blue {
            color: #2563eb;
        }

        .purple {
            color: #7c3aed;
        }


        /* =========================
           TABLE
        ========================= */

        .table-panel {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 20px;
        }

        .table-header h2 {
            font-size: 20px;
            color: #111827;
        }

        .table-header span {
            color: #6b7280;
            font-size: 13px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        tr:last-child td {
            border-bottom: none;
        }


        /* =========================
           PROTOCOL
        ========================= */

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


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .capture-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .top-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }

        }

    </style>

</head>

<body>


<div class="dashboard-layout">


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


        <!-- HEADER -->

        <header class="top-header">

            <div>

                <h1>
                    Packet Capture Logs
                </h1>

                <p>
                    Network packets captured by the honeypot
                </p>

            </div>


            <div class="user-info">

                <?= htmlspecialchars($_SESSION["role"]) ?>

            </div>

        </header>


        <!-- CAPTURE -->

        <section class="capture-section">

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

        </section>


        <!-- STATISTICS -->

        <section class="cards">

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

        </section>


        <!-- PACKET TABLE -->

        <section class="table-panel">

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
                                    <?= htmlspecialchars(
                                        $packet["captured_at"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $packet["source_ip"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $packet["destination_ip"]
                                    ) ?>
                                </td>

                                <td>

                                    <?php

                                    $protocol = strtoupper(
                                        $packet["protocol"] ?? "Unknown"
                                    );

                                    $protocolClass =
                                        strtolower($protocol);

                                    ?>

                                    <span
                                        class="protocol <?= htmlspecialchars(
                                            $protocolClass
                                        ) ?>"
                                    >
                                        <?= htmlspecialchars($protocol) ?>
                                    </span>

                                </td>

                                <td>

                                    <?= $packet["source_port"] !== null
                                        ? htmlspecialchars(
                                            $packet["source_port"]
                                        )
                                        : "-"
                                    ?>

                                </td>

                                <td>

                                    <?= $packet["destination_port"] !== null
                                        ? htmlspecialchars(
                                            $packet["destination_port"]
                                        )
                                        : "-"
                                    ?>

                                </td>

                                <td>

                                    <?= $packet["packet_size"] !== null
                                        ? htmlspecialchars(
                                            $packet["packet_size"]
                                        ) . " bytes"
                                        : "-"
                                    ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $packet["packet_info"]
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


    </main>

</div>


<script>

const captureButton =
    document.getElementById("captureButton");

const captureMessage =
    document.getElementById("captureMessage");


captureButton.addEventListener(
    "click",
    function () {

        captureButton.disabled = true;

        captureButton.textContent =
            "Capturing...";

        captureMessage.className =
            "capture-message";

        captureMessage.textContent = "";


        fetch("../API/capture_packets.php")

            .then(response => response.json())

            .then(data => {

                if (data.success) {

                    let message =
                        "Capture completed. "
                        + data.saved_packets
                        + " packets saved.";

                    if (
                        data.port_scan_detections !== undefined
                        &&
                        data.port_scan_detections > 0
                    ) {

                        message +=
                            " "
                            + data.port_scan_detections
                            + " port scanning detection(s) generated.";

                    }

                    captureMessage.className =
                        "capture-message success";

                    captureMessage.textContent =
                        message;


                    setTimeout(
                        function () {

                            window.location.reload();

                        },
                        1500
                    );

                } else {

                    captureMessage.className =
                        "capture-message error";

                    captureMessage.textContent =
                        data.message ||
                        "Packet capture failed.";

                }

            })

            .catch(error => {

                captureMessage.className =
                    "capture-message error";

                captureMessage.textContent =
                    "Unable to connect to the packet capture service.";

            })

            .finally(() => {

                setTimeout(
                    function () {

                        captureButton.disabled =
                            false;

                        captureButton.textContent =
                            "Capture Packets";

                    },
                    1500
                );

            });

    }
);

</script>


</body>

</html>