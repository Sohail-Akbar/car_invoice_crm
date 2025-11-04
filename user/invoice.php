<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "invoice.js",
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
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
                                value="10">
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

                    <button type="button" id="add_row" class="btn btn-secondary btn-sm mb-3">+ Add Row</button>

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
                                    <td><input type="number" name="paid_amount" id="paid_amount" class="form-control"
                                            value="0"></td>
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