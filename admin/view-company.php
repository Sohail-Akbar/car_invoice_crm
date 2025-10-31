<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$company_admin_sql = "SELECT u.*, c.company_name, c.company_contact, c.company_address, c.company_logo, c.company_email
                        FROM users AS u
                        LEFT JOIN companies AS c ON u.company_id = c.id
                        WHERE (u.agency_id IS NULL OR u.agency_id = '') AND u.type = 'admin' AND u.user_id = '" . LOGGED_IN_USER_ID . "'";
$company_admin_data = $db->query($company_admin_sql, ["select_query" => true]);
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
                    <p>Companies</p>
                    <a href="add-company">Add New Company</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    if ($company_admin_data) {
                    ?>
                        <table class="table table-striped dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Company Details</th>
                                    <th>Admin Details</th>
                                    <th>Company Logo</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($company_admin_data as $user) {
                                    if ($user['id'] === LOGGED_IN_USER_ID)
                                        continue; ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <strong>Company Name: </strong>
                                            <?= $user['company_name'] ?><br>
                                            <strong>Company Contact :</strong>
                                            <?= $user['company_contact'] ?><br>
                                            <strong>Company Address: </strong>
                                            <?= $user['company_address'] ?>
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
                                            <?php if (isset($user['company_logo'])) { ?>
                                                <img src="<?= _DIR_ . "uploads/" . $user['company_logo'] ?>" style="width:100px;">
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
                                                <a class="no-btn-styles text-success cp" href="add-company?id=<?= $user['id'] ?>"><i class="fas fa-edit"></i></a>
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