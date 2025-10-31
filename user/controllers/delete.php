<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . "includes/Classes/TCDelete.php";

$_delete->set([
    "staff" => "staffs",
    "customer" => "customers",
    "mot_history" => "mot_history",
]);



$_delete->init();
