<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "customer-profile.js",
    _DIR_ . "js/select2.min.js",
    _DIR_ . "js/jquery.dataTables.min.js"
];
$CSS_FILES_ = [
    "customer-profile.css",
    _DIR_ . "css/select2.min.css",
    _DIR_ . "css/jquery.dataTables.min.css"
];


$get_id = $_GET['id'];

$customer = $db->select_one("users", "*", [
    "id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "customer"
]);

$invoice = $db->select("invoices", "*", [
    "customer_id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$company = $db->select_one("companies", "*", [
    "id" => LOGGED_IN_USER['company_id']
]);


$total_paid = 0;
$total_due = 0;

foreach ($invoice as $inv) {
    $total_paid += $inv['paid_amount'];
    $total_due  += $inv['due_amount'];
}

$total_invoice = count($invoice);



$agency_id = LOGGED_IN_USER['agency_id'];
$assigned_staff = $db->query("
    SELECT 
        cs.id,
        cs.staff_id,
        cs.assignment_date,
        u.fname,
        u.lname,
        u.email,
        u.contact,
        u.title
    FROM customer_staff cs
    INNER JOIN users u ON cs.staff_id = u.id
    WHERE cs.customer_id = $get_id
      AND cs.company_id = " . LOGGED_IN_USER['company_id'] . "
      AND cs.agency_id = $agency_id
      AND cs.is_active = 1
      AND u.type = 'staff'
", [
    "select_query" => true,
]);


// Customer Cars
$cars = $db->select("customer_car_history", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1,
    "customer_id" => $get_id,
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        <?php
        if (LOGGED_IN_USER['type']  === "customer") { ?>.sidebar {
            display: none;
        }

        .all-content {
            margin-left: 0 !important;
        }

        .navbar {
            padding-left: 20px;
        }

        <?php   }
        ?>
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="container">
            <?php
            // Header
            if (LOGGED_IN_USER['type'] === "agency") {
                require_once "./components/customer-profile/header.php";
            }
            // Profile info
            require_once "./components/customer-profile/profile-info.php";
            // tabs
            require_once "./components/customer-profile/tabs.php";
            // Overview
            require_once "./components/customer-profile/vehicles.php";
            // Invoice
            require_once "./components/customer-profile/invoices.php";
            // Proforma Invoice
            require_once "./components/customer-profile/proforma-invoices.php";
            // Notes
            require_once "./components/customer-profile/notes.php";
            ?>
        </div>
    </div>

    <!-- View Work Carried -->

    <div class="modal fade view-work-carried-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">View Work Carried</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <div id="carsInfoContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Invoices -->
    <div class="modal fade add-customer-notes-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 60%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">View Work Carried</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <form action="customer" method="POST" class="ajax_form reset" data-reset="reset">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span class="label">Note</span>
                                    <textarea name="note" rows="5" class="form-control w-100" required></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <input type="hidden" name="addCustomerNotes" value="<?= bc_code(); ?>">
                                <input type="hidden" name="customer_id" value="<?= $get_id; ?>">
                                <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const _GET = <?= json_encode($_GET); ?>;
        const SITE_URL = '<?= SITE_URL ?>';
        const LOGIN_TYPE = '<?= LOGGED_IN_USER['type'] ?>';
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>