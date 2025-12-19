<?php
require_once('includes/db.php');
$page_name = 'Add Services';

$JS_FILES_ = [];

$service_id = isset($_GET['id']) ? $_GET['id'] : null;

$services_sql = "SELECT * FROM services WHERE company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " ORDER BY id DESC";
$services_data = $db->query($services_sql, ["select_query" => true]);

$service_data = [];
if ($service_id) {
    $service_sql = "SELECT * FROM services WHERE id = '$service_id' AND company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " LIMIT 1";
    $service_data = $db->query($service_sql, ["select_query" => true, "values" => [$service_id]]);
    if ($service_data) {
        $service_data = $service_data[0];
    } else {
        header("Location: add-services");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-services-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-3 custom-heading">Add New Service</h3>
            <form action="services" method="POST" class="ajax_form reset" data-reset="reset">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span class="label">Text</span>
                            <input type="text" class="form-control" value="<?= arr_val($service_data, "text", "") ?>" name="text" required data-length="[1,250]">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span class="label">Amount</span>
                            <input type="text" class="form-control" value="<?= arr_val($service_data, "amount", "") ?>" name="amount" required data-length="[1,250]">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="saveService" value="<?= bc_code(); ?>">
                        <?php if ($service_id) { ?>
                            <input type="hidden" name="id" value="<?= $service_id; ?>">
                        <?php } ?>
                        <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive mt-5">
            <table class="table table-striped dataTable">
                <thead style="background: var(--webMainColor);color: white;">
                    <tr>
                        <th>#</th>
                        <th>Text</th>
                        <th>Amount</th>
                        <th class="d-none">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$services_data) {
                        echo '<tr><td colspan="4" class="text-center">No Services Found</td></tr>';
                    } else {
                        $count = 1;
                        foreach ($services_data as $service) { ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?= $service['text'] ?></td>
                                <td><?= $service['amount'] ?></td>
                                <td class="d-none">
                                    <span class="text-white p-1 bold small-font <?php
                                                                                if ($service['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                else echo 'bg-success'; ?>">
                                        <?php
                                        if ($service['is_active'] == '1') echo 'Active';
                                        else echo 'Inactive';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="align-center child-el-margin-x">
                                        <a class="no-btn-styles text-success cp" href="add-services?id=<?= $service['id'] ?>"><i class="fas fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                    <?php $count++;
                        }
                    } ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>