<?php

// Get all assigned task
if (isset($_POST['getAssignedTask'])) {
    $user =  validateBearerToken("token");

    $staff_id = (int) $user['id'];
    $company_id = (int) $user['company_id'];
    $agency_id  = (int) $user['agency_id'];

    // Fetch assigned customers and vehicles
    $assigned_tasks = $db->query("
    SELECT 
        cs.id AS cs_id,
        cs.video_url,
        u.id AS customer_id,
        u.fname,
        u.lname,
        u.contact,
        u.email,
        i.id AS invoice_id,
        i.invoice_no,
        MIN(cch.reg_number) AS reg_number,
        MIN(cch.make) AS make,
        MIN(cch.model) AS model,
        MIN(cch.primaryColour) AS primaryColour,
        MIN(cch.fuelType) AS fuelType,
        cs.is_active,
        i.status,
        i.due_date,
        i.total_amount
    FROM customer_staff cs
    INNER JOIN users u ON cs.customer_id = u.id 
    INNER JOIN invoices i ON cs.invoice_id = i.id
    INNER JOIN customer_car_history cch 
        ON cch.customer_id = u.id 
        AND cch.is_active = 1
    WHERE cs.staff_id = $staff_id
      AND cs.is_active = 1
      AND u.type = 'customer'
      AND cs.company_id = $company_id
      AND cs.agency_id = $agency_id
    GROUP BY i.id
    ORDER BY i.due_date ASC
", [
        "select_query" => true,
    ]);


    echo json_encode([
        "status" => "success",
        "message" => "Fetch all Assigned Tasks successfully",
        "data" => $assigned_tasks
    ]);
    die;
}

// view task 
if (isset($_POST['viewTask'])) {
    $user =  validateBearerToken("token");

    $invoice_id = $_POST["invoice_id"];
    // Fetch Services for this invoice
    $services_data = $db->query("SELECT s.text AS service_name, ii.id AS invoice_item_id, ii.is_completed
    FROM invoice_items ii
    INNER JOIN services s ON ii.services_id = s.id
    WHERE ii.invoice_id = '$invoice_id'
", ["select_query" => true]);

    if (!$services_data) {
        echo json_encode([
            "status" => "success",
            "message" => "No services found for this invoice.",
            "data" => []
        ]);
        exit;
    }
    echo json_encode([
        "status" => "success",
        "message" => "Fetch Services for the invoice successfully",
        "data" => $services_data
    ]);
}

// is completed service
if (isset($_POST['markServiceCompleted'])) {
    $user =  validateBearerToken("token");
    $invoice_item_id = intval($_POST['invoice_item_id']);
    if (!$invoice_item_id) {
        returnError("Invoice Item ID is required");
        die;
    }
    // Update the invoice_items record as completed
    $update = $db->update("invoice_items", [
        "is_completed" => 1, // mark as completed
    ], [
        "id" => $invoice_item_id
    ]);
    if ($update) {
        echo json_encode([
            "status" => "success",
            "message" => "Service marked as completed",
            "data" => []
        ]);
        die;
    } else {
        returnError("Failed to update service");
        die;
    }
}


// Fetch Complate Task 
if (isset($_POST['getCompleteTask'])) {
    $user =  validateBearerToken("token");

    $staff_id = $user['id'];
    $company_id = $user['company_id'];
    $agency_id  = $user['agency_id'];

    // Fetch assigned customers and vehicles
    $assigned_tasks = $db->query("
    SELECT 
        cs.id AS cs_id,
        cs.video_url,
        u.id AS customer_id,
        u.fname,
        u.lname,
        u.contact,
        u.email,
        i.id AS invoice_id,
        i.invoice_no,
        MIN(cch.reg_number) AS reg_number,
        MIN(cch.make) AS make,
        MIN(cch.model) AS model,
        MIN(cch.primaryColour) AS primaryColour,
        MIN(cch.fuelType) AS fuelType,
        cs.is_active,
        i.status,
        i.due_date,
        i.total_amount
    FROM customer_staff cs
    INNER JOIN users u ON cs.customer_id = u.id 
    INNER JOIN invoices i ON cs.invoice_id = i.id
    INNER JOIN customer_car_history cch 
        ON cch.customer_id = u.id 
        AND cch.is_active = 1
    WHERE cs.staff_id = $staff_id
      AND cs.is_active = 0
      AND u.type = 'customer'
      AND cs.company_id = $company_id
      AND cs.agency_id = $agency_id
    GROUP BY i.id
    ORDER BY i.due_date ASC
", [
        "select_query" => true,
    ]);


    echo json_encode([
        "status" => "success",
        "message" => "Fetch all Complete Tasks successfully",
        "data" => $assigned_tasks
    ]);
    die;
}


// Mark Task Done
if (isset($_POST['markTaskDone'])) {
    $user =  validateBearerToken("token");
    $cs_id = intval($_POST['cs_id']);
    $video_url = arr_val($_POST, 'video_url', '');

    if (!$cs_id) {
        returnError("Customer Staff ID is required");
        die;
    }

    // Update the customer_staff record as completed
    $update = $db->update("customer_staff", [
        "is_active" => 0, // mark as done
        "completed_at" => date("Y-m-d H:i:s"),
        "video_url" => $video_url
    ], [
        "id" => $cs_id
    ]);

    if ($update) {
        echo json_encode([
            "status" => "success",
            "message" => "Task marked as done",
            "data" => []
        ]);
        die;
    } else {
        returnError("Failed to update task");
        die;
    }
}
