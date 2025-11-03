<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


if (isset($_POST['saveService'])) {
    $text = $_POST['text'];
    $amount = $_POST['amount'];

    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        // Update existing service
        $db->update("services", [
            "text" => ["encrypt" => $text],
            "amount" => ["encrypt" => $amount]
        ], [
            "id" => $id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id']
        ]);
        returnSuccess("Service updated successfully", [
            "redirect" => "add-services"
        ]);
    } else {
        $save = $db->insert("services", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "text" => ["encrypt" => $text],
            "amount" => ["encrypt" => $amount]
        ]);

        returnSuccess("Role created successfully", [
            "redirect" => "add-services"
        ]);
    }
}
