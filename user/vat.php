<?php
require_once('includes/db.php');
$page_name = 'VAT Management';

$JS_FILES_ = [];

$agency = $db->select_one("agencies", "*", [
    "id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id']
]);

// Handle form submissions
if (isset($_POST['saveVat'])) {
    $vat = $_POST['vat'];
    // Update if exists
    $db->update("agencies", [
        "vat_percentage" => $vat
    ], [
        "id" => LOGGED_IN_USER['agency_id'],
        "company_id" => LOGGED_IN_USER['company_id']
    ]);
    echo '<script>
    alert("Update VAT% Successfully");
    location.href = "vat";
</script>';
}
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
                Manage VAT Settings
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <span class="label">VAT%</span>
                                <input type="number" step="0.01" class="form-control"
                                    value="<?= arr_val($agency, "vat_percentage", "") ?>"
                                    name="vat" required data-length="[1,250]">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="saveVat" value="true">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once('./includes/js.php'); ?>
</body>

</html>