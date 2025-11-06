<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "invoice.js",
    _DIR_ . "js/select2.min.js"
];
$CSS_FILES_ = [
    _DIR_ . "css/select2.min.css"
];

// Fetch all active services
$services = $db->select("services", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1
]);


$invoice_no = generateInvoiceNo($db);

$customers = $db->select("customers", "id,title,fname,lname", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1
], ["select_query" => true]);

$agency = $db->select_one("agencies", "*", [
    "id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id']
]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        /* Make Select2 look like Bootstrap .form-control */
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            background-color: #fff;
            box-shadow: none;
            line-height: 1.2;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529;
            margin-top: -1px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
            right: .5rem;
            top: 0;
            width: 2.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #212529 transparent transparent transparent;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--single.select2-selection--focus {
            outline: 0;
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        .select2-container--default .select2-selection--multiple {
            min-height: calc(2.25rem + 2px);
            padding: .25rem .5rem;
            border: 1px solid #ced4da;
            border-radius: .375rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #0d6efd;
            color: #fff;
            border: none;
            padding: .25rem .5rem;
            margin-top: .2rem;
            margin-right: .25rem;
            border-radius: .375rem;
            font-size: .85em;
        }

        .select2-container .select2-dropdown {
            border-radius: .375rem;
            border: 1px solid rgba(0, 0, 0, .15);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: .25rem;
            padding: .375rem .75rem;
            height: auto;
        }

        .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: #e9ecef;
            opacity: 1;
        }

        .select2-container--default.select2-container--small .select2-selection--single {
            height: calc(1.5rem + 2px);
            padding: .125rem .5rem;
            border-radius: .25rem;
        }

        .select2-container--default.select2-container--large .select2-selection--single {
            height: calc(2.75rem + 2px);
            padding: .5rem 1rem;
            border-radius: .5rem;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <form action="invoice" method="POST" class="ajax_form">
            <div class="card shadow">
                <div class="card-header  text-white">
                    <h4 class="mb-0">Create New Invoice</h4>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control" readonly
                                value="<?= $invoice_no ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Due Date</label>
                            <input type="date" name="due_date" class="form-control"
                                value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Customer</label>
                            <select name="customer_id" class="form-control" id="customerSelectBox">
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>">
                                        <?= htmlspecialchars($customer['title'] . ' ' . $customer['fname'] . ' ' . $customer['lname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-none" id="motHistoryDiv">
                            <label>Mot History (Reg No.)</label>
                            <select name="mot_id" class="form-control" id="motHistorySelectBox">
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label>VAT%</label>
                            <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control"
                                value="<?= $agency['vat_percentage'] ?>" readonly>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-2 text-primary">Invoice Items</h5>
                    <table class="table table-bordered" id="invoice_table">
                        <thead class="thead-light">
                            <tr>
                                <th>Service</th>
                                <th width="100">Amount</th>
                                <th width="50">#</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <button type="button" id="add_row" class="btn btn-secondary btn-sm mb-3">+ Add Item</button>

                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <table class="table">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td><span id="subtotal">0</span></td>
                                </tr>
                                <tr>
                                    <th>Tax:</th>
                                    <td><span id="tax_amount">0</span></td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td><span id="total_amount">0</span></td>
                                </tr>
                                <tr>
                                    <th>Paid:</th>
                                    <td><input type="number" name="paid_amount" id="paid_amount" class="form-control" step="any" value="0"></td>
                                </tr>
                                <tr>
                                    <th>Due:</th>
                                    <td><span id="due_amount">0</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes"></textarea>
                    </div>

                    <div class="text-right">
                        <input type="hidden" name="saveInvoice" value="<?= bc_code(); ?>">
                        <button type="submit" class="btn btn-success">Save Invoice</button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        const SERVICES = <?= json_encode($services); ?>;
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>