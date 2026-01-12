<?php
require_once('includes/db.php');
$page_name = 'Completed Task';

$JS_FILES_ = [
    "customer.js",
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];


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
                            "data": null,
                            "render": function(data, type, row, meta) {
                                return meta.settings._iDisplayStart + meta.row + 1;
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

                                if (data == "Completed") {
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