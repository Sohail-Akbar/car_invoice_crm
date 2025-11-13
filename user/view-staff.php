<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$staffs_data = $db->select("users", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "staff"
], ["order_by" => "id desc"]);
if (!$staffs_data) $staffs_data = [];
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
                    <p>Staffs</p>
                    <a href="add-staff">Add New Staff</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    if ($staffs_data) {
                    ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Staff Details</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($staffs_data as $staff) { ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <strong>Name: </strong>
                                            <?= $staff['title'] . " " . $staff['fname'] . " " . $staff['lname'] ?><br>
                                            <strong>Email :</strong>
                                            <?= $staff['email'] ?><br>
                                            <strong>Contact :</strong>
                                            <?= $staff['contact'] ?><br>
                                        </td>
                                        <td>
                                            <?= $staff['address'] . ", " . $staff['postcode'] . ", " . $staff['city'] ?>
                                        </td>
                                        <td>
                                            <span class="text-white p-1 bold small-font <?php
                                                                                        if ($staff['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                        else echo 'bg-success'; ?>">
                                                <?php
                                                if ($staff['is_active'] == '1') echo 'Active';
                                                else echo 'Inactive';
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="align-center child-el-margin-x">
                                                <a class="no-btn-styles text-success cp" href="add-staff?id=<?= $staff['id'] ?>"><i class="fas fa-edit"></i></a>
                                                <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $staff['id']; ?>" data-action="staff"><i class="fas fa-trash-alt"></i></button>
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