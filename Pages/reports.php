<?php

require_once "../auth.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| REPORT DATA
|--------------------------------------------------------------------------
*/

// Total attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
");

$totalAttacks = (int) $stmt->fetchColumn();


// Critical attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'Critical'
");

$criticalAttacks = (int) $stmt->fetchColumn();


// High attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'High'
");

$highAttacks = (int) $stmt->fetchColumn();


// Medium attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'Medium'
");

$mediumAttacks = (int) $stmt->fetchColumn();


// Unique attackers
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT ip_address)
    FROM attack_events
");

$uniqueAttackers = (int) $stmt->fetchColumn();


// Detected attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE status = 'Detected'
");

$detectedAttacks = (int) $stmt->fetchColumn();


// Investigating attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE status = 'Investigating'
");

$investigatingAttacks = (int) $stmt->fetchColumn();


// Resolved attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE status = 'Resolved'
");

$resolvedAttacks = (int) $stmt->fetchColumn();


// Attack type report
$stmt = $pdo->query("
    SELECT
        attack_type,
        COUNT(*) AS total
    FROM attack_events
    GROUP BY attack_type
    ORDER BY total DESC
");

$attackTypes = $stmt->fetchAll();


// Recent attack report
$stmt = $pdo->query("
    SELECT
        ip_address,
        username,
        attack_type,
        risk_level,
        status,
        created_at
    FROM attack_events
    ORDER BY created_at DESC
    LIMIT 20
");

$recentAttacks = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reports | Trap&Trace</title>

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


        .logo {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 12px;
        }


        .logo span {
            color: #ef4444;
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


        .sidebar a:hover {
            background: #1f2937;
            color: white;
        }


        .sidebar a.active {
            background: #dc2626;
            color: white;
        }


        .logout {
            margin-top: 25px;
            border-top: 1px solid #374151;
            padding-top: 15px;
        }


        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 240px;
            padding: 30px;
        }


        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
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


        /* =========================
           SUMMARY CARDS
        ========================= */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }


        .card {
            background: white;
            border-radius: 10px;
            padding: 22px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        }


        .card-title {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }


        .card-value {
            font-size: 30px;
            font-weight: bold;
        }


        .critical {
            color: #dc2626;
        }


        .high {
            color: #ea580c;
        }


        .green {
            color: #16a34a;
        }


        /* =========================
           REPORT PANELS
        ========================= */

        .panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        }


        .panel h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }


        .report-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }


        /* =========================
           REPORT ITEMS
        ========================= */

        .report-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }


        .report-item:last-child {
            border-bottom: none;
        }


        .report-label {
            color: #4b5563;
            font-size: 14px;
        }


        .report-number {
            font-size: 18px;
            font-weight: bold;
        }


        /* =========================
           TABLE
        ========================= */

        .table-container {
            overflow-x: auto;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }


        th {
            text-align: left;
            padding: 12px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }


        td {
            padding: 13px 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
        }


        /* =========================
           BADGES
        ========================= */

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }


        .badge-critical {
            background: #fee2e2;
            color: #b91c1c;
        }


        .badge-high {
            background: #ffedd5;
            color: #c2410c;
        }


        .badge-medium {
            background: #fef3c7;
            color: #b45309;
        }


        .badge-low {
            background: #dcfce7;
            color: #15803d;
        }


        .badge-detected {
            background: #fee2e2;
            color: #b91c1c;
        }


        .badge-investigating {
            background: #fef3c7;
            color: #b45309;
        }


        .badge-resolved {
            background: #dcfce7;
            color: #15803d;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }


            .report-grid {
                grid-template-columns: 1fr;
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


            .cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

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


    <a href="/Pages/packet_logs.php">
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


    <a href="/Pages/reports.php" class="active">
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


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main">


    <!-- TOP BAR -->

    <div class="topbar">


        <div class="page-title">

            <h1>
                Attack Reports
            </h1>

            <p>
                Summarized information about detected security events
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


    <!-- =========================
         SUMMARY CARDS
    ========================= -->

    <div class="cards">


        <div class="card">

            <div class="card-title">
                Total Attacks
            </div>

            <div class="card-value">
                <?= $totalAttacks ?>
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Unique Attackers
            </div>

            <div class="card-value">
                <?= $uniqueAttackers ?>
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Critical Attacks
            </div>

            <div class="card-value critical">
                <?= $criticalAttacks ?>
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Resolved Attacks
            </div>

            <div class="card-value green">
                <?= $resolvedAttacks ?>
            </div>

        </div>


    </div>


    <!-- =========================
         REPORT SUMMARY
    ========================= -->

    <div class="report-grid">


        <!-- RISK SUMMARY -->

        <div class="panel">

            <h2>
                Risk Level Summary
            </h2>


            <div class="report-item">

                <span class="report-label">
                    Critical
                </span>

                <span class="report-number critical">
                    <?= $criticalAttacks ?>
                </span>

            </div>


            <div class="report-item">

                <span class="report-label">
                    High
                </span>

                <span class="report-number high">
                    <?= $highAttacks ?>
                </span>

            </div>


            <div class="report-item">

                <span class="report-label">
                    Medium
                </span>

                <span class="report-number">
                    <?= $mediumAttacks ?>
                </span>

            </div>


        </div>


        <!-- STATUS SUMMARY -->

        <div class="panel">

            <h2>
                Attack Status Summary
            </h2>


            <div class="report-item">

                <span class="report-label">
                    Detected
                </span>

                <span class="report-number critical">
                    <?= $detectedAttacks ?>
                </span>

            </div>


            <div class="report-item">

                <span class="report-label">
                    Investigating
                </span>

                <span class="report-number">
                    <?= $investigatingAttacks ?>
                </span>

            </div>


            <div class="report-item">

                <span class="report-label">
                    Resolved
                </span>

                <span class="report-number green">
                    <?= $resolvedAttacks ?>
                </span>

            </div>


        </div>


    </div>


    <!-- =========================
         ATTACK TYPE REPORT
    ========================= -->

    <div class="panel">

        <h2>
            Attack Type Summary
        </h2>


        <div class="table-container">


            <table>


                <thead>

                    <tr>

                        <th>
                            Attack Type
                        </th>

                        <th>
                            Number of Events
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php if (count($attackTypes) > 0): ?>


                        <?php foreach ($attackTypes as $attack): ?>


                            <tr>

                                <td>
                                    <?= htmlspecialchars($attack["attack_type"]) ?>
                                </td>

                                <td>
                                    <?= $attack["total"] ?>
                                </td>

                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="2">
                                No attack type data available.
                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>


            </table>


        </div>


    </div>


    <!-- =========================
         RECENT ATTACK REPORT
    ========================= -->

    <div class="panel">

        <h2>
            Recent Attack Events
        </h2>


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


                    <?php if (count($recentAttacks) > 0): ?>


                        <?php foreach ($recentAttacks as $attack): ?>


                            <?php

                            $riskClass = match ($attack["risk_level"]) {

                                "Critical" => "badge-critical",

                                "High" => "badge-high",

                                "Medium" => "badge-medium",

                                "Low" => "badge-low",

                                default => ""

                            };


                            $statusClass = match ($attack["status"]) {

                                "Detected" => "badge-detected",

                                "Investigating" => "badge-investigating",

                                "Resolved" => "badge-resolved",

                                default => ""

                            };

                            ?>


                            <tr>


                                <td>
                                    <?= htmlspecialchars($attack["created_at"]) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($attack["ip_address"]) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($attack["username"] ?? "-") ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($attack["attack_type"]) ?>
                                </td>


                                <td>

                                    <span class="badge <?= $riskClass ?>">
                                        <?= htmlspecialchars($attack["risk_level"]) ?>
                                    </span>

                                </td>


                                <td>

                                    <span class="badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($attack["status"]) ?>
                                    </span>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="6">
                                No attack events available.
                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>


            </table>


        </div>


    </div>


</div>


</body>

</html>