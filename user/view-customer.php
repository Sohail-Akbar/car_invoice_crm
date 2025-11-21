<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    "customer.js",
    _DIR_ . "js/jquery.dataTables.min.js",
    _DIR_ . "js/bootstrap.bundle.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css",
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            min-width: 250px;
            transform: translate3d(0px, 37.0909px, 0px) !important;
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            border-left-color: #667eea;
            padding-left: 25px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="card">
            <div class="card-header">
                <div class="pull-away">
                    <p>Customers</p>
                    <a href="add-customer">Add New Customer</a>
                </div>
            </div>
            <div class="card-body">
                <div class="dataTable-container">
                    <div class="table-responsive">
                        <table class="table table-striped" id="customersTable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer Details</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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