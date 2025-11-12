<?php
$VERIFY_LOGIN = true;
require_once('includes/db.php');
$page_name = 'Login';

$CSS_FILES_ = [
    "login.css"
];
$JS_FILES_ = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>

    <div class="login-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-car-side"></i>
                </div>
                <div class="logo-text">
                    <h1>AutoPro</h1>
                    <p>Vehicle Management System</p>
                </div>
            </div>

            <ul class="features">
                <li>
                    <i class="fas fa-shield-alt"></i>
                    Secure & Reliable Platform
                </li>
                <li>
                    <i class="fas fa-tachometer-alt"></i>
                    Real-time Vehicle Tracking
                </li>
                <li>
                    <i class="fas fa-cogs"></i>
                    Advanced Management Tools
                </li>
                <li>
                    <i class="fas fa-chart-line"></i>
                    Performance Analytics
                </li>
            </ul>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your vehicle management dashboard</p>
            </div>

            <form action="authorize" method="POST" class="mt-5 ajax_form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password"
                            required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye" style="left: -18px;"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="login" value="<?php echo md5(time()); ?>">
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>

                <div class="forgot-password">
                    <a href="forgot">Forgot your password?</a>
                </div>
            </form>

            <div class="footer">
                <p>&copy; 2024 AutoPro Vehicle Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('loginForm');

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye" style="left: -18px;"></i>' : '<i class="fas fa-eye-slash" style="left: -18px;"></i>';
            });

            // Form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                // Simulate login process
                if (email && password) {
                    // Add loading state
                    const submitBtn = this.querySelector('.login-btn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                    submitBtn.disabled = true;

                    // Simulate API call
                    setTimeout(() => {
                        alert('Login successful! Redirecting to dashboard...');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 1500);
                }
            });

            // Add focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
    <?php require_once('./includes/js.php'); ?>
    <script>
        <?php
        // Verify User
        if (isset($_GET['verify']) && isset($_GET['token']) && isset($_GET['email'])) {
            $token = $_GET['token'];
            $email = $_GET['email'];

            $res = verifyUserWithToken($email, $token);
            $res = json_decode($res, true);
            echo js_msg($res['status'], $res['data']);
        }
        if (isset($_GET['success'])) {
            echo 'sAlert("' . $_GET['success'] . '", "Congratulations")';
        }
        ?>
    </script>
</body>

</html>