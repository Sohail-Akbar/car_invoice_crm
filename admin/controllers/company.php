<?php

define('DIR', '../');
require_once('../includes/db.php');
require_once("../../includes/Classes/TCFunctions.php");

if (isset($_POST['addCompany'])) {
    // Get form data
    $company_name = arr_val($_POST, "company_name", "");
    $company_address = arr_val($_POST, "company_address", "");
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
        "company_name" => ["encrypt" => $company_name],
        "company_address" => ['encrypt' => $company_address],
        "company_contact" => ["encrypt" => $company_contact],
        "company_email" =>  $company_email,
    ];

    if ($company_logo !== "") {
        $company_data["company_logo"] = ["encrypt" => $company_logo];
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
        "title" => ["encrypt" => $title],
        "gender" => ["encrypt" => $gender],
        "fname" => ["encrypt" => $first_name],
        "lname" => ["encrypt" => $last_name],
        "name" => ["encrypt" => $first_name . " " . $last_name],
        "email" => $email,
        "address" => ["encrypt" => $address],
        "city" => ["encrypt" => $city],
        "lat" => ["encrypt" => $lat],
        "lng" => ["encrypt" => $lng],
        "postcode" => ["encrypt" => $postcode],
        "contact" => ["encrypt" => $contact],
        "type" => "admin",
        "image" => "avatar.png",
        "verify_status" => "1",
        "is_admin" => "1",
        "user_id" => LOGGED_IN_USER_ID
    ];

    // Password hash only if provided
    if (!empty($password)) {
        $user_data["password"] = ["encrypt" => password_hash($password, PASSWORD_BCRYPT)];
    }

    // User insert/update
    if ($user_id) {
        $db->update("users", $user_data, ["id" => $user_id]);
    } else {
        $db->insert("users", $user_data);
    }

    if ($user_id) {
        returnSuccess("Data saved successfully", [
            "redirect" => "view-company"
        ]);
    } else {
        returnSuccess("Data saved successfully");
    }
}
