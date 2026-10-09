<?php

require_once "../auth.php";
require_once "../config/database.php";

/* =========================
   LOG STATISTICS
========================= */

$totalLogs = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
    ")
    ->fetchColumn();

$detectedLogs = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE status = 'Detected'
    ")
    ->fetchColumn();

$investigatingLogs = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE status = 'Investigating'
    ")
    ->fetchColumn();

$resolvedLogs = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE status = 'Resolved'
    ")
    ->fetchColumn();

/* =========================
   SYSTEM LOGS
========================= */

$logs = $pdo
    ->query("
        SELECT *
        FROM attack_events
        ORDER BY created_at DESC
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

    <title>Trap&Trace - Logs</title>

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

        .detected {
            color: #dc2626;
        }

        .investigating {
            color: #ca8a04;
        }

        .resolved {
            color: #16a34a;
        }

        /* =========================
           LOG TABLE
        ========================= */

        .logs-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .logs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logs-header h2 {
            font-size: 20px;
        }

        .logs-header span {
            color: #6b7280;
            font-size: 13px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
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
           STATUS BADGES
        ========================= */

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-detected {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-investigating {
            background: #fef3c7;
            color: #a16207;
        }

        .status-resolved {
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

        @media (max-width: 700px) {

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
            <a
                href="logs.php"
                class="active"
            >
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
            System Activity Logs
        </h1>

        <p>
            Historical security events recorded by Trap&Trace
        </p>

    </header>


    <!-- =========================
         STATISTICS
    ========================== -->

    <section class="stats-grid">

        <div class="stat-card">

            <h3>
                Total Logs
            </h3>

            <div class="stat-number">
                <?= $totalLogs ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Detected
            </h3>

            <div class="stat-number detected">
                <?= $detectedLogs ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Investigating
            </h3>

            <div class="stat-number investigating">
                <?= $investigatingLogs ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Resolved
            </h3>

            <div class="stat-number resolved">
                <?= $resolvedLogs ?>
            </div>

        </div>

    </section>


    <!-- =========================
         LOG TABLE
    ========================== -->

    <section class="logs-card">

        <div class="logs-header">

            <h2>
                Security Activity History
            </h2>

            <span>
                Latest 100 records
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
                            Command
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

                <?php if (count($logs) > 0): ?>

                    <?php foreach ($logs as $log): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($log["created_at"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["ip_address"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["username"] ?? "-") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["attack_type"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["command"] ?? "-") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($log["activity"]) ?>
                            </td>

                            <td>

                                <?php

                                $riskClass = "risk-low";

                                if ($log["risk_level"] === "Critical") {
                                    $riskClass = "risk-critical";
                                } elseif ($log["risk_level"] === "High") {
                                    $riskClass = "risk-high";
                                } elseif ($log["risk_level"] === "Medium") {
                                    $riskClass = "risk-medium";
                                }

                                ?>

                                <span class="risk <?= $riskClass ?>">

                                    <?= htmlspecialchars($log["risk_level"]) ?>

                                </span>

                            </td>

                            <td>

                                <?php

                                $statusClass = "status-detected";

                                if ($log["status"] === "Investigating") {
                                    $statusClass = "status-investigating";
                                } elseif ($log["status"] === "Resolved") {
                                    $statusClass = "status-resolved";
                                }

                                ?>

                                <span class="status <?= $statusClass ?>">

                                    <?= htmlspecialchars($log["status"]) ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            style="text-align:center; padding:30px;"
                        >
                            No system activity logs recorded.

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