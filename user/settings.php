<?php
require_once('includes/db.php');
$page_name = 'Settings';

$JS_FILES_ = [];

$agency = $db->select_one("agencies", "*", [
    "id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id']
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content setting-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-4 custom-heading text-clr">VAT%</h3>
            <form action="settings" method="POST" class="ajax_form">
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
        <div class="card mt-4 d-none">
            <div class="card-header">
                Branch Discount
            </div>
            <div class="card-body">
                <form action="settings" method="POST" class="ajax_form">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <span class="label">Discount%</span>
                                <input type="number" step="0.01" class="form-control"
                                    value="<?= arr_val($agency, "discount_percentage", "") ?>"
                                    name="discount" required data-length="[1,250]">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="saveDiscount" value="true">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-4 d-none">
            <div class="card-header">
                SMS API Settings
            </div>
            <div class="card-body">
                <form action="settings" method="POST" class="ajax_form">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <span class="label">API Key</span>
                                <input type="text" class="form-control"
                                    value="<?= get_api_key(); ?>"
                                    name="sms_api_key" required data-length="[1,250]">
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <input type="hidden" name="saveSmsApiKey" value="true">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once('./includes/js.php'); ?>
</body>

</html>