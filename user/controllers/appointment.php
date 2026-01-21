
<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


if (isset($_POST['add_booking_appointment'])) {

    // DEBUG (temporary)
    // print_r($_POST); die;

    $customer_id = trim($_POST['customer_id']);
    $vehicle_id = trim($_POST['mot_id']);

    // 🔹 Convert datetime
    $startTime = date('Y-m-d H:i:s', strtotime($_POST['startTime']));
    $endTime   = date('Y-m-d H:i:s', strtotime($_POST['endTime']));

    $appointment_notes = trim($_POST['appointment_notes']);

    if (empty($customer_id)) {
        returnError("Customer is required");
        die();
    }

    if (empty($vehicle_id)) {
        returnError("Vehicle is required");
        die();
    }

    if (!$startTime || !$endTime) {
        returnError("Invalid date/time format");
        die();
    }

    $vehicle = $db->select_one('customer_car_history', "reg_number,make,model", [
        'id' => $vehicle_id,
        'customer_id' => $customer_id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id']
    ]);

    $title = $vehicle['reg_number'] . ' - ' . $vehicle['make'] . ' ' . $vehicle['model'];

    $save = $db->insert("appointments", [
        'customer_id' => $customer_id,
        'vehicle_id' => $vehicle_id,
        'title' => $title,
        'start_datetime' => $startTime,   // ✅ FIXED
        'end_datetime' => $endTime,       // ✅ FIXED
        'description' => $appointment_notes,
        'company_id' => LOGGED_IN_USER['company_id'],
        'agency_id' => LOGGED_IN_USER['agency_id'],
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    if ($save) {
        returnSuccess("Appointment added successfully", ["redirect" => ""]);
    } else {
        returnError("Failed to add appointment");
    }
}


// fetch appointments
if (isset($_POST['fetchAppointments'])) {

    $start = $_POST['start'];
    $end   = $_POST['end'];

    $startDate = date('Y-m-d 00:00:00', strtotime($start));
    $endDate   = date('Y-m-d 23:59:59', strtotime($end));

    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id  = LOGGED_IN_USER['agency_id'];

    $sql = "
        SELECT 
            a.id,
            a.start_datetime,
            a.end_datetime,
            a.description,
            a.title,
            c.fname,
            c.lname
        FROM appointments a
        LEFT JOIN users c ON c.id = a.customer_id AND c.type = 'customer'
        WHERE a.company_id = '$company_id'
          AND a.agency_id = '$agency_id'
          AND a.start_datetime < '$endDate'
          AND a.end_datetime > '$startDate'
        ORDER BY a.start_datetime ASC
    ";

    $appointments = $db->query($sql, ["select_query" => true]);

    $events = [];

    foreach ($appointments as $row) {
        $customerName = trim($row['fname'] . ' ' . $row['lname']);

        $events[] = [
            "id" => $row['id'],
            // 🔹 TITLE with Customer Name
            "title" => $customerName . ' - ' . $row['title'],
            "start" => $row['start_datetime'],
            "end"   => $row['end_datetime'],
            // 🔹 DESCRIPTION
            "description" => $row['description'],
            "allDay" => false
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}


// get appointment Details
if (isset($_POST['fetchSingleAppointment'])) {
    $appointment_id = $_POST['appointment_id'];

    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id  = LOGGED_IN_USER['agency_id'];

    if (!is_numeric($appointment_id)) {
        returnError("Invalid appointment ID");
        die();
    }

    //  get customer details with appointment details 
    $sql = "
        SELECT 
            a.id,
            a.customer_id,
            a.vehicle_id,
            a.start_datetime,
            a.end_datetime,
            a.description,
            a.title,
            c.fname,
            c.lname,
            c.email,
            c.contact
        FROM appointments a
        LEFT JOIN users c ON c.id = a.customer_id AND c.type = 'customer'
        WHERE a.company_id = '$company_id'
          AND a.agency_id = '$agency_id'
          AND a.id = '$appointment_id'
    ";

    $appointment = $db->query($sql, ["select_query" => true]);

    if ($appointment) {
        returnSuccess("Appointment fetched successfully", ['appointment' => $appointment[0]]);
    } else {
        returnError("Appointment not found");
    }
}
