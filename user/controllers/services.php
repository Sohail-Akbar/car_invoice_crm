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
            "text" =>  $text,
            "amount" =>  $amount
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
            "text" =>  $text,
            "amount" =>  $amount
        ]);

        returnSuccess("Role created successfully", [
            "redirect" => "add-services"
        ]);
    }
}



if (isset($_POST['addNewServices'])) {
    $type = $_POST['type'] ?? 'general';
    $value = trim($_POST['value'] ?? '');

    if ($value === '') {
        echo json_encode(['status' => 'error', 'message' => 'Empty value']);
        exit;
    }

    // Check duplicate
    $existing = $db->select_one("services", "id", [
        "text" => $value,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id']
    ]);

    if ($existing) {
        returnError("Already exists");
        exit;
    }

    // Insert new service
    $insert_id = $db->insert("services", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "text" => $value,
        "amount" => 0
    ]);

    if ($insert_id) {
        // Fetch all active services
        $services = $db->select("services", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "is_active" => 1
        ]);

        returnSuccess([
            'id' => $insert_id,
            'text' => $value,
            "services" => $services
        ]);
    } else {
        returnError("DB insert failed");
    }
}


if (isset($_POST['updateAmount'])) {
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id === null || $amount === '') {
        returnError("Invalid input");
    } else {
        // Normalize amount (allow comma as decimal separator)
        $normalized = str_replace(',', '.', $amount);
        if (!is_numeric($normalized)) {
            returnError("Invalid amount");
        } else {
            $normalized = (float)$normalized;
            $updated = $db->update("services", [
                "amount" =>  $normalized
            ], [
                "id" => $id,
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id" => LOGGED_IN_USER['agency_id'],
                "is_active" => 1
            ]);

            if ($updated) {
                returnSuccess("Amount updated successfully", [
                    "id" => $id,
                    "amount" => $normalized
                ]);
            } else {
                returnError("Update failed or no changes made");
            }
        }
    }
}
