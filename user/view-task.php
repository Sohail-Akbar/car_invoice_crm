<?php
require_once('includes/db.php');
$page_name = 'View All Tasks';

$JS_FILES_ = [];

$invoice_id = _get_param("id");

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