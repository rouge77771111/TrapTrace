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

<div class="sidebar">


    <div class="logo">
        Trap<span>&</span>Trace
    </div>


    <div class="menu-title">
        Main
    </div>


    <a href="/TrapTrace/dashboard.php">
        Dashboard
    </a>


    <a href="/TrapTrace/Pages/monitoring.php">
        Monitoring
    </a>


    <a href="/TrapTrace/Pages/alerts.php">
        Alerts
    </a>


    <a href="/TrapTrace/Pages/packet_logs.php">
        Packet Logs
    </a>


    <a href="/TrapTrace/Pages/attackers.php">
        Attackers
    </a>


    <a href="/TrapTrace/Pages/logs.php">
        Logs
    </a>


    <div class="menu-title">
        Analysis
    </div>


    <a href="/TrapTrace/Pages/analytics.php">
        Analytics
    </a>


    <a href="/TrapTrace/Pages/reports.php">
        Reports
    </a>


    <div class="menu-title">
        System
    </div>


    <a
        href="/TrapTrace/Pages/settings.php"
        class="active"
    >
        Honeypot Configuration
    </a>


    <div class="logout">

        <a href="/TrapTrace/logout.php">
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