<?php

require_once "../auth.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Trap&Trace - Alerts</title>

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
           ALERT STATISTICS
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
        }

        .total {
            color: #2563eb;
        }

        .critical {
            color: #dc2626;
        }

        .high {
            color: #ea580c;
        }

        .medium {
            color: #ca8a04;
        }

        /* =========================
           ALERT TABLE
        ========================= */

        .alerts-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .alerts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .alerts-header h2 {
            font-size: 20px;
        }

        .alerts-header span {
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

        /* =========================
           STATUS
        ========================= */

        .status {
            color: #2563eb;
            font-weight: 600;
        }

        .error-message {
            text-align: center;
            padding: 30px;
            color: #b91c1c;
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
            <a href="monitoring.php">
                Monitoring
            </a>
        </li>

        <li>
            <a
                href="alerts.php"
                class="active"
            >
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

        <h1>
            Security Alerts
        </h1>

        <p>
            Detected threats requiring attention
        </p>

    </header>


    <!-- =========================
         ALERT STATISTICS
    ========================== -->

    <section class="stats-grid">

        <div class="stat-card">

            <h3>
                Total Alerts
            </h3>

            <div
                class="stat-number total"
                id="totalAlerts"
            >
                0
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Critical Alerts
            </h3>

            <div
                class="stat-number critical"
                id="criticalAlerts"
            >
                0
            </div>

        </div>


        <div class="stat-card">

            <h3>
                High Alerts
            </h3>

            <div
                class="stat-number high"
                id="highAlerts"
            >
                0
            </div>

        </div>


        <div class="stat-card">

            <h3>
                Medium Alerts
            </h3>

            <div
                class="stat-number medium"
                id="mediumAlerts"
            >
                0
            </div>

        </div>

    </section>


    <!-- =========================
         ALERT TABLE
    ========================== -->

    <section class="alerts-card">

        <div class="alerts-header">

            <h2>
                Detected Security Alerts
            </h2>

            <span>
                Retrieved from REST API
            </span>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Time</th>

                        <th>IP Address</th>

                        <th>Username</th>

                        <th>Attack Type</th>

                        <th>Activity</th>

                        <th>Risk</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody id="alertsTable">

                    <tr>

                        <td
                            colspan="7"
                            style="text-align:center; padding:30px;"
                        >
                            Loading alerts...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>

</main>


<script>

async function loadAlerts() {

    try {

        const response = await fetch("../api/alerts.php", {
            method: "GET",
            credentials: "same-origin",
            cache: "no-store"
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error("API request failed.");
        }

        const alerts = data.alerts || [];

        /* =========================
           STATISTICS
        ========================= */

        document.getElementById("totalAlerts").textContent =
            alerts.length;

        document.getElementById("criticalAlerts").textContent =
            alerts.filter(
                alert => alert.risk_level === "Critical"
            ).length;

        document.getElementById("highAlerts").textContent =
            alerts.filter(
                alert => alert.risk_level === "High"
            ).length;

        document.getElementById("mediumAlerts").textContent =
            alerts.filter(
                alert => alert.risk_level === "Medium"
            ).length;


        /* =========================
           TABLE
        ========================= */

        const table = document.getElementById("alertsTable");

        table.innerHTML = "";

        if (alerts.length === 0) {

            table.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        style="text-align:center; padding:30px;"
                    >
                        No security alerts recorded.
                    </td>
                </tr>
            `;

            return;
        }


        alerts.forEach(alert => {

            let riskClass = "risk-medium";

            if (alert.risk_level === "Critical") {
                riskClass = "risk-critical";
            }

            if (alert.risk_level === "High") {
                riskClass = "risk-high";
            }

            const row = document.createElement("tr");

            row.innerHTML = `

                <td>
                    ${escapeHtml(alert.created_at)}
                </td>

                <td>
                    ${escapeHtml(alert.ip_address)}
                </td>

                <td>
                    ${escapeHtml(alert.username || "-")}
                </td>

                <td>
                    ${escapeHtml(alert.attack_type)}
                </td>

                <td>
                    ${escapeHtml(alert.activity)}
                </td>

                <td>

                    <span class="risk ${riskClass}">
                        ${escapeHtml(alert.risk_level)}
                    </span>

                </td>

                <td>

                    <span class="status">
                        ${escapeHtml(alert.status)}
                    </span>

                </td>

            `;

            table.appendChild(row);

        });

    } catch (error) {

        console.error(error);

        document.getElementById("alertsTable").innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="error-message"
                >
                    Unable to retrieve alerts from the API.
                </td>
            </tr>
        `;

    }

}


/* =========================
   HTML ESCAPE
========================= */

function escapeHtml(value) {

    const div = document.createElement("div");

    div.textContent = value ?? "";

    return div.innerHTML;

}


/* =========================
   INITIAL LOAD
========================= */

loadAlerts();


/* =========================
   AUTO REFRESH
========================= */

setInterval(loadAlerts, 5000);

</script>

</body>

</html>