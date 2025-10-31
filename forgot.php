<?php
require_once('includes/db.php');
require_once _DIR_ . "includes/Classes/TCEmails.php";
$page_name = 'Login';
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
</head>

<body>
    <div class="lg-container">
        <div class="login-window" data-js-height=".lg-container">
            <div class="row m-0 h-100">
                <div class="col-lg-6 col-md-8">
                    <div class="sign-form content-center h-100">
                        <div class="clearfix">
                            <h2 class="heading text-primary">Reset Password </h2>
                            <form action="login" method="POST" class="mt-5 ajax_form reset">
                                <?php if ($reset_password) {
                                ?>
                                    <div class="form-group pt-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="password" name="new_password" class="form-control u_password" placeholder="New Password" required data-length="[1,20]">
                                        </div>
                                    </div>
                                    <div class="form-group pt-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="password" name="confirm_password" class="form-control u_password" placeholder="Confirm Password" required data-length="[1,20]">
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="hidden" name="token" value="<?php echo addslashes($_GET['token']); ?>">
                                        <input type="hidden" name="email" value="<?php echo addslashes($_GET['email']); ?>">
                                        <input type="hidden" name="reset_password" value="<?php echo md5(rand(0, 999)); ?>">
                                        <button type="submit" class="btn btn-lg bg-primary btn-block">Reset Password</button>
                                    </div>
                                <?php
                                } else { ?>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="text" name="email" class="form-control" placeholder="Email" required>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="hidden" name="send_reset_password_link" value="<?php echo md5(rand(0, 999)); ?>">
                                        <button type="submit" class="btn btn-lg bg-primary btn-block">Reset Password</button>
                                    </div>
                                <?php } ?>
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
        <?php echo $alertMsg; ?>
    </script>
</body>

</html>