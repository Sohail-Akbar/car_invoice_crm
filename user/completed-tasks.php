<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    "customer.js",
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];


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
                    <p class="text-white">Completed Tasks</p>
                    <a href="assigned-vehicles">Back to Assigned Tasks</a>
                </div>
            </div>
            <div class="card-body">
                <div class="dataTable-container">
                    <div class="table-responsive">
                        <table class="table table-striped" id="ComplateTaskTable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Invoice No</th>
                                    <th>Completed At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            if ($("#ComplateTaskTable").length) {
                $('#ComplateTaskTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "controllers/staff?fetchCompletedTasks=true",
                        "type": "POST"
                    },
                    "pageLength": 10,
                    "columns": [{
                            "data": "id",
                            "render": function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            "data": "customer_name"
                        },
                        {
                            "data": "vehicle"
                        },
                        {
                            "data": "invoice_no"
                        },
                        {
                            "data": "completed_at"
                        },
                        {
                            "data": "status",
                            "render": function(data) {
                                let text = "";
                                let color = "";

                                if (data == 0) {
                                    text = "Completed";
                                    color = "bg-success";
                                } else {
                                    text = "Pending";
                                    color = "bg-warning text-dark";
                                }

                                return `<span class="text-white p-1 bold small-font ${color}">${text}</span>`;
                            }

                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return `<a href="view-task.php?id=${row.invoice_id}" class="text-success" title="View Invoice">
                                    <i class="fa fa-eye"></i>
                                </a>`;
                            }
                        }
                    ],
                    "scrollX": true,
                });
            }
        });
    </script>
</body>

</html>