<?php
require_once('includes/db.php');
$page_name = 'Invoice Services';

$JS_FILES_ = [];

$invoice_id = _get_param("id");

$staff_id = LOGGED_IN_USER_ID;
$company_id = LOGGED_IN_USER['company_id'];
$agency_id  = LOGGED_IN_USER['agency_id'];

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

// Fetch Services for this invoice
$services_data = $db->query("SELECT s.text AS service_name
    FROM invoice_items ii
    INNER JOIN services s ON ii.services_id = s.id
    WHERE ii.invoice_id = '$invoice_id'
", ["select_query" => true]);

if (!$services_data) $services_data = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <div class="pull-away">
                    <p class="text-white">Invoice Services</p>
                    <a href="assigned-vehicles">Back to Assigned Vehicles</a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <?php if ($services_data) { ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Service Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($services_data as $service) { ?>
                                    <tr>
                                        <td><?= $count; ?></td>
                                        <td><?= htmlspecialchars($service['service_name']); ?></td>
                                    </tr>
                                <?php $count++;
                                } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p class="text-center m-3">No services found for this invoice.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('./includes/js.php'); ?>
</body>

</html>