<?php

require_once "../auth.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| HONEYPOT CONFIGURATION
|--------------------------------------------------------------------------
|
| This page currently displays the configuration of the Trap&Trace
| monitoring environment. The values are local demonstration settings.
|
*/

$honeypotName = "KV Enterprise";
$honeypotStatus = "Active";
$honeypotType = "Web Application Honeypot";
$monitoringStatus = "Enabled";
$logCollection = "Enabled";
$alertMonitoring = "Enabled";
$databaseStatus = "Connected";
$environment = "Local XAMPP Server";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Honeypot Configuration | Trap&Trace</title>

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
           STATUS CARD
        ========================= */

        .status-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-header h2 {
            font-size: 18px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 20px;
            background: #dcfce7;
            color: #15803d;
            font-size: 12px;
            font-weight: bold;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #16a34a;
            border-radius: 50%;
        }

        /* =========================
           CONFIGURATION GRID
        ========================= */

        .config-grid {
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
           CONFIGURATION ROWS
        ========================= */

        .config-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
            gap: 20px;
        }

        .config-row:last-child {
            border-bottom: none;
        }

        .config-label {
            color: #6b7280;
            font-size: 13px;
        }

        .config-value {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        /* =========================
           ENABLED BADGE
        ========================= */

        .enabled {
            color: #15803d;
            background: #dcfce7;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
        }

        .connected {
            color: #15803d;
            background: #dcfce7;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
        }

        /* =========================
           INFORMATION BOX
        ========================= */

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
            line-height: 1.6;
            color: #4b5563;
            font-size: 13px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .config-grid {
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

            .status-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
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
            <a href="../dashboard.php">Dashboard</a>
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
            <a href="analytics.php">Analytics</a>
        </li>

        <li>
            <a href="reports.php">Reports</a>
        </li>

        <li>
            <a href="settings.php" class="active">Honeypot Configuration</a>
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
                Honeypot Configuration
            </h1>

            <p>
                Monitoring and honeypot environment settings
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
         HONEYPOT STATUS
    ========================= -->

    <div class="status-card">

        <div class="status-header">

            <h2>
                Honeypot Status
            </h2>

            <div class="status-badge">

                <span class="status-dot"></span>

                <?= htmlspecialchars($honeypotStatus) ?>

            </div>

        </div>

    </div>

    <!-- =========================
         CONFIGURATION
    ========================= -->

    <div class="config-grid">

        <!-- HONEYPOT INFORMATION -->

        <div class="panel">

            <h2>
                Honeypot Information
            </h2>

            <div class="config-row">

                <span class="config-label">
                    Honeypot Name
                </span>

                <span class="config-value">
                    <?= htmlspecialchars($honeypotName) ?>
                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Honeypot Type
                </span>

                <span class="config-value">
                    <?= htmlspecialchars($honeypotType) ?>
                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Environment
                </span>

                <span class="config-value">
                    <?= htmlspecialchars($environment) ?>
                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Status
                </span>

                <span class="config-value">

                    <span class="enabled">
                        <?= htmlspecialchars($honeypotStatus) ?>
                    </span>

                </span>

            </div>

        </div>

        <!-- MONITORING CONFIGURATION -->

        <div class="panel">

            <h2>
                Monitoring Configuration
            </h2>

            <div class="config-row">

                <span class="config-label">
                    Monitoring
                </span>

                <span class="config-value">

                    <span class="enabled">
                        <?= htmlspecialchars($monitoringStatus) ?>
                    </span>

                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Log Collection
                </span>

                <span class="config-value">

                    <span class="enabled">
                        <?= htmlspecialchars($logCollection) ?>
                    </span>

                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Alert Monitoring
                </span>

                <span class="config-value">

                    <span class="enabled">
                        <?= htmlspecialchars($alertMonitoring) ?>
                    </span>

                </span>

            </div>

            <div class="config-row">

                <span class="config-label">
                    Database
                </span>

                <span class="config-value">

                    <span class="connected">
                        <?= htmlspecialchars($databaseStatus) ?>
                    </span>

                </span>

            </div>

        </div>

    </div>

    <!-- =========================
         SYSTEM INFORMATION
    ========================= -->

    <div class="panel">

        <h2>
            System Information
        </h2>

        <div class="info-box">

            Trap&Trace is currently configured to monitor the
            <strong>KV Enterprise</strong> web application honeypot.

            Detected suspicious activities can be recorded in the
            attack events database and displayed through the
            Monitoring, Alerts, Attackers, Analytics, and Reports
            sections of the system.

        </div>

    </div>

</div>

</body>

</html>