<?php

session_start();

if (!isset($_SESSION["kv_logged_in"])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION["kv_username"] ?? "User";

$products = [
    [
        "code" => "CF-001",
        "name" => "Premium Persian Cat Food",
        "category" => "Dry Cat Food",
        "stock" => 120,
        "status" => "In Stock"
    ],
    [
        "code" => "CF-002",
        "name" => "Indoor Adult Cat Food",
        "category" => "Dry Cat Food",
        "stock" => 85,
        "status" => "In Stock"
    ],
    [
        "code" => "CF-003",
        "name" => "Kitten Growth Formula",
        "category" => "Kitten Food",
        "stock" => 64,
        "status" => "In Stock"
    ],
    [
        "code" => "CF-004",
        "name" => "Sensitive Stomach Formula",
        "category" => "Specialized Food",
        "stock" => 31,
        "status" => "Low Stock"
    ],
    [
        "code" => "CF-005",
        "name" => "Grain-Free Salmon Cat Food",
        "category" => "Premium Food",
        "stock" => 18,
        "status" => "Low Stock"
    ],
    [
        "code" => "CF-006",
        "name" => "Chicken Adult Formula",
        "category" => "Dry Cat Food",
        "stock" => 0,
        "status" => "Out of Stock"
    ]
];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>KV Enterprise | Inventory</title>

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

        /* SIDEBAR */

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

        /* MAIN */

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

        /* PANEL */

        .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 22px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-header h2 {
            font-size: 18px;
        }

        .add-button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .add-button:hover {
            background: #1d4ed8;
        }

        /* TABLE */

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
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
        }

        td {
            padding: 14px 13px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .product-code {
            font-weight: bold;
            color: #475569;
        }

        .stock-number {
            font-weight: bold;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .in-stock {
            background: #dcfce7;
            color: #15803d;
        }

        .low-stock {
            background: #fef3c7;
            color: #b45309;
        }

        .out-stock {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* FOOTER NOTE */

        .notice {
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            color: #64748b;
            font-size: 12px;
        }

        @media (max-width: 800px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
                padding: 20px;
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


<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        KV <span>Enterprise</span>
    </div>


    <div class="menu-title">
        Main
    </div>


    <a href="dashboard.php">
        Dashboard
    </a>


    <a href="inventory.php" class="active">
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


<!-- MAIN -->

<div class="main">


    <div class="topbar">

        <div>

            <h1>
                Inventory
            </h1>

            <p>
                Manage cat food products and stock levels
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


    <div class="panel">


        <div class="panel-header">

            <h2>
                Product Inventory
            </h2>

            <button class="add-button">
                + Add Product
            </button>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Product Code
                        </th>

                        <th>
                            Product Name
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Stock
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($products as $product): ?>

                        <?php

                        if ($product["status"] === "In Stock") {
                            $statusClass = "in-stock";
                        } elseif ($product["status"] === "Low Stock") {
                            $statusClass = "low-stock";
                        } else {
                            $statusClass = "out-stock";
                        }

                        ?>

                        <tr>

                            <td class="product-code">
                                <?= htmlspecialchars($product["code"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product["category"]) ?>
                            </td>

                            <td class="stock-number">
                                <?= $product["stock"] ?>
                            </td>

                            <td>

                                <span class="status <?= $statusClass ?>">
                                    <?= htmlspecialchars($product["status"]) ?>
                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>


        <div class="notice">

            Inventory records are maintained by KV Enterprise
            administrators. Stock information is updated through
            the internal inventory management system.

        </div>


    </div>


</div>


</body>

</html>