<?php

session_name("KVENTERPRISE_SESSION");
session_start();

if (!isset($_SESSION["kv_logged_in"]) || $_SESSION["kv_logged_in"] !== true) {
    header("Location: index.php");
    exit;
}

$products = [
    [
        "id" => "CF-001",
        "name" => "Premium Persian Cat Food",
        "category" => "Adult Cat Food",
        "stock" => 24
    ],
    [
        "id" => "CF-002",
        "name" => "Indoor Adult Cat Food",
        "category" => "Adult Cat Food",
        "stock" => 19
    ],
    [
        "id" => "CF-003",
        "name" => "Kitten Growth Formula",
        "category" => "Kitten Food",
        "stock" => 15
    ],
    [
        "id" => "CF-004",
        "name" => "Sensitive Stomach Formula",
        "category" => "Specialized Food",
        "stock" => 5
    ],
    [
        "id" => "CF-005",
        "name" => "Grain-Free Salmon Cat Food",
        "category" => "Adult Cat Food",
        "stock" => 8
    ],
    [
        "id" => "CF-006",
        "name" => "Chicken Adult Formula",
        "category" => "Adult Cat Food",
        "stock" => 0
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

    <title>Inventory | KV Enterprise</title>

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
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* INVENTORY PANEL */

        .panel {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
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

        .product-count {
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
            padding: 13px 12px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }

        td {
            padding: 15px 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .product-id {
            font-weight: bold;
            color: #2563eb;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
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

        /* RESPONSIVE */

        @media (max-width: 700px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
                padding: 20px;
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

    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="inventory.php" class="active">
        Inventory
    </a>

    <a href="admin.php">
        Administration
    </a>

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
            Inventory
        </h1>

        <p>
            Cat food inventory records
        </p>

    </div>

    <div class="panel">

        <div class="panel-header">

            <h2>
                Product Inventory
            </h2>

            <span class="product-count">
                <?= count($products) ?> products
            </span>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Product ID
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

                    if ($product["stock"] === 0) {

                        $status = "Out of Stock";
                        $statusClass = "out-stock";

                    } elseif ($product["stock"] <= 8) {

                        $status = "Low Stock";
                        $statusClass = "low-stock";

                    } else {

                        $status = "In Stock";
                        $statusClass = "in-stock";

                    }

                    ?>

                    <tr>

                        <td class="product-id">
                            <?= htmlspecialchars($product["id"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($product["name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($product["category"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($product["stock"]) ?> units
                        </td>

                        <td>

                            <span class="status <?= $statusClass ?>">
                                <?= htmlspecialchars($status) ?>
                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>