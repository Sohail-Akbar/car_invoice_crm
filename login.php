<?php
$VERIFY_LOGIN = true;
require_once('includes/db.php');
$page_name = 'Login';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <div class="lg-container">
        <div class="login-window" data-js-height=".lg-container">
            <div class="row m-0 h-100">
                <div class="col-lg-6 col-md-8">
                    <div class="sign-form content-center h-100">
                        <div class="clearfix">
                            <h2 class="heading text-primary">Sign in to <?= SITE_NAME ?></h2>
                            <p class="more">OR Use email account</p>
                            <form action="authorize" method="POST" class="mt-5 ajax_form">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="text" name="email" class="form-control" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="form-group pt-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="password" name="password" class="form-control" placeholder="Password" required data-length="[1,20]">
                                    </div>
                                </div>
                                <p class="text-right">
                                    <!-- <a href="forgot">Forgot Password?</a> -->
                                </p>
                                <div class="form-group mt-5">
                                    <input type="hidden" name="login" value="<?php echo md5(time()); ?>">
                                    <button type="submit" class="btn btn-lg bg-primary btn-block">Login</button>
                                </div>
                                <!-- <p class="more my-3">OR</p> -->
                                <!-- <p class="more">Don't you have an account? <a href="register">Sign Up</a></p> -->
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-4 window-bg content-center text-center">
                    <div class="col-lg-6 col-12">
                        <h1 class="heading text-white" style="font-weight: 900;">Hello there</h1>
                        <p class="more text-white my-3">Enter Your details and start your journey with us</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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