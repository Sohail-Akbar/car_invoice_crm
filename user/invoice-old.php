<?php
require_once('includes/db.php');
$page_name = 'Invoice';

$JS_FILES_ = [
    "invoice.js",
    _DIR_ . "js/select2.min.js"
];
$CSS_FILES_ = [
    _DIR_ . "css/select2.min.css",
    "invoice.css"
];

$get_invoice_id = _get_param("id", null);
$get_customer_id = _get_param("customer_id", null);

// Fetch all active services
$services = $db->select("services", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1
]);


$invoice_no = generateInvoiceNo($db);

$customers = $db->select("users", "id,title,fname,lname", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1,
    "type" => "customer"
], ["select_query" => true]);

$agency = $db->select_one("agencies", "*", [
    "id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id']
]);

// invoice Service
$invoiceItems = [];
$invoiceData = [];
if ($get_invoice_id) {
    // Services Item
    $invoiceItems = $db->query("SELECT s.id, s.text, s.amount, i.quantity
    FROM invoice_items i
    INNER JOIN services s ON i.services_id = s.id
    WHERE i.invoice_id = '$get_invoice_id'", ["select_query" => true]);
    if (!$invoiceItems) $invoiceItems = [];

    // $invoiceData
    $invoiceData = $db->select_one("invoices", "id,notes,discount,write_off", [
        "id" => $get_invoice_id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
    ]);
    if (!$invoiceData) $invoiceData = [];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content invoice-container" id="mainContent">
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
                        <?php if (!$get_invoice_id) { ?>
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
                                <label>Vehicle (Reg No.)</label>
                                <select name="mot_id" class="form-control" id="motHistorySelectBox">
                                </select>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="row mx-0">
                                <div class="col-md-4 px-0">
                                    <label>VAT%</label>
                                    <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control"
                                        value="<?= arr_val($agency, "vat_percentage", 0) ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Discount%</label>
                                    <input type="number" step="0.01" name="discount_percentage" id="discountPercentage" class="form-control"
                                        value="<?= arr_val($agency, "discount_percentage", 0)  ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Discount Amount</label>
                                    <input type="number" step="0.01"
                                        name="discount_amount"
                                        id="discountAmount"
                                        class="form-control" value="<?= arr_val($invoiceData, "discount", 0); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-2 text-primary">Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoice_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Service</th>
                                    <th width="80">Quantity</th>
                                    <th width="100">Amount</th>
                                    <th width="50">#</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
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
                                    <th>Discount:</th>
                                    <td><span id="discount_show">0</span></td>
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
                                    <td>
                                        <div class="pull-away">
                                            <span id="due_amount">0</span>
                                            <input type="checkbox" class="tc-checkbox" <?= arr_val($invoiceData, "write_off", 0) == 1 ? "checked" : "" ?> data-label="Write off" name="write_off">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes"><?= arr_val($invoiceData, "notes", ""); ?></textarea>
                    </div>
                    <label>
                        <input type="radio" name="proforma" value="0" checked>
                        Invoice
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="proforma" value="1" required>
                        Proforma
                    </label>

                    <div class="text-right">
                        <?php if ($get_invoice_id) { ?>
                            <input type="hidden" name="invoice_id" value="<?= $get_invoice_id ?>">
                            <input type="hidden" name="customer_id" value="<?= $get_customer_id ?>">
                        <?php } ?>
                        <input type="hidden" name="saveInvoice" value="<?= bc_code(); ?>">
                        <button type="submit" class="btn btn-success">Save Invoice</button>
                    </div>

                </div>
            </div>
        </form>
    </main>

    <script>
        let SERVICES = <?= json_encode($services); ?>;
        let INVOICE_ITEMS = <?= json_encode($invoiceItems) ?>;
        let _GET = <?= json_encode($_GET) ?>;
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>