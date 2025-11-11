<?php

define('DIR', '../');
require_once('../includes/db.php');
require_once("../../includes/Classes/TCFunctions.php");

if (isset($_POST['addAgency'])) {
    // Get form data
    $agency_name = arr_val($_POST, "agency_name", "");
    $agency_address = arr_val($_POST, "agency_address", "");
    $agency_contact = arr_val($_POST, "agency_contact", "");
    $agency_email = arr_val($_POST, "agency_email", "");
    $title = arr_val($_POST, "title", "");
    $gender = arr_val($_POST, "gender", "");
    $first_name = arr_val($_POST, "first_name", "");
    $last_name = arr_val($_POST, "last_name", "");
    $contact = arr_val($_POST, "contact", "");
    $email = arr_val($_POST, "email", "");
    $address = arr_val($_POST, "address", "");
    $city = arr_val($_POST, "city", "");
    $lat = arr_val($_POST, "lat", "");
    $lng = arr_val($_POST, "lng", "");
    $postcode = arr_val($_POST, "postcode", "");
    $password = arr_val($_POST, "password", "");

    $agency_user_id = arr_val($_POST, "agency_user_id", null);
    $company_login_id = arr_val($_POST, "company_login_id", null);
    $_agency_id = arr_val($_POST, "agency_id", null);

    // File upload
    $file = $_fn->upload_file('agency_logo', [
        "multiple" => false,
        "path" => "../../uploads"
    ]);

    $company_logo = ""; // default
    if ($file['status'] === "success" && isset($file['filename'])) {
        $company_logo = $file['filename'];
    } elseif ($file['status'] !== "success" && !empty($_FILES['agency_logo']['name'])) {
        returnError('File upload failed');
    }

    // Company data array
    $company_data = [
        "name" =>  $agency_name,
        "address" =>  $agency_address,
        "contact" =>  $agency_contact,
        "email" =>  $agency_email,
        "company_id" => $company_login_id
    ];

    if ($company_logo !== "") {
        $company_data["agency_logo"] =  $company_logo;
    }

    $agency_id = "";
    // Company insert/update
    if ($agency_user_id) {
        // Update existing company
        $db->update("agencies", $company_data, ["id" => $_agency_id, "company_id" => $company_login_id]);
        $agency_id = $_agency_id;
    } else {
        // Insert new company
        $agency_id = $db->insert("agencies", $company_data);
    }

    // User data array
    $user_data = [
        "company_id" => $company_login_id,
        "agency_id" => $agency_id,
        "title" =>  $title,
        "gender" =>  $gender,
        "fname" =>  $first_name,
        "lname" =>  $last_name,
        "name" =>  $first_name . " " . $last_name,
        "email" => $email,
        "address" =>  $address,
        "city" =>  $city,
        "lat" =>  $lat,
        "lng" =>  $lng,
        "postcode" =>  $postcode,
        "contact" =>  $contact,
        "type" => "agency",
        "image" => "avatar.png",
        "verify_status" => "1",
        "is_admin" => "0",
        "user_id" => LOGGED_IN_USER_ID
    ];

    // Password hash only if provided
    if (!empty($password)) {
        $user_data["password"] =  password_hash($password, PASSWORD_BCRYPT);
    }

    // User insert/update
    if ($agency_user_id) {
        $db->update("users", $user_data, ["id" => $agency_user_id, "company_id" => $company_login_id, "agency_id" => $agency_id]);
    } else {
        $db->insert("users", $user_data);
    }

    if ($agency_user_id) {
        returnSuccess("Data saved successfully", [
            "redirect" => "view-agency"
        ]);
    } else {
        returnSuccess("Data saved successfully");
    }
}
