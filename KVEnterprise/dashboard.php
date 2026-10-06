<?php

session_name("KVENTERPRISE_SESSION");
session_start();

if (!isset($_SESSION["kv_logged_in"]) || $_SESSION["kv_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | KV Enterprise</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f4f6f8;
            color: #1f2937;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 230px;
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
            color: #3b82f6;
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

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .logout {
            margin-top: 25px;
        }

        .logout a {
            color: #fca5a5;
        }

        /* MAIN */

        .main {
            margin-left: 230px;
            padding: 35px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
        }

        /* CONTENT */

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .panel {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .panel h2 {
            font-size: 18px;
            margin-bottom: 18px;
        }

        .inventory-row {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .inventory-row:last-child {
            border-bottom: none;
        }

        .stock {
            font-weight: bold;
        }

        .normal {
            color: #16a34a;
        }

        .low {
            color: #d97706;
        }

        .out {
            color: #dc2626;
        }

        .notice {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 15px;
            border-radius: 6px;
            color: #374151;
            font-size: 13px;
            line-height: 1.6;
        }

        /* RESPONSIVE */

        @media (max-width: 1000px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
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

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        KV<span>Enterprise</span>
    </div>

    <div class="menu-title">
        Main
    </div>

    <a href="dashboard.php" class="active">
        Dashboard
    </a>

    <a href="inventory.php">
        Inventory
    </a>

    <a href="admin.php">
        Administration
    </a>

    <div class="menu-title">
        Account
    </div>

    <div class="logout">

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>

<!-- MAIN CONTENT -->

<div class="main">

    <div class="header">

        <h1>
            Dashboard
        </h1>

        <p>
            KV Enterprise Inventory Management
        </p>

    </div>

    <!-- SUMMARY CARDS -->

    <div class="cards">

        <div class="card">

            <div class="card-title">
                Total Products
            </div>

            <div class="card-value">
                24
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                In Stock
            </div>

            <div class="card-value">
                19
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                Low Stock
            </div>

            <div class="card-value">
                3
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                Out of Stock
            </div>

            <div class="card-value">
                2
            </div>

        </div>

    </div>

    <!-- CONTENT -->

    <div class="content-grid">

        <div class="panel">

            <h2>
                Recent Inventory
            </h2>

            <div class="inventory-row">

                <span>
                    Premium Persian Cat Food
                </span>

                <span class="stock normal">
                    24 units
                </span>

            </div>

            <div class="inventory-row">

                <span>
                    Indoor Adult Cat Food
                </span>

                <span class="stock normal">
                    19 units
                </span>

            </div>

            <div class="inventory-row">

                <span>
                    Kitten Growth Formula
                </span>

                <span class="stock normal">
                    15 units
                </span>

            </div>

            <div class="inventory-row">

                <span>
                    Sensitive Stomach Formula
                </span>

                <span class="stock low">
                    5 units
                </span>

            </div>

            <div class="inventory-row">

                <span>
                    Chicken Adult Formula
                </span>

                <span class="stock out">
                    0 units
                </span>

            </div>

        </div>

        <div class="panel">

            <h2>
                System Notice
            </h2>

            <div class="notice">

                KV Enterprise is a local demonstration
                application used as the web honeypot for
                Trap&Trace.

                <br><br>

                Suspicious access attempts and login
                activities may be recorded and analyzed
                by the Trap&Trace monitoring system.

            </div>

        </div>

    </div>

</div>

</body>

</html>