<?php

require_once "auth.php";
require_once "config/database.php";

/* =========================
   DASHBOARD STATISTICS
========================= */

$totalAttacks = $pdo
    ->query("SELECT COUNT(*) FROM attack_events")
    ->fetchColumn();

$uniqueAttackers = $pdo
    ->query("SELECT COUNT(DISTINCT ip_address) FROM attack_events")
    ->fetchColumn();

$criticalAttacks = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE risk_level = 'Critical'
    ")
    ->fetchColumn();

$highAttacks = $pdo
    ->query("
        SELECT COUNT(*)
        FROM attack_events
        WHERE risk_level = 'High'
    ")
    ->fetchColumn();

/* =========================
   RECENT ATTACK EVENTS
========================= */

$recentEvents = $pdo
    ->query("
        SELECT *
        FROM attack_events
        ORDER BY created_at DESC
        LIMIT 8
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

    <title>Trap&Trace - Dashboard</title>

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
           LAYOUT
        ========================= */

        .dashboard-layout {
            min-height: 100vh;
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
           STAT CARDS
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            color: #6b7280;
            font-size: 13px;
            font-weight: normal;
            margin-bottom: 12px;
        }

        .stat-card .number {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .stat-card.critical .number {
            color: #dc2626;
        }

        .stat-card.high .number {
            color: #ea580c;
        }

        /* =========================
           RECENT EVENTS
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
            color: #111827;
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

        tr:last-child td {
            border-bottom: none;
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
           STATUS
        ========================= */

        .status {
            color: #2563eb;
            font-weight: 600;
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

            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
    ========================== -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <h2>
                Trap<span>&</span>Trace
            </h2>

        </div>

        <ul class="sidebar-menu">

            <li>
                <a
                    href="dashboard.php"
                    class="active"
                >
                    Dashboard
                </a>
            </li>

            <li>
                <a href="Pages/monitoring.php">
                    Monitoring
                </a>
            </li>

            <li>
                <a href="Pages/alerts.php">
                    Alerts
                </a>
            </li>

            <li>
                <a href="Pages/packet_logs.php">
                    Packet Logs
                </a>
            </li>

            <li>
                <a href="Pages/attackers.php">
                    Attackers
                </a>
            </li>

            <li>
                <a href="Pages/logs.php">
                    Logs
                </a>
            </li>

            <li>
                <a href="Pages/analytics.php">
                    Analytics
                </a>
            </li>

            <li>
                <a href="Pages/reports.php">
                    Reports
                </a>
            </li>

            <li>
                <a href="Pages/settings.php">
                    Honeypot Configuration
                </a>
            </li>

            <li class="logout">
                <a href="logout.php">
                    Logout
                </a>
            </li>

        </ul>

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

        <header class="top-header">

            <div>

                <h1>
                    Security Dashboard
                </h1>

                <p>
                    Welcome back,
                    <?= htmlspecialchars($_SESSION["full_name"]) ?>
                </p>

            </div>

            <div class="user-info">

                <?= htmlspecialchars($_SESSION["role"]) ?>

            </div>

        </header>


        <!-- =========================
             STATISTICS
        ========================== -->

        <section class="stats-grid">

            <div class="stat-card">

                <h3>
                    Total Attacks
                </h3>

                <div class="number">
                    <?= $totalAttacks ?>
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Unique Attackers
                </h3>

                <div class="number">
                    <?= $uniqueAttackers ?>
                </div>

            </div>


            <div class="stat-card critical">

                <h3>
                    Critical Attacks
                </h3>

                <div class="number">
                    <?= $criticalAttacks ?>
                </div>

            </div>


            <div class="stat-card high">

                <h3>
                    High-Risk Attacks
                </h3>

                <div class="number">
                    <?= $highAttacks ?>
                </div>

            </div>

        </section>


        <!-- =========================
             RECENT EVENTS
        ========================== -->

        <section class="events-card">

            <div class="events-header">

                <h2>
                    Recent Attack Events
                </h2>

                <span>
                    Latest detected activity
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
                                Risk
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($recentEvents) > 0): ?>

                        <?php foreach ($recentEvents as $event): ?>

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

                                    <span class="status">

                                        <?= htmlspecialchars($event["status"]) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                style="text-align:center; padding:30px;"
                            >
                                No attack events recorded.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>

</html>