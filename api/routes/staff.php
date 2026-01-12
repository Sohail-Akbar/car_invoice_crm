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
    $services_data = $db->query("SELECT s.text AS service_name
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
