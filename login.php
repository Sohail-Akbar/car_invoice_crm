<?php
$VERIFY_LOGIN = true;
require_once('includes/db.php');
$page_name = 'Login';

$CSS_FILES_ = [
    "login.css",
    _DIR_ . "/css/mdb.min.css"
];
$JS_FILES_ = [
    _DIR_ . "/js/mdb.min.js"
];

if (!isset($_SESSION['user_id']) && isset($_COOKIE['Garage_Remember_Me'])) {

    list($user_id, $token) = explode(":", $_COOKIE['Garage_Remember_Me']);

    $user = $db->select_one("users", "*", ["id" => $user_id]);

    if ($user && $user['remember_token']) {

        $token_hash = hash("sha256", $token);

        // Validate hash
        if ($user['remember_token'] === $token_hash) {
            // SUCCESS → Auto Login
            $_SESSION['user_id'] = $user_id;

            $url = "user/dashboard";
            if ($user['type'] === "main_admin") {
                $url = "admin/dashboard";
            } else if ($user['type'] === "admin") {
                $url = "admin/dashboard";
            }
            echo success('logged in successfully', [
                'redirect' => $url,
            ]);

            // JavaScript redirect
            echo "<script>
                    window.location.href = '$url';
                  </script>";
            exit;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .form-outline .form-control.active~.form-label,
        .form-outline .form-control:focus~.form-label {
            transform: translateY(-1.3rem) translateY(.1rem) scale(.8);
        }

        input {
            font-size: 14px !important;
        }

        label {
            font-size: 18px !important;
        }

        input {
            padding: 10px !important;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="row mx-0 main-container">
            <div class="login-form content-center">
                <div class="login-form-container">
                    <div class="logo-img-head">
                        <img src="./images/PR-Auto-Centre-Logo-dark.png" class="logo-img" alt="Logo Img">
                    </div>
                    <div class="heading text-center">
                        <h3 class="mb-1">Sign in <?= SITE_NAME  ?></h3>
                        <p> Please sign in to access your workshop dashboard.</p>
                    </div>
                    <form action="authorize" method="POST" class="mt-5 ajax_form" autocomplete="off" data-callback="signInCB">
                        <div class="form-group">
                            <div class="form-outline">
                                <input type="email" id="email" name="email" class="form-control" />
                                <label class="form-label" for="email">Email</label>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <div class="form-outline">
                                <input type="password" id="password" name="password" class="form-control" />
                                <label class="form-label" for="password">Password</label>
                            </div>
                        </div>
                        <div class="form-group pull-away ">
                            <input type="checkbox" class="tc-checkbox" data-label="Remember me" name="remember_me">
                            <div class="forgot-password">
                                <a href="forgot" class="text-dark" style="opacity: 0.7;">Forgot your password?</a>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <input type="hidden" name="login" value="<?php echo md5(time()); ?>">
                            <button type="submit" class="login-btn">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </div>
                    </form>
                    <p class="foot-param">© 2025 Intellectual Bunch Limited. All rights reserved. </p>
                </div>
            </div>
        </div>
    </div>


    <?php require_once('./includes/js.php'); ?>
    <script>
        tc.fn.cb.signInCB = async (form, data) => {
            if (data.status === 'success') {
                location.href = data.redirect;
            } else {
                sAlert(data.data, data.status);
            }
        }
    </script>
</body>

</html>