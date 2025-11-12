<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . "includes/Classes/TCDelete.php";

$_delete->set([
    "staff" => "staffs",
    "customer" => "users",
    "customer_car_history" => "customer_car_history",
    "customer_staff" => "customer_staff",
    "customer_notes" => "customer_notes"
]);



$_delete->init();
