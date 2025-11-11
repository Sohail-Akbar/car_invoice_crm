<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


if (isset($_POST['saveRole'])) {
    $text = $_POST['text'];

    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        // Update existing role
        $db->update("roles", [
            "text" =>  $text
        ], [
            "id" => $id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id']
        ]);
        returnSuccess("Role updated successfully", [
            "redirect" => "add-role"
        ]);
    } else {
        $save = $db->insert("roles", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "text" =>  $text
        ]);

        returnSuccess("Role created successfully", [
            "redirect" => "add-role"
        ]);
    }
}
