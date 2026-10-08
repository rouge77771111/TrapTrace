<?php

session_name("KVENTERPRISE_SESSION");
session_start();

require_once "../config/database.php";

$ipAddress = $_SERVER["REMOTE_ADDR"] ?? "Unknown";
$requestUri = $_SERVER["REQUEST_URI"] ?? "/KVEnterprise/admin.php";

/*
|--------------------------------------------------------------------------
| UNAUTHORIZED ACCESS DETECTION
|--------------------------------------------------------------------------
| If someone tries to access this protected KV Enterprise page
| without being authenticated, record the attempt as an attack.
*/

if (!isset($_SESSION["kv_logged_in"]) || $_SESSION["kv_logged_in"] !== true) {

    try {

        $stmt = $pdo->prepare("
            INSERT INTO attack_events
            (
                ip_address,
                username,
                attack_type,
                command,
                activity,
                risk_level,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $ipAddress,
            null,
            "Unauthorized Access",
            "Requested URL: " . $requestUri,
            "Unauthorized access attempt detected on a protected KV Enterprise service.",
            "High",
            "Detected"
        ]);

    } catch (PDOException $e) {

        // Do not expose database errors to the honeypot visitor.

    }

    header("Location: /KVEnterprise/index.php");
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

    <title>Administration | KV Enterprise</title>

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

        /* MAIN CONTENT */

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

        /* PANEL */

        .panel {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .panel h2 {
            margin-bottom: 12px;
            font-size: 19px;
        }

        .panel p {
            color: #6b7280;
            line-height: 1.6;
            font-size: 14px;
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

    <a href="inventory.php">
        Inventory
    </a>

    <a href="admin.php" class="active">
        Administration
    </a>

    <div class="logout">

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="header">

        <h1>
            Administration
        </h1>

        <p>
            KV Enterprise administrative services
        </p>

    </div>

    <div class="panel">

        <h2>
            Administrative Access
        </h2>

        <p>
            This protected area is available only to authenticated
            KV Enterprise users.
        </p>

    </div>

</div>

</body>

</html>