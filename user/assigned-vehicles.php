<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];



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
    <style>
        .vehicles-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .vehicle-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .vehicle-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .vehicle-body p {
            margin: 5px 0;
            font-size: 14px;
        }

        .vehicle-body .status {
            padding: 2px 6px;
            border-radius: 4px;
            color: #fff;
            font-weight: 500;
            text-transform: capitalize;
        }

        .vehicle-body .status.unpaid {
            background-color: #dc3545;
        }

        .vehicle-body .status.paid {
            background-color: #28a745;
        }

        .vehicle-body .status.partial {
            background-color: #ffc107;
            color: #000;
        }

        .vehicle-body .status.cancelled {
            background-color: #6c757d;
        }

        .vehicle-footer {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-complete,
        .btn-view-invoice {
            flex: 1;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-complete {
            background-color: #28a745;
            color: #fff;
        }

        .btn-view-invoice {
            background-color: #007bff;
            color: #fff;
        }

        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .vehicles-list {
                flex-direction: column;
            }

            .vehicle-card {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <h1>Assigned Vehicles</h1>
        <div class="vehicles-list">
            <?php if (!empty($assigned_tasks)): ?>
                <?php foreach ($assigned_tasks as $task): ?>
                    <div class="vehicle-card">
                        <div class="vehicle-header">
                            <h3><?= htmlspecialchars($task['fname'] . ' ' . $task['lname']) ?></h3>
                            <span class="invoice">Invoice #: <?= htmlspecialchars($task['invoice_no']) ?></span>
                        </div>
                        <div class="vehicle-body">
                            <p><strong>Vehicle:</strong> <?= htmlspecialchars($task['make'] . ' ' . $task['model']) ?></p>
                            <p><strong>Reg. Number:</strong> <?= htmlspecialchars($task['reg_number']) ?></p>
                            <p><strong>Color:</strong> <?= htmlspecialchars($task['primaryColour']) ?></p>
                            <p><strong>Fuel Type:</strong> <?= htmlspecialchars($task['fuelType']) ?></p>
                            <p><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></p>
                            <p><strong>Contact:</strong> <?= htmlspecialchars($task['contact']) ?> | <?= htmlspecialchars($task['email']) ?></p>
                        </div>

                        <div class="vehicle-footer">
                            <form action="staff" method="POST" class="ajax_form">
                                <input type="hidden" name="cs_id" value="<?= $task['cs_id'] ?>">
                                <input type="hidden" name="markTaskDone" value="true">
                                <button type="submit" class="btn-complete">Mark Task Done</button>
                            </form>
                            <button class="btn-view-invoice" onclick="window.location='view-task?id=<?= $task['invoice_id'] ?>'">
                                View Task
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No assigned vehicles/tasks at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once('./includes/js.php'); ?>
</body>

</html>