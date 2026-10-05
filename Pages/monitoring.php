<?php

require_once "../auth.php";
require_once "../config/database.php";

/* =========================
   MONITORING STATISTICS
========================= */

$totalEvents = $pdo
    ->query("SELECT COUNT(*) FROM attack_events")
    ->fetchColumn();

$uniqueIPs = $pdo
    ->query("SELECT COUNT(DISTINCT ip_address) FROM attack_events")
    ->fetchColumn();

$highRisk = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE risk_level = 'High'
    ")
    ->fetchColumn();

$criticalRisk = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE risk_level = 'Critical'
    ")
    ->fetchColumn();

/* =========================
   LATEST EVENTS
========================= */

$events = $pdo
    ->query("
        SELECT *
        FROM attack_events
        ORDER BY created_at DESC
        LIMIT 50
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

    <title>Trap&Trace - Monitoring</title>

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

        .monitor-status {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            color: #15803d;
            font-size: 14px;
            font-weight: 600;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #22c55e;
        }

        /* =========================
           STATISTICS
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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

        .critical {
            color: #dc2626;
        }

        .high {
            color: #ea580c;
        }

        /* =========================
           EVENTS CARD
        ========================= */

        .events-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .events-header h2 {
            font-size: 20px;
        }

        .events-header span {
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

        /* =========================
           RISK BADGES
        ========================= */

        .risk {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .risk-critical {
            background: #fee2e2;
            color: #b91c1c;
        }

        .risk-high {
            background: #ffedd5;
            color: #c2410c;
        }

        .risk-medium {
            background: #fef3c7;
            color: #a16207;
        }

        .risk-low {
            background: #dcfce7;
            color: #15803d;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
            <a
                href="monitoring.php"
                class="active"
            >
                Monitoring
            </a>
        </li>

        <li>
            <a href="alerts.php">
                Alerts
            </a>
        </li>

        <li>
            <a href="packet_logs.php">
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

        <div>

            <h1>
                Live Monitoring
            </h1>

            <p>
                Honeypot activity and threat detection
            </p>

        </div>

        <div class="monitor-status">

            <span class="status-dot"></span>

            Monitoring Active

        </div>

    </header>


    <!-- =========================
         STATISTICS
    ========================== -->

    <section class="stats-grid">

        <div class="stat-card">

            <h3>
                Total Detected Events
            </h3>

            <div class="stat-number">
                <?= $totalEvents ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Unique IP Addresses
            </h3>

            <div class="stat-number">
                <?= $uniqueIPs ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                High-Risk Events
            </h3>

            <div class="stat-number high">
                <?= $highRisk ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Critical Events
            </h3>

            <div class="stat-number critical">
                <?= $criticalRisk ?>
            </div>

        </div>

    </section>


    <!-- =========================
         LIVE EVENTS
    ========================== -->

    <section class="events-card">

        <div class="events-header">

            <h2>
                Live Honeypot Activity
            </h2>

            <span>
                Latest 50 events
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
                            IP Address
                        </th>

                        <th>
                            Username
                        </th>

                        <th>
                            Attack Type
                        </th>

                        <th>
                            Activity
                        </th>

                        <th>
                            Risk
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($events) > 0): ?>

                    <?php foreach ($events as $event): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($event["created_at"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event["ip_address"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event["username"] ?? "-") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event["attack_type"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($event["activity"]) ?>
                            </td>

                            <td>

                                <?php

                                $riskClass = "risk-low";

                                if ($event["risk_level"] === "Critical") {
                                    $riskClass = "risk-critical";
                                } elseif ($event["risk_level"] === "High") {
                                    $riskClass = "risk-high";
                                } elseif ($event["risk_level"] === "Medium") {
                                    $riskClass = "risk-medium";
                                }

                                ?>

                                <span class="risk <?= $riskClass ?>">

                                    <?= htmlspecialchars($event["risk_level"]) ?>

                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars($event["status"]) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="7"
                            style="text-align:center; padding:30px;"
                        >
                            No monitoring events recorded.

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