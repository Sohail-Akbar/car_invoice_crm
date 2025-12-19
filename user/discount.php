<?php
require_once('includes/db.php');
$page_name = 'Discount';

$JS_FILES_ = [];

$discount_id = isset($_GET['id']) ? $_GET['id'] : null;

$discount_sql = "SELECT * FROM discount WHERE company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " ORDER BY id DESC";
$discount_data = $db->query($discount_sql, ["select_query" => true]);

$discount = [];
if ($discount_id) {
    $discount_sql = "SELECT * FROM discount WHERE id = '$discount_id' AND company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " LIMIT 1";
    $_discount = $db->query($discount_sql, ["select_query" => true]);
    if ($_discount) {
        $discount = $_discount[0];
    } else {
        header("Location: discount");
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
    <main class="main-content add-role-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-3 custom-heading">Discount</h3>
            <form action="settings" method="POST" class="ajax_form reset" data-reset="reset">
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="form-group">
                            <span class="label">Title:</span>
                            <input type="text" class="form-control" value="<?= arr_val($discount, "title", "") ?>" name="title" placeholder="Discount Title" required data-length="[1,250]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <span class="label">Discount%:</span>
                            <input type="number" class="form-control" name="discount" value="<?= arr_val($discount, "discount", 0) ?>">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="saveCompanyDiscount" value="<?= bc_code(); ?>">
                        <?php if ($discount_id) { ?>
                            <input type="hidden" name="id" value="<?= $discount_id; ?>">
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
                        <th>Title</th>
                        <th>Discount%</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$discount_data) {
                        echo '<tr><td colspan="4" class="text-center">No Roles Found</td></tr>';
                    } else {
                        $count = 1;
                        foreach ($discount_data as $discount) { ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?= $discount['title'] ?></td>
                                <td><?= $discount['discount'] ?>%</td>
                                <td>
                                    <div class="align-center child-el-margin-x">
                                        <a class="no-btn-styles text-success cp" href="discount?id=<?= $discount['id'] ?>"><i class="fas fa-edit"></i></a>
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