<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


// vat Update
if (isset($_POST['saveVat'])) {
    $vat = $_POST['vat'];

    // Update if exists
    $db->update("agencies", [
        "vat_percentage" => $vat
    ], [
        "id" => LOGGED_IN_USER['agency_id'],
        "company_id" => LOGGED_IN_USER['company_id']
    ]);

    returnSuccess("Update VAT% Successfully");
}

// Discount Update
if (isset($_POST['saveDiscount'])) {
    $discount = $_POST['discount'];

    // Update if exists
    $db->update("agencies", [
        "discount_percentage" => $discount
    ], [
        "id" => LOGGED_IN_USER['agency_id'],
        "company_id" => LOGGED_IN_USER['company_id']
    ]);

    returnSuccess("Update VAT% Successfully");
}

// API Kay 
if (isset($_POST['saveSmsApiKey'])) {

    $apiKey = trim($_POST['sms_api_key']);

    // Encryption key – isko .env me rakho
    $secretKey = "b8f9e1c2d3a4f567b1c2d3e4f5a6b7c8";

    // Encrypt API key
    $encryptedKey = openssl_encrypt($apiKey, "AES-256-CBC", $secretKey, 0, "1234567890123456");

    $db->update("agencies", [
        "sms_api_key" => $encryptedKey
    ], [
        "id" => LOGGED_IN_USER['agency_id'],
        "company_id" => LOGGED_IN_USER['company_id']
    ]);

    returnSuccess("SMS API Key updated successfully");
}


// save Company Discount
if (isset($_POST['saveCompanyDiscount'])) {
    $title = arr_val($_POST, "title", "");
    $discount = arr_val($_POST, "discount", "");
    $id = arr_val($_POST, "id", "");

    if (empty($title)) returnError("Title is required");
    if (empty($discount)) returnError("Discount is required");

    $save  = null;
    if ($id) {
        $save = $db->update("discount", [
            "title" => $title,
            "discount" => $discount
        ], [
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "company_id" => LOGGED_IN_USER['company_id'],
            "id" => $id
        ]);
    } else {
        $save = $db->insert("discount", [
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "company_id" => LOGGED_IN_USER['company_id'],
            "title" => $title,
            "discount" => $discount
        ]);
    }

    if ($save) {
        returnSuccess("Data save successfully", [
            "redirect" => "discount"
        ]);
    } else {
        returnError("Something went wrong");
    }
}
