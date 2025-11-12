<?php
require_once('includes/db.php');
require_once _DIR_ . "includes/Classes/SmtpMailer.php";
$page_name = 'Login';
$JS_FILES_ = [];
$reset_password = false;
$alertMsg = '';

// Verify User
if (isset($_GET['reset']) && isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $user = $db->select_one('users', '*', ['email' => $email]);
    if ($user) {
        $new_forgot_token = md5(time() . rand(9, 9999)) . $user['id'];
        $new_expiry_date = get_date_with("+ 1 days");
        if ($token == $user['password_forgot_token']) {
            $expiry_date = $user['token_expiry_date'];
            $expiry_date = date("Y-m-d h:i:s", strtotime($expiry_date));
            $current_date = date("Y-m-d h:i:s");
            if ($current_date > $expiry_date) {
                $db->update('users', array(
                    'password_forgot_token' => $new_forgot_token,
                    'token_expiry_date' => $new_expiry_date
                ), array('id' => $user['id']));
                $_tc_email->send([
                    'template' => 'forgotEmail',
                    'to' => $user['email'],
                    'vars' => [
                        'token' => $new_forgot_token,
                        'to' => $user['email'],

                    ]
                ]);
                $alertMsg = 'sAlert("Reset Link expired. We sent a new password reset link to your email address. You can reset your account password with in next 24 hours", "Error");';
            } else {
                $reset_password = true;
            }
        } else {
            $db->update('users', array(
                'password_forgot_token' => $new_forgot_token,
                'token_expiry_date' => $new_expiry_date
            ), array('id' => $user['id']));
            $_tc_email->send([
                'template' => 'forgotEmail',
                'to' => $user['email'],
                'vars' => [
                    'token' => $new_forgot_token,
                    'to' => $user['email'],

                ]
            ]);
            $alertMsg = 'sAlert("Reset Link expired. We sent a new password reset link to your email address. You can reset your account password with in next 24 hours", "Error");';
        }
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
            padding: 0 !important;
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

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
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

        .btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }

        .otp-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .password-strength {
            margin-top: 10px;
            height: 5px;
            background: #ecf0f1;
            border-radius: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s;
        }

        .password-weak .password-strength-bar {
            width: 30%;
            background: #e74c3c;
        }

        .password-medium .password-strength-bar {
            width: 60%;
            background: #f39c12;
        }

        .password-strong .password-strength-bar {
            width: 100%;
            background: #2ecc71;
        }

        .password-requirements {
            margin-top: 10px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .success-message {
            text-align: center;
            padding: 20px;
            background: #d4edda;
            color: #155724;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
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

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-panel {
                padding: 30px 20px;
            }

            .right-panel {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <div class="logo-icon">🚗</div>
                <div class="logo-text">Vehicle Garage</div>
            </div>
            <h2 class="panel-title">Reset Your Password</h2>
            <p class="panel-description">Follow the simple steps to securely reset your password and regain access to
                your vehicle garage account.</p>
            <ul class="features">
                <li>
                    <div class="feature-icon">✓</div>
                    <span>Secure Email verification</span>
                </li>
                <li>
                    <div class="feature-icon">✓</div>
                    <span>Real-time password strength check</span>
                </li>
                <li>
                    <div class="feature-icon">✓</div>
                    <span>Instant access after reset</span>
                </li>
            </ul>
        </div>

        <div class="right-panel">
            <div class="form-container">
                <h2 class="form-title">Forgot Password?</h2>
                <p class="form-subtitle">Enter your details to reset your password</p>
                <div class="success-message" id="successMessage">
                    <h3>Password Reset Successful!</h3>
                    <p>Your password has been changed successfully. You can now login with your new password.</p>
                </div>

                <form action="authorize" method="POST" class="mt-5 ajax_form reset">
                    <?php if ($reset_password) { ?>
                        <div class="form-step active" id="step1">
                            <div class="form-group">
                                <label for="email">New Password</label>
                                <input type="password" name="new_password" class="form-control u_password" placeholder="New Password" required data-length="[1,20]">
                            </div>
                            <div class="form-group">
                                <label for="email">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control u_password" placeholder="Confirm Password" required data-length="[1,20]">
                            </div>
                            <div class="form-group mt-3">
                                <input type="hidden" name="token" value="<?php echo addslashes($_GET['token']); ?>">
                                <input type="hidden" name="email" value="<?php echo addslashes($_GET['email']); ?>">
                                <input type="hidden" name="reset_password" value="<?php echo md5(rand(0, 999)); ?>">
                                <button type="submit" class="btn btn-lg bg-primary btn-block">Update</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-step active" id="step1">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" placeholder="Enter your registered email" required>
                            </div>
                            <input type="hidden" name="send_reset_password_link" value="<?php echo md5(rand(0, 999)); ?>">
                            <button type="submit" class="btn" id="sendOtpBtn">Reset Password</button>
                        </div>
                    <?php } ?>
                </form>

                <div class="back-to-login">
                    <a href="login">← Back to Login</a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
    <script>
        <?php echo $alertMsg; ?>
    </script>
</body>

</html>