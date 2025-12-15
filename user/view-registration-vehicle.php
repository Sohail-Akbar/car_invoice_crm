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
    <main class="main-content view-reg-vehicle-container" id="mainContent">
        <div class="card">
            <div class="custom-table-header pull-away">
                <div class="search-container">
                    <input type="text" class="search-input search-minimal form-control" placeholder="Type to search...">
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="d-flex content-center">
                    <div class="btn-group dropleft content-center br-5">
                        <button type="button" class="btn dropdown-toggle table-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Entries
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7H20M6.99994 12H16.9999M10.9999 17H12.9999" stroke="#454545" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">5</a>
                            <a class="dropdown-item" href="#">25</a>
                            <a class="dropdown-item" href="#">50</a>
                            <a class="dropdown-item" href="#">100</a>
                        </div>
                    </div>
                    <a href="add-customer" class="btn ml-3 add-customer-btn br-5">+ &nbsp;Add New Customer</a>
                </div>
            </div>
            <div class="table-responsive  table-custom-design mt-5">
                <table id="vehicleTable" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Customer Details</th>
                            <th>Registration No</th>
                            <th>Details</th>
                            <th class="d-none">Status</th>
                            <th class="d-none"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>