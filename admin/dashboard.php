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
    <div class="all-content">
        <div class="card">
            <div class="card-body">
                <h3 class="heading">Dashboard</h3>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>