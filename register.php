<?php
$VERIFY_LOGIN = true;
require_once('includes/db.php');
$page_name = 'Register';

die;

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
                <div class="col-lg-6 col-md-4 window-bg content-center text-center">
                    <div class="col-lg-6 col-12">
                        <h1 class="heading text-white" style="font-weight: 900;">Hello there</h1>
                        <p class="more text-white my-3">Enter Your details and start your journey with us</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-8">
                    <div class="sign-form content-center h-100">
                        <div class="clearfix">
                            <h2 class="heading text-primary">Create Account</h2>
                            <p class="more">OR use your email account to registration</p>
                            <form action="authorize" method="POST" class="mt-4 ajax_form reset">
                                <div class="row m-0">
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-0">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="form-group pt-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control u_password" placeholder="Password" required>
                                    </div>
                                </div>
                                <div class="form-group pt-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="c_password" class="form-control u_password" placeholder="Confirm Password" required>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <input type="hidden" name="register_new_user" value="<?php echo md5(time()); ?>">
                                    <button type="submit" class="btn btn-lg bg-primary btn-block">Register</button>
                                </div>
                                <p class="more my-1">OR</p>
                                <p class="more">Already have an account? <a href="login">Log In</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>