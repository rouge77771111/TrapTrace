<?php

session_start();

require_once "config/database.php";

// Google reCAPTCHA Configuration
// Default test keys are provided for development and testing.
// For production, configure RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY in Railway or environment variables.
$recaptchaSiteKey = getenv("RECAPTCHA_SITE_KEY") ?: "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";
$recaptchaSecretKey = getenv("RECAPTCHA_SECRET_KEY") ?: "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $recaptchaResponse = $_POST["g-recaptcha-response"] ?? "";

    // 1. Verify reCAPTCHA
    if (empty($recaptchaResponse)) {

        $error = "Please complete the reCAPTCHA verification.";

    } else {

        $clientIp = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"] ?? "";
        if (strpos($clientIp, ",") !== false) {
            $clientIp = trim(explode(",", $clientIp)[0]);
        }

        $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
        $verifyData = [
            "secret"   => $recaptchaSecretKey,
            "response" => $recaptchaResponse,
            "remoteip" => $clientIp
        ];

        $verified = false;

        if (function_exists("curl_init")) {
            $ch = curl_init($verifyUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verifyData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $apiResult = curl_exec($ch);
            curl_close($ch);
        } else {
            $options = [
                "http" => [
                    "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                    "method"  => "POST",
                    "content" => http_build_query($verifyData),
                    "timeout" => 10
                ]
            ];
            $context = stream_context_create($options);
            $apiResult = @file_get_contents($verifyUrl, false, $context);
        }

        if ($apiResult !== false) {
            $resultJson = json_decode($apiResult, true);
            if (!empty($resultJson["success"])) {
                $verified = true;
            }
        }

        if (!$verified) {

            $error = "reCAPTCHA verification failed. Please try again.";

        } elseif ($username === "" || $password === "") {

            $error = "Please enter your username and password.";

        } else {

            $stmt = $pdo->prepare(
                "SELECT * FROM users WHERE username = ? LIMIT 1"
            );

            $stmt->execute([$username]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                header("Location: dashboard.php");
                exit;

            } else {

                $error = "Invalid username or password.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trap&Trace - Login</title>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 400px;
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 220px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }

        .logo h1 {
            font-size: 32px;
            color: #111827;
            margin-bottom: 8px;
        }

        .logo h1 span {
            color: #2563eb;
        }

        .logo p {
            color: #6b7280;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #374151;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2563eb;
        }

        .recaptcha-group {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 11px;
            border-radius: 7px;
            margin-bottom: 18px;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 440px) {
            .login-card {
                width: 92%;
                padding: 30px 20px;
            }
            .g-recaptcha {
                transform: scale(0.88);
                transform-origin: 0 0;
            }
        }

    </style>

</head>

<body>

    <div class="login-card">

        <div class="logo">

            <?php if (file_exists(__DIR__ . "/assets/traptrace-logo.png")): ?>
                <img
                    src="assets/traptrace-logo.png"
                    alt="Trap&Trace"
                >
            <?php else: ?>
                <h1>Trap<span>&</span>Trace</h1>
            <?php endif; ?>

            <p>Honeypot Security Monitoring System</p>

        </div>

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <div class="form-group recaptcha-group">
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey) ?>"></div>
            </div>

            <button
                type="submit"
                class="login-button"
            >
                Login
            </button>

        </form>

    </div>

</body>

</html>