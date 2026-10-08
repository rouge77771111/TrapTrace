<?php

session_name("KVENTERPRISE_SESSION");
session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $validUsername = "admin";
    $validPassword = "kvadmin123";

    $ipAddress = $_SERVER["REMOTE_ADDR"] ?? "Unknown";

    error_log("KV login IP check: " . json_encode([
    "REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"] ?? null,
    "X_FORWARDED_FOR" => $_SERVER["HTTP_X_FORWARDED_FOR"] ?? null,
    "X_REAL_IP" => $_SERVER["HTTP_X_REAL_IP"] ?? null
]));

    /*
    |--------------------------------------------------------------------------
    | Successful Authorized Login
    |--------------------------------------------------------------------------
    */

    if ($username === $validUsername && $password === $validPassword) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO system_activity_logs
                (
                    ip_address,
                    username,
                    activity_type,
                    activity
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            $stmt->execute([
                $ipAddress,
                $username,
                "Successful Login",
                "Authorized user successfully logged in to KV Enterprise."
            ]);

        } catch (PDOException $e) {

            // Keep the honeypot working if logging fails.

        }

        $_SESSION["kv_logged_in"] = true;
        $_SESSION["kv_username"] = $username;

        header("Location: dashboard.php");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Failed Login — Observe Behavior
    |--------------------------------------------------------------------------
    */

    try {

        /*
        | Count ALL previous failed login events from this
        | IP address during the last 10 minutes.
        */

        $countStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM attack_events
            WHERE ip_address = ?
            AND attack_type IN (
                'Credential Attack',
                'Repeated Login Attempt',
                'Brute Force Attack'
            )
            AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ");

        $countStmt->execute([$ipAddress]);

        $previousAttempts = (int) $countStmt->fetchColumn();

        /*
        | Include the current failed login attempt.
        */

        $totalAttempts = $previousAttempts + 1;

        /*
        |--------------------------------------------------------------------------
        | Classify Observed Behavior
        |--------------------------------------------------------------------------
        */

   if ($totalAttempts === 1) {
    $attackType = "Credential Attack";
    $riskLevel = "Low";
    $activity = "First failed login attempt detected on KV Enterprise.";

} elseif ($totalAttempts <= 3) {
    $attackType = "Repeated Login Attempt";
    $riskLevel = "Medium";
    $activity = "Repeated failed login attempts detected from the same IP address.";

} elseif ($totalAttempts === 4) {
    $attackType = "Repeated Login Attempt";
    $riskLevel = "High";
    $activity = "Four failed login attempts detected from the same IP address within 10 minutes.";

} else {
    $attackType = "Brute Force Attack";
    $riskLevel = "Critical";
    $activity = "Five or more failed login attempts detected from the same IP address within 10 minutes, indicating possible brute-force behavior.";
}

        /*
        |--------------------------------------------------------------------------
        | Store Observed Suspicious Activity
        |--------------------------------------------------------------------------
        */

        $command =
            "Username: " .
            ($username !== "" ? $username : "[empty]");

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
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $ipAddress,
            $username,
            $attackType,
            $command,
            $activity,
            $riskLevel,
            "Detected"
        ]);

    } catch (PDOException $e) {

        // Keep the honeypot working if logging fails.

    }

    $error = "Invalid username or password.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>KV Enterprise - Login</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(
                    rgba(15, 23, 42, 0.92),
                    rgba(15, 23, 42, 0.92)
                ),
                #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 25px;
        }

        .login-card {
            background: #ffffff;
            color: #1e293b;
            border-radius: 14px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .logo {
            width: 58px;
            height: 58px;
            background: #2563eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 25px;
            font-weight: bold;
            margin: 0 auto 18px;
        }

        h1 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 7px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .footer {
            text-align: center;
            margin-top: 22px;
            font-size: 12px;
            color: #94a3b8;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <div class="logo">
                KV
            </div>

            <h1>KV Enterprise</h1>

            <p class="subtitle">
                Inventory Management System
            </p>

            <?php if ($error !== ""): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    autocomplete="off"
                    required
                >

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    autocomplete="off"
                    required
                >

                <button type="submit">
                    Sign In
                </button>

            </form>

            <div class="footer">
                KV Enterprise Inventory Management System
            </div>

        </div>

    </div>

</body>

</html>