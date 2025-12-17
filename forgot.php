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

        .login-form-container {
            width: 460px;
            min-height: 500px;
            border-radius: 10px;
        }

        .my-confirm-btn {
            width: 100%;
            font-size: 16px;
            color: white;
            background: linear-gradient(45deg, #102E4A, #266DB0);
            padding: 8px;
            border-radius: 10px;
            border: none;
            outline: none;
        }

        .my-confirm-btn:hover {
            opacity: 0.7;
        }

        .my-swal-box {
            border-radius: 15px !important;
            padding: 20px !important;

        }

        .my-swal-box .swal2-title {
            padding: 0 30px;
            color: #153c60;
            padding-top: 22px;
            margin-bottom: 0;
        }

        .my-swal-box .swal2-actions {
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="row mx-0 main-container">
            <div class="login-form content-center">
                <div class="login-form-container">
                    <div class="logo-img-head">
                        <!-- <img src="./images/Hillcliffe-Garage-Logo.png" class="logo-img" alt="Logo Img"> -->
                        <img src="./images/PR-Auto-Centre-Logo-dark.png" class="logo-img" alt="Logo Img">
                    </div>
                    <?php if (!isset($_GET['type'])) { ?>
                        <div class="heading">
                            <h3 class="mt-4 text-center mb-2">Forgot Password</h3>
                            <p class="text-center">Enter your registered email address. We’ll send you a code to reset your password.</p>
                        </div>
                        <form action="authorize" method="POST" class="mt-4 ajax_form" data-callback="signInCB">
                            <div class="form-group">
                                <div class="form-outline">
                                    <input type="email" id="email" name="email" class="form-control" />
                                    <label class="form-label" for="email">Email</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="send_reset_password_otp" value="<?php echo md5(rand(0, 999)); ?>">
                                <button type="submit" class="login-btn">Send OTP</button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <div class="heading">
                            <a href="login">
                                <h3 style="font-size: 14px;"><i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp; Back</h3>
                            </a>
                            <h3 class="mt-4 text-center mb-2">Reset Password</h3>
                            <p class="text-center">Enter your new password</p>
                        </div>
                        <form action="authorize" method="POST" class="mt-4 ajax_form" data-callback="passwordUpdateCB">
                            <div class="form-group">
                                <div class="form-outline">
                                    <input type="password" id="password" name="password" class="form-control" />
                                    <label class="form-label" for="password">New Password</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-outline">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" />
                                    <label class="form-label" for="confirm_password">Confirm Password</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="updatePassword" value="<?php echo md5(rand(0, 999)); ?>">
                                <button type="submit" class="login-btn">Confirm</button>
                            </div>
                        </form>
                    <?php } ?>
                    <p class="foot-param">© 2025 Intellectual Bunch Limited.
                        All rights reserved. </p>
                    <a href="login">
                        <h3 style="font-size: 14px;"><i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp; Back</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <?php require_once('./includes/js.php'); ?>
    <script>
        // Add new customer callback
        tc.fn.cb.passwordUpdateCB = async (form, data) => {
            if (data.status === 'success') {

                Swal.fire({
                    icon: 'success',
                    title: `Password Update Successfully`,
                    html: `
        <p style="font-size:15px;margin-top:10px;color:#333;">
            Your password has been updated successfully.
        </p>
    `,

                    // CUSTOM BUTTON
                    confirmButtonText: 'Back to Login',
                }).then(() => {
                    window.location.href = "login";
                });


            } else {
                sAlert(data.data, data.status);
            }
        };

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