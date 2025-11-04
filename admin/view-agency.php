<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$agency_data = "SELECT u.*, a.name as agency_name, a.contact as agency_contact, a.address as agency_address, a.agency_logo, a.email as agency_email
                        FROM users AS u
                        LEFT JOIN agencies AS a ON u.company_id = a.company_id
                        WHERE u.company_id = a.company_id AND u.agency_id = a.id AND u.user_id = '" . LOGGED_IN_USER_ID . "'
                        ORDER BY u.id DESC";
$agency_data = $db->query($agency_data, ["select_query" => true]);
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
                    <p>Agencies</p>
                    <a href="add-agency">Add New Agency</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    if ($agency_data) {
                    ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Agency Details</th>
                                    <th>Agency Details</th>
                                    <th>Agency Logo</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($agency_data as $user) {
                                    if ($user['id'] === LOGGED_IN_USER_ID)
                                        continue; ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <strong>Agency Name: </strong>
                                            <?= $user['agency_name'] ?><br>
                                            <strong>Agency Contact :</strong>
                                            <?= $user['agency_contact'] ?><br>
                                            <strong>Agency Address: </strong>
                                            <?= $user['agency_address'] ?>
                                        </td>
                                        <td>
                                            <strong>Full Name: </strong>
                                            <?= $user['title'] . "  " . $user['name'] ?><br>
                                            <strong>Email :</strong>
                                            <?= $user['email'] ?><br>
                                            <strong>Contact Number: </strong>
                                            <?= $user['contact'] ?>
                                        </td>
                                        <td class="content-center">
                                            <?php if (isset($user['agency_logo'])) { ?>
                                                <img src="<?= _DIR_ . "uploads/" . $user['agency_logo'] ?>" style="width:100px;">
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="text-white p-1 bold small-font <?php
                                                                                        if ($user['verify_status'] != '1') echo 'bg-warning text-dark';
                                                                                        else echo 'bg-success'; ?>">
                                                <?php
                                                if ($user['verify_status'] == '1') echo 'Verified';
                                                else echo 'unverified';
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="align-center child-el-margin-x">
                                                <a class="no-btn-styles text-success cp" href="add-agency?id=<?= $user['id'] ?>"><i class="fas fa-edit"></i></a>
                                                <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $user['id']; ?>" data-action="user"><i class="fas fa-trash-alt"></i></button>
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