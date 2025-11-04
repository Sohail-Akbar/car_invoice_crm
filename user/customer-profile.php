<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "customer.js"
];
$CSS_FILES_ = [
    "customer-profile.css"
];


$get_id = $_GET['id'];

$customer = $db->select_one("customers", "*", [
    "id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$invoice = $db->select("invoices", "*", [
    "customer_id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$company = $db->select_one("companies", "*", [
    "id" => LOGGED_IN_USER['company_id']
]);


$total_paid = 0;
$total_due = 0;

foreach ($invoice as $inv) {
    $total_paid += $inv['paid_amount'];
    $total_due  += $inv['due_amount'];
}

$total_invoice = count($invoice);



$agency_id = LOGGED_IN_USER['agency_id'];
$assigned_staff = $db->query("SELECT 
    `customer_staff`.`id`,
    `customer_staff`.`staff_id`,
    `customer_staff`.`assignment_date`,
    `staffs`.`fname`,
    `staffs`.`lname`,
    `staffs`.`email`,
    `staffs`.`contact`,
    `staffs`.`title`
FROM 
    `customer_staff`
INNER JOIN 
    `staffs` ON `customer_staff`.`staff_id` = `staffs`.`id`
WHERE 
    `customer_staff`.`customer_id` = $get_id
    AND `customer_staff`.`company_id` = $get_id
    AND `customer_staff`.`agency_id` = $agency_id
    AND `customer_staff`.`is_active` = 1", ["select_query" => true]);

// Customer Cars
$cars = $db->select("customer_car_history", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1,
    "customer_id" => $get_id,
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="container">
            <?php
            // Header
            require_once "./components/customer-profile/header.php";
            // Profile info
            require_once "./components/customer-profile/profile-info.php";
            // tabs
            require_once "./components/customer-profile/tabs.php";
            // Overview
            require_once "./components/customer-profile/overview.php";
            // Invoice
            require_once "./components/customer-profile/invoices.php";
            // Notes
            require_once "./components/customer-profile/notes.php";
            // Cars
            require_once "./components/customer-profile/cars.php";
            ?>
        </div>
    </div>
    <script>
        const _GET = <?= json_encode($_GET); ?>;
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>