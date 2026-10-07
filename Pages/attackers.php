<?php

require_once "../auth.php";
require_once "../config/database.php";

/* =========================
   ATTACKER STATISTICS
========================= */

$uniqueAttackers = $pdo
    ->query("
        SELECT COUNT(DISTINCT ip_address)
        FROM attack_events
    ")
    ->fetchColumn();

$totalEvents = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
    ")
    ->fetchColumn();

$criticalAttackers = $pdo
    ->query("
        SELECT COUNT(DISTINCT ip_address)
        FROM attack_events
        WHERE risk_level = 'Critical'
    ")
    ->fetchColumn();

/* =========================
   ATTACKER IP DATA
========================= */

$attackers = $pdo
    ->query("
        SELECT
            ip_address,
            COUNT(*) AS events,
            COUNT(DISTINCT attack_type) AS attack_types,
            MAX(created_at) AS last_activity,
            CASE
                WHEN SUM(risk_level = 'Critical') > 0
                    THEN 'Critical'
                WHEN SUM(risk_level = 'High') > 0
                    THEN 'High'
                WHEN SUM(risk_level = 'Medium') > 0
                    THEN 'Medium'
                ELSE 'Low'
            END AS highest_risk
        FROM attack_events
        GROUP BY ip_address
        ORDER BY events DESC, last_activity DESC
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

    <title>Trap&Trace - Attackers</title>

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

        .critical {
            color: #dc2626;
        }

        /* =========================
           ATTACKERS TABLE
        ========================= */

        .attackers-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .attackers-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .attackers-header h2 {
            font-size: 20px;
        }

        .attackers-header span {
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

        .ip-address {
            font-weight: 600;
            color: #2563eb;
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
            <a href="packet_logs.php">
                Packet Logs
            </a>
        </li>

        <li>
            <a
                href="attackers.php"
                class="active"
            >
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
            Attacker IP Addresses
        </h1>

        <p>
            IP addresses associated with detected attacks
        </p>

    </header>


    <!-- =========================
         STATISTICS
    ========================== -->

    <section class="stats-grid">

        <div class="stat-card">

            <h3>
                Unique Attackers
            </h3>

            <div class="stat-number">
                <?= $uniqueAttackers ?>
            </div>

        </div>


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
                Critical-Risk Attackers
            </h3>

            <div class="stat-number critical">
                <?= $criticalAttackers ?>
            </div>

        </div>

    </section>


    <!-- =========================
         ATTACKER TABLE
    ========================== -->

    <section class="attackers-card">

        <div class="attackers-header">

            <h2>
                Detected Attacker IPs
            </h2>

            <span>
                Grouped by IP address
            </span>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            IP Address
                        </th>

                        <th>
                            Events
                        </th>

                        <th>
                            Attack Types
                        </th>

                        <th>
                            Last Activity
                        </th>

                        <th>
                            Highest Risk
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($attackers) > 0): ?>

                    <?php foreach ($attackers as $attacker): ?>

                        <tr>

                            <td>

                                <span class="ip-address">

                                    <?= htmlspecialchars($attacker["ip_address"]) ?>

                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars($attacker["events"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($attacker["attack_types"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($attacker["last_activity"]) ?>
                            </td>

                            <td>

                                <?php

                                $riskClass = "risk-low";

                                if ($attacker["highest_risk"] === "Critical") {
                                    $riskClass = "risk-critical";
                                } elseif ($attacker["highest_risk"] === "High") {
                                    $riskClass = "risk-high";
                                } elseif ($attacker["highest_risk"] === "Medium") {
                                    $riskClass = "risk-medium";
                                }

                                ?>

                                <span class="risk <?= $riskClass ?>">

                                    <?= htmlspecialchars($attacker["highest_risk"]) ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="5"
                            style="text-align:center; padding:30px;"
                        >
                            No attacker IP addresses recorded.

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