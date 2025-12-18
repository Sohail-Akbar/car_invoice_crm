<?php

// Get all assigned task
if (isset($_POST['getAssignedTask'])) {
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
        cch.reg_number,
        cch.make,
        cch.model,
        cch.primaryColour,
        cch.fuelType,
        i.status,
        i.due_date,
        i.total_amount
    FROM customer_staff cs
    INNER JOIN users u ON cs.customer_id = u.id
    INNER JOIN invoices i ON cs.invoice_id = i.id
    INNER JOIN customer_car_history cch ON cch.customer_id = u.id
    WHERE cs.staff_id = $staff_id
      AND cs.is_active = 1
      AND u.type = 'customer'
      AND cs.company_id = $company_id
      AND cs.agency_id = $agency_id
      AND cch.is_active = 1
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


// Fetch Complate Task 
// Get all assigned task
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
        cch.reg_number,
        cch.make,
        cch.model,
        cch.primaryColour,
        cch.fuelType,
        i.status,
        i.due_date,
        i.total_amount
    FROM customer_staff cs
    INNER JOIN users u ON cs.customer_id = u.id
    INNER JOIN invoices i ON cs.invoice_id = i.id
    INNER JOIN customer_car_history cch ON cch.customer_id = u.id
    WHERE cs.staff_id = $staff_id
      AND cs.is_active = 1
      AND u.type = 'customer'
      AND cs.company_id = $company_id
      AND cs.agency_id = $agency_id
      AND cch.is_active = 1
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
