<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    "vehicle-register.js",
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$mot_history = $db->select("customer_car_history", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
], [
    "order_by" => "id desc"
]);
if (!$mot_history) $mot_history = [];
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
            <div class="card-header">
                <div class="pull-away">
                    <p>View Registeration Vehicle</p>
                    <a href="registration-vehicle">Add New Register Vehicle</a>
                </div>
            </div>
            <div class="card-body">
                <div class="dataTable-container">
                    <div class="table-responsive">
                        <table id="vehicleTable" class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer Details</th>
                                    <th>Registration No</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th class="d-none"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>