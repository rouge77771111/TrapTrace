<?php

require_once "../auth.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| ANALYTICS DATA
|--------------------------------------------------------------------------
*/

// Total attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
");

$totalAttacks = (int) $stmt->fetchColumn();


// Unique attackers
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT ip_address)
    FROM attack_events
");

$uniqueAttackers = (int) $stmt->fetchColumn();


// Critical attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'Critical'
");

$criticalAttacks = (int) $stmt->fetchColumn();


// High-risk attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'High'
");

$highAttacks = (int) $stmt->fetchColumn();


// Medium-risk attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'Medium'
");

$mediumAttacks = (int) $stmt->fetchColumn();


// Low-risk attacks
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM attack_events
    WHERE risk_level = 'Low'
");

$lowAttacks = (int) $stmt->fetchColumn();


// Attack type statistics
$stmt = $pdo->query("
    SELECT
        attack_type,
        COUNT(*) AS total
    FROM attack_events
    GROUP BY attack_type
    ORDER BY total DESC
");

$attackTypes = $stmt->fetchAll();


// Top attacker IP addresses
$stmt = $pdo->query("
    SELECT
        ip_address,
        COUNT(*) AS total_events
    FROM attack_events
    GROUP BY ip_address
    ORDER BY total_events DESC
    LIMIT 10
");

$attackerStats = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Analytics | Trap&Trace</title>

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


        /* =========================
           ANALYTICS PANELS
        ========================= */

        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        }

        .panel h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }


        /* =========================
           STATISTICS BARS
        ========================= */

        .stat-row {
            margin-bottom: 18px;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
            font-size: 13px;
        }

        .bar {
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #dc2626;
            border-radius: 20px;
        }

        .orange {
            background: #ea580c;
        }

        .yellow {
            background: #d97706;
        }

        .green {
            background: #16a34a;
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
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .section-grid {
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

<aside class="sidebar">

    <div class="sidebar-logo">
        <h2>Trap<span>&</span>Trace</h2>
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="/dashboard.php">Dashboard</a>
        </li>

        <li>
            <a href="monitoring.php">Monitoring</a>
        </li>

        <li>
            <a href="alerts.php">Alerts</a>
        </li>

        <li>
            <a href="packet_logs.php">Packet Logs</a>
        </li>

        <li>
            <a href="attackers.php">Attackers</a>
        </li>

        <li>
            <a href="logs.php">Logs</a>
        </li>

        <li>
            <a href="analytics.php" class="active">Analytics</a>
        </li>

        <li>
            <a href="reports.php">Reports</a>
        </li>

        <li>
            <a href="settings.php">Honeypot Configuration</a>
        </li>

        <li class="logout">
            <a href="../logout.php">Logout</a>
        </li>

    </ul>

</aside>

<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main">


    <!-- TOP BAR -->

    <div class="topbar">


        <div class="page-title">

            <h1>
                Analytics
            </h1>

            <p>
                Overview of attack patterns and security activity
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
                High-Risk Attacks
            </div>

            <div class="card-value high">
                <?= $highAttacks ?>
            </div>

        </div>

    </div>


    <!-- =========================
         ANALYTICS
    ========================= -->

    <div class="section-grid">


        <!-- RISK LEVEL DISTRIBUTION -->

        <div class="panel">

            <h2>
                Risk Level Distribution
            </h2>


            <?php

            $riskData = [

                [
                    "name" => "Critical",
                    "value" => $criticalAttacks,
                    "class" => ""
                ],

                [
                    "name" => "High",
                    "value" => $highAttacks,
                    "class" => "orange"
                ],

                [
                    "name" => "Medium",
                    "value" => $mediumAttacks,
                    "class" => "yellow"
                ],

                [
                    "name" => "Low",
                    "value" => $lowAttacks,
                    "class" => "green"
                ]

            ];


            foreach ($riskData as $risk):

                $percentage = $totalAttacks > 0
                    ? ($risk["value"] / $totalAttacks) * 100
                    : 0;

            ?>


                <div class="stat-row">


                    <div class="stat-header">

                        <span>
                            <?= htmlspecialchars($risk["name"]) ?>
                        </span>

                        <strong>
                            <?= $risk["value"] ?>
                        </strong>

                    </div>


                    <div class="bar">

                        <div
                            class="bar-fill <?= $risk["class"] ?>"
                            style="width: <?= $percentage ?>%;"
                        ></div>

                    </div>


                </div>


            <?php endforeach; ?>


        </div>


        <!-- ATTACK TYPES -->

        <div class="panel">

            <h2>
                Attack Types
            </h2>


            <?php if (count($attackTypes) > 0): ?>


                <?php foreach ($attackTypes as $attack): ?>


                    <?php

                    $percentage = $totalAttacks > 0
                        ? ($attack["total"] / $totalAttacks) * 100
                        : 0;

                    ?>


                    <div class="stat-row">


                        <div class="stat-header">

                            <span>
                                <?= htmlspecialchars($attack["attack_type"]) ?>
                            </span>

                            <strong>
                                <?= $attack["total"] ?>
                            </strong>

                        </div>


                        <div class="bar">

                            <div
                                class="bar-fill"
                                style="width: <?= $percentage ?>%;"
                            ></div>

                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <p style="color:#6b7280;">
                    No attack data available.
                </p>


            <?php endif; ?>


        </div>


    </div>


    <!-- =========================
         TOP ATTACKERS
    ========================= -->

    <div class="panel">

        <h2>
            Top Attacker IP Addresses
        </h2>


        <div class="table-container">


            <table>


                <thead>

                    <tr>

                        <th>
                            IP Address
                        </th>

                        <th>
                            Detected Events
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php if (count($attackerStats) > 0): ?>


                        <?php foreach ($attackerStats as $attacker): ?>


                            <tr>

                                <td>
                                    <?= htmlspecialchars($attacker["ip_address"]) ?>
                                </td>

                                <td>
                                    <?= $attacker["total_events"] ?>
                                </td>

                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="2">
                                No attacker data available.
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