<?php

define('DIR', '../');
require_once('../includes/db.php');
require_once("../../includes/Classes/TCFunctions.php");

if (isset($_POST['addCompany'])) {
    // Get form data
    $company_name = arr_val($_POST, "company_name", "");
    $company_address = arr_val($_POST, "company_address", "");
    $company_lat = arr_val($_POST, "company_lat", "");
    $company_lng = arr_val($_POST, "company_lng", "");
    $company_city = arr_val($_POST, "company_city", "");
    $company_postcode = arr_val($_POST, "company_postcode", "");
    $company_contact = arr_val($_POST, "company_contact", "");
    $company_email = arr_val($_POST, "company_email", "");
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

    $user_id = arr_val($_POST, "user_id", null);
    $company_id = arr_val($_POST, "company_id", null);

    if (strlen($password) !== 6 && !$company_id) returnError("Password must be exactly 6 digits!");

    // File upload
    $file = $_fn->upload_file('company_logo', [
        "multiple" => false,
        "path" => "../../uploads"
    ]);

    $company_logo = ""; // default
    if ($file['status'] === "success" && isset($file['filename'])) {
        $company_logo = $file['filename'];
    } elseif ($file['status'] !== "success" && !empty($_FILES['company_logo']['name'])) {
        returnError('File upload failed');
    }

    // Company data array
    $company_data = [
        "company_name" =>  $company_name,
        "company_address" =>  $company_address,
        "company_contact" =>  $company_contact,
        "company_email" =>  $company_email,
        "company_lat" =>  $company_lat,
        "company_lng" =>  $company_lng,
        "company_city" =>  $company_city,
        "company_postcode" =>  $company_postcode,
        "created" => CREATED_AT
    ];

    if ($company_logo !== "") {
        $company_data["company_logo"] =  $company_logo;
    }

    // Company insert/update
    if ($company_id) {
        // Update existing company
        $db->update("companies", $company_data, ["id" => $company_id]);
    } else {
        // Insert new company
        $company_id = $db->insert("companies", $company_data);
    }

    // User data array
    $user_data = [
        "company_id" => $company_id,
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
        "type" => "admin",
        "image" => "avatar.png",
        "verify_status" => "1",
        "is_admin" => "1",
        "user_id" => LOGGED_IN_USER_ID,
        "date_added" => CREATED_AT
    ];

    // Password hash only if provided
    if (!empty($password)) {
        $user_data["password"] =  password_hash($password, PASSWORD_BCRYPT);
    }

    if ($company_logo !== "") {
        $user_data["image"] =  $company_logo;
    }
    // User insert/update
    if ($user_id) {
        $db->update("users", $user_data, ["id" => $user_id]);
    } else {
        $db->insert("users", $user_data);
    }

    returnSuccess("Data saved successfully", [
        "redirect" => "view-company"
    ]);
}
