<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');

// Add Staff
if (isset($_POST['createStaff'])) {
    $title = arr_val($_POST, 'title', '');
    $gender = arr_val($_POST, 'gender', '');
    $fname = arr_val($_POST, 'fname', '');
    $lname = arr_val($_POST, 'lname', '');
    $email = arr_val($_POST, 'email', '');
    $contact = arr_val($_POST, 'contact', '');
    $role_id = arr_val($_POST, 'role_id', '');
    $address = arr_val($_POST, 'address', '');
    $postcode = arr_val($_POST, 'postcode', '');
    $city = arr_val($_POST, 'city', '');

    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        $save = $db->update("staffs", [
            "role_id" => $role_id,
            "title" => ["encrypt" => $title],
            "gender" => ["encrypt" => $gender],
            "fname" => ["encrypt" => $fname],
            "lname" => ["encrypt" => $lname],
            "email" => ["encrypt" => $email],
            "contact" => ["encrypt" => $contact],
            "address" => ["encrypt" => $address],
            "postcode" => ["encrypt" => $postcode],
            "city" => ["encrypt" => $city],
        ], [
            "id" => $id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id']
        ]);
    } else {
        $save = $db->insert("staffs", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "role_id" => $role_id,
            "title" => ["encrypt" => $title],
            "gender" => ["encrypt" => $gender],
            "fname" => ["encrypt" => $fname],
            "lname" => ["encrypt" => $lname],
            "email" => ["encrypt" => $email],
            "contact" => ["encrypt" => $contact],
            "address" => ["encrypt" => $address],
            "postcode" => ["encrypt" => $postcode],
            "city" => ["encrypt" => $city],
        ]);
    }

    if ($save) {
        returnSuccess("Staff saved successfully", [
            "redirect" => "add-staff"
        ]);
    } else {
        returnError("Error saving staff. Please try again.");
    }
}


// Add Customer
if (isset($_POST['createCustomer'])) {
    $title = arr_val($_POST, 'title', '');
    $gender = arr_val($_POST, 'gender', '');
    $fname = arr_val($_POST, 'fname', '');
    $lname = arr_val($_POST, 'lname', '');
    $email = arr_val($_POST, 'email', '');
    $contact = arr_val($_POST, 'contact', '');
    $address = arr_val($_POST, 'address', '');
    $postcode = arr_val($_POST, 'postcode', '');
    $city = arr_val($_POST, 'city', '');

    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        $save = $db->update("customers", [
            "title" => ["encrypt" => $title],
            "gender" => ["encrypt" => $gender],
            "fname" => ["encrypt" => $fname],
            "lname" => ["encrypt" => $lname],
            "email" => ["encrypt" => $email],
            "contact" => ["encrypt" => $contact],
            "address" => ["encrypt" => $address],
            "postcode" => ["encrypt" => $postcode],
            "city" => ["encrypt" => $city],
        ], [
            "id" => $id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id']
        ]);
    } else {
        $save = $db->insert("customers", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "title" => ["encrypt" => $title],
            "gender" => ["encrypt" => $gender],
            "fname" => ["encrypt" => $fname],
            "lname" => ["encrypt" => $lname],
            "email" => ["encrypt" => $email],
            "contact" => ["encrypt" => $contact],
            "address" => ["encrypt" => $address],
            "postcode" => ["encrypt" => $postcode],
            "city" => ["encrypt" => $city],
        ]);
    }

    if ($save) {
        $customers = $db->select("customers", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
        ]);

        returnSuccess("Customer saved successfully", [
            "redirect" => "view-customer",
            "customer" => $customers
        ]);
    } else {
        returnError("Error saving customer. Please try again.");
    }
}
