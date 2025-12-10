<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . "includes/Classes/TCDelete.php";

$_delete->set([
    "staff" => "users",
    "customer" => "users",
    "customer_car_history" => "customer_car_history",
    "customer_staff" => "customer_staff",
    "customer_notes" => "customer_notes",
    "email_template" => "email_template"
]);



$_delete->init();
