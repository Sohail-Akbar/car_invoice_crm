<?php
require_once "includes/db.php";

$token = $_GET['token'] ?? '';
if (!$token) exit("Invalid link.");
$page_name = "Set Password";

$user = $db->select_one("users", "*", ["verify_token" => $token, "is_active" => 1]);

if (!$user) exit("Invalid or expired token.");

$successMessage = '';
$errorMessage = '';

if (isset($_POST['set_password'])) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $errorMessage = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters.";
    } else {
        $db->update("users", [
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "verify_token" => null
        ], [
            "id" => $user['id']
        ]);
        $successMessage = "Password set successfully! You can now login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 28px;
            margin-right: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
        }

        .panel-title {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .panel-description {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .features {
            list-style-type: none;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .feature-icon {
            margin-right: 10px;
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .right-panel {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .form-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        .btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            font-weight: 600;
        }

        .btn:hover {
            background: #2980b9;
        }

        .success-message {
            text-align: center;
            padding: 20px;
            background: #d4edda;
            color: #155724;
            border-radius: 8px;
            margin-bottom: 20px;
            display: <?php echo $successMessage ? 'block' : 'none'; ?>;
        }

        .error-message {
            text-align: center;
            padding: 20px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 8px;
            margin-bottom: 20px;
            display: <?php echo $errorMessage ? 'block' : 'none'; ?>;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #3498db;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        @media (max-width:768px) {
            .container {
                flex-direction: column;
            }

            .left-panel,
            .right-panel {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container p-0">
        <div class="left-panel">
            <div class="logo">
                <div class="logo-icon">🚗</div>
                <div class="logo-text">Vehicle Garage</div>
            </div>
            <h2 class="panel-title">Set Your Password</h2>
            <p class="panel-description">Enter a strong password to secure your account and gain full access to your dashboard.</p>
            <ul class="features">
                <li>
                    <div class="feature-icon">✓</div><span>Secure Email verification</span>
                </li>
                <li>
                    <div class="feature-icon">✓</div><span>Real-time password strength check</span>
                </li>
                <li>
                    <div class="feature-icon">✓</div><span>Instant access after reset</span>
                </li>
            </ul>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <h2 class="form-title">Set Password</h2>
                <p class="form-subtitle">Choose a strong password for your account</p>

                <div class="success-message"><?php echo $successMessage; ?></div>
                <div class="error-message"><?php echo $errorMessage; ?></div>

                <?php if (!$successMessage) { ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" placeholder="Enter Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" name="set_password" class="btn">Set Password</button>
                    </form>
                <?php } ?>

                <div class="back-to-login">
                    <a href="login">← Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>