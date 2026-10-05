<?php

session_name("KVENTERPRISE_SESSION");
session_start();

if (!isset($_SESSION["kv_logged_in"])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION["kv_username"] ?? "User";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>KV Enterprise | Dashboard</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }


        body {
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
            width: 230px;
            height: 100vh;
            background: #1e293b;
            color: white;
            padding: 25px 15px;
        }


        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 10px;
        }


        .logo span {
            color: #3b82f6;
        }


        .menu-title {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            margin: 20px 10px 10px;
            letter-spacing: 1px;
        }


        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 11px 10px;
            border-radius: 6px;
            margin-bottom: 5px;
            font-size: 14px;
        }


        .sidebar a:hover,
        .sidebar a.active {
            background: #334155;
            color: white;
        }


        /* =========================
           MAIN CONTENT
        ========================= */

        .main {
            margin-left: 230px;
            padding: 30px;
        }


        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }


        .topbar h1 {
            font-size: 26px;
            margin-bottom: 5px;
        }


        .topbar p {
            color: #64748b;
            font-size: 13px;
        }


        .user {
            text-align: right;
            font-size: 13px;
        }


        .user span {
            display: block;
            color: #64748b;
            margin-top: 4px;
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
            padding: 22px;
            border-radius: 9px;
            border: 1px solid #e2e8f0;
        }


        .card-title {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 10px;
        }


        .card-value {
            font-size: 27px;
            font-weight: bold;
        }


        /* =========================
           CONTENT GRID
        ========================= */

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }


        .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 22px;
        }


        .panel h2 {
            font-size: 18px;
            margin-bottom: 18px;
        }


        /* =========================
           INVENTORY ITEMS
        ========================= */

        .product {
            display: flex;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px solid #e5e7eb;
        }


        .product:last-child {
            border-bottom: none;
        }


        .product-name {
            font-size: 14px;
            font-weight: bold;
        }


        .product-stock {
            color: #16a34a;
            font-size: 13px;
        }


        /* =========================
           NOTICE
        ========================= */

        .notice {
            margin-top: 25px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.5;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid {
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

            .topbar {
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
        KV <span>Enterprise</span>
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


    <a href="inventory.php">
        Products
    </a>


    <a href="inventory.php">
        Stock Reports
    </a>


    <div class="menu-title">
        Account
    </div>


    <a href="index.php">
        Logout
    </a>


</div>


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main">


    <!-- TOP BAR -->

    <div class="topbar">


        <div>

            <h1>
                Inventory Dashboard
            </h1>

            <p>
                KV Enterprise Cat Food Inventory Management
            </p>

        </div>


        <div class="user">

            <strong>
                <?= htmlspecialchars($username) ?>
            </strong>

            <span>
                Administrator
            </span>

        </div>


    </div>


    <!-- =========================
         SUMMARY CARDS
    ========================= -->

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


    <!-- =========================
         CONTENT
    ========================= -->

    <div class="grid">


        <!-- INVENTORY -->

        <div class="panel">


            <h2>
                Cat Food Inventory
            </h2>


            <div class="product">

                <div class="product-name">
                    Premium Persian Cat Food
                </div>

                <div class="product-stock">
                    120 units
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    Indoor Adult Cat Food
                </div>

                <div class="product-stock">
                    85 units
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    Kitten Growth Formula
                </div>

                <div class="product-stock">
                    64 units
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    Sensitive Stomach Formula
                </div>

                <div class="product-stock">
                    31 units
                </div>

            </div>


        </div>


        <!-- RECENT ACTIVITY -->

        <div class="panel">


            <h2>
                Recent Inventory Activity
            </h2>


            <div class="product">

                <div class="product-name">
                    Stock received
                </div>

                <div>
                    Today
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    Product updated
                </div>

                <div>
                    Today
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    Stock adjustment
                </div>

                <div>
                    Yesterday
                </div>

            </div>


            <div class="product">

                <div class="product-name">
                    New product added
                </div>

                <div>
                    Yesterday
                </div>

            </div>


        </div>


    </div>


    <!-- INFORMATION -->

    <div class="notice">

        <strong>KV Enterprise ERP System</strong><br>

        This is a controlled local honeypot environment.
        Suspicious authentication attempts are monitored and
        recorded by the Trap&Trace security monitoring system.

    </div>


</div>


</body>

</html>