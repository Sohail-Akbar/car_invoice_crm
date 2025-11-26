<?php
require_once('includes/db.php');
$page_name = 'Setting';

$JS_FILES_ = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content profile-setting-container" id="mainContent">
        <div class="card">
            <div class="card-body">
                <h3 class="heading">Personal Information</h3>
                <form action="user" method="POST" class="ajax_form reset" data-reset="reset">
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="label">First Name</span>
                                <input type="text" class="form-control" name="fname" value="<?= LOGGED_IN_USER['fname']; ?>" required data-length="[1,250]">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="label">Last Name</span>
                                <input type="text" class="form-control" name="lname" value="<?= LOGGED_IN_USER['lname']; ?>" required data-length="[1,250]">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="update_personal_information" value="<?= bc_code(); ?>">
                            <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-5">
            <div class="card-body">
                <h3 class="heading">Change Password</h3>
                <form action="user" method="POST" class="ajax_form reset" data-reset="reset">
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="label">Current Password</span>
                                <input type="password" class="form-control" name="current_password" required data-length="[1,20]">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="label">New Password</span>
                                <input type="password" class="form-control u_password" name="new_password" required data-length="[1,20]">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="label">Confirm Password</span>
                                <input type="password" class="form-control u_password" name="confirm_password" required data-length="[1,20]">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="change_password" value="<?= bc_code(); ?>">
                            <button class="btn" type="submit"> Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>