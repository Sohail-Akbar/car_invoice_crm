<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    _DIR_ . "js/select2.min.js",
];
$CSS_FILES_ = [
    _DIR_ . "css/select2.min.css",
];

$email_template_head = $db->select("email_template_head", "id,email_title", [
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id']
], [
    "order_by" => "id desc"
]);
if (empty($email_template_head)) $email_template_head = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .custom-heading::after {
            width: 45%;
        }

        .custom-heading::before {
            left: 45%;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-role-container" id="mainContent">
        <div class="card">
            <div class="card-body">
                <h3 class="heading mb-5 custom-heading">Define/Update Email Templates</h3>
                <form action="role" method="POST" class="ajax_form reset" data-reset="reset">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <span class="label">Email Template Head:</span>
                                <div class="d-flex">
                                    <select class="form-control select2-list" name="email_title">
                                        <option value="23">234</option>
                                        <?php foreach ($email_template_head as $head) { ?>
                                            <option value="<?= $head['id'] ?>"><?= $head['email_title'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="content-center">
                                        <button type="button" class="btn text-white d-flex ml-3 br-5">+ &nbsp; Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="saveRole" value="<?= bc_code(); ?>">
                            <button class="btn br-5" type="submit"><i class="fas fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive mt-5">
            <table class="table table-striped dataTable">
                <thead style="background: var(--webMainColor);color: white;">
                    <tr>
                        <th>#</th>
                        <th>Text</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>