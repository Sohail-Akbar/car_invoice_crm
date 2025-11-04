<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$customers_data = $db->select("customers", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
], ["order_by" => "id desc"]);
if (!$customers_data) $customers_data = [];
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
                    if ($customers_data) {
                    ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>customer Details</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($customers_data as $customer) { ?>
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
                                            <?= $customer['address'] . ", " . $customer['postcode'] . ", " . $customer['city'] ?>
                                        </td>
                                        <td>
                                            <span class="text-white p-1 bold small-font <?php
                                                                                        if ($customer['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                        else echo 'bg-success'; ?>">
                                                <?php
                                                if ($customer['is_active'] == '1') echo 'Active';
                                                else echo 'Inactive';
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="align-center child-el-margin-x">
                                                <a class="no-btn-styles text-success cp" href="add-customer?id=<?= $customer['id'] ?>"><i class="fas fa-edit"></i></a>
                                                <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $customer['id']; ?>" data-action="customer"><i class="fas fa-trash-alt"></i></button>
                                                <a href="customer-profile?id=<?= $customer['id'] ?>" class="text-success" title="View Customer Profile">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $count++;
                                } ?>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>