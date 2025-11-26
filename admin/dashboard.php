<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];
$CSS_FILES_ = [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content dashboard-container" id="mainContent">
        <div class="header">
            <h5>Cody Garage</h5>
            <div class="heading-sm-w" style="width: 360px;text-align: end;">
                <h2 class="mb-0" style="font-size: 45px;color: white;"><b>Welcome</b> To Your</h2>
                <h2 style="font-size: 45px;color: white;"><b>Dashboard...</b></h2>
            </div>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>