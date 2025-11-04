<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$mot_history = $db->select("customer_car_history", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
], [
    "order_by" => "id desc"
]);
if (!$mot_history) $mot_history = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="card">
            <div class="card-header">
                <div class="pull-away">
                    <p>Customers</p>
                    <a href="add-customer">Add New Customer</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    if ($mot_history) {
                    ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>customer Details</th>
                                    <th>Registration No</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($mot_history as $mot_history) {
                                    $customer = $db->select_one("customers", "*", [
                                        "id" => $mot_history['customer_id'],
                                        "company_id" => LOGGED_IN_USER['company_id'],
                                        "agency_id" => LOGGED_IN_USER['agency_id']
                                    ]);
                                    if (!$customer) $customer = [];
                                ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <strong>Name: </strong>
                                            <?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?><br>
                                            <strong>Email :</strong>
                                            <?= $customer['email'] ?><br>
                                            <strong>Contact :</strong>
                                            <?= $customer['contact'] ?><br>
                                        </td>
                                        <td>
                                            <?= $mot_history['reg_number'] ?>
                                        </td>
                                        <td>
                                            <strong>Make: </strong>
                                            <?= $mot_history['make'] ?><br>
                                            <strong>Model :</strong>
                                            <?= $mot_history['model'] ?><br>
                                            <strong>Engine Size :</strong>
                                            <?= $mot_history['engineSize'] ?><br>
                                            <strong>Expiry Date :</strong>
                                            <?= $mot_history['expiryDate'] ?><br>
                                        </td>
                                        <td>
                                            <span class="text-white p-1 bold small-font <?php
                                                                                        if ($mot_history['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                        else echo 'bg-success'; ?>">
                                                <?php
                                                if ($customer['is_active'] == '1') echo 'Active';
                                                else echo 'Inactive';
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="align-center child-el-margin-x">
                                                <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $mot_history['id']; ?>" data-action="customer_car_history"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $count++;
                                } ?>
                            </tbody>
                        </table>
                    <?php
                    } else {
                        echo "<p>No MOT history found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>