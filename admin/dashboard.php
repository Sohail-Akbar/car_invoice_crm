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
            <div class="heading-sm-w" style="text-align: left;">
                <h2 class="mb-0" style="font-size: 45px;color: #333;"><b>Welcome To</b></h2>
                <h2 style="font-size: 45px;color: #333;"><b>Your <span class="text-clr" style="position: relative;">
                            Dashboard
                            <svg class="dashboard-svg-bar" width="134" height="19" viewBox="0 0 134 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M133.471 1.55529C56.1984 -3.4811 22.9232 5.02254 0.585449 8.54261C0.238293 11.48 0.370139 13.8959 0.00041482 18.5626C59.8957 -0.893031 99.8257 2.30424 133.426 3.54363C133.439 3.17416 133.429 2.6637 133.471 1.55529Z" fill="#214F79" />
                            </svg>
                        </span></b></h2>
            </div>
            <div class="pt-1">
                <p class="mt-4" style="color: #333;">Manage your invoices, payments, and clients</p>
                <p style="color: #333;">easily from here.</p>
            </div>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>