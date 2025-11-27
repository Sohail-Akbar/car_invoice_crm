<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

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

$customers = $db->select("users", "id,title,fname,lname,address,contact", [
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
    $invoiceData = $db->select_one("invoices", "id,notes,discount,write_off,subtotal,discount_percentage", [
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
    <style>
        .form-group {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content invoice-container" id="mainContent">
        <h4 class="invoice-text">Create New Invoice</h4>
        <div class="card px-0">
            <form action="invoice" method="POST" class="ajax_form">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="row mx-0">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control py-2" readonly
                                        value="<?= $invoice_no ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Invoice Date</label>
                                    <input type="date" name="invoice_date" class="form-control py-2" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Due Date</label>
                                    <input type="date" name="due_date" class="form-control py-2"
                                        value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                </div>
                            </div>
                            <?php if (!$get_invoice_id) { ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label">Customer</label>
                                        <select name="customer_id" class="form-control select2-list py-2" id="customerSelectBox">
                                            <option value="">-- Select Customer --</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer['id'] ?>">
                                                    <?= htmlspecialchars($customer['title'] . ' ' . $customer['fname'] . ' ' . $customer['lname']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 d-none" id="motHistoryDiv">
                                    <div class="form-group">
                                        <label class="label">Vehicle (Reg No.)</label>
                                        <select name="mot_id" class="form-control py-2" id="motHistorySelectBox">
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Status</label>
                                    <select name="status" class="form-control py-2">
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                        <option value="partial">Partial</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">VAT %</label>
                                    <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control py-2"
                                        value="<?= arr_val($agency, "vat_percentage", 0) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Discount %</label>
                                    <input type="number" step="0.01" name="discount_percentage" id="discountPercentage" class="form-control py-2"
                                        value="<?= arr_val($agency, "discount_percentage", 0)  ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label">Discount Amount</label>
                                    <?php
                                    $discount_amount_val = 0;
                                    if (arr_val($invoiceData, "discount", 0)) {
                                        $discount_amount_val = $invoiceData['discount'] - (($invoiceData['subtotal'] * $invoiceData['discount_percentage']) / 100);
                                    }
                                    ?>
                                    <input type="number" step="0.01"
                                        name="discount_amount"
                                        id="discountAmount"
                                        class="form-control py-2" value="<?= $discount_amount_val; ?>">
                                </div>
                            </div>
                            <!-- Invoice Items -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="invoice-items-container">
                                        <div class="invoice-heading">
                                            <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 0.0996094H10C10.5039 0.0996094 10.9874 0.299931 11.3438 0.65625C11.7001 1.01257 11.9004 1.49609 11.9004 2V5.25C11.9004 5.42239 11.8319 5.58806 11.71 5.70996C11.5881 5.83186 11.4224 5.90039 11.25 5.90039C11.0776 5.90039 10.9119 5.83186 10.79 5.70996C10.6681 5.58806 10.5996 5.42239 10.5996 5.25V2C10.5996 1.84087 10.5363 1.68869 10.4238 1.57617C10.3113 1.46365 10.1591 1.40039 10 1.40039H5C4.84087 1.40039 4.68869 1.46365 4.57617 1.57617C4.46365 1.68869 4.40039 1.84087 4.40039 2V10C4.40039 10.1591 4.46365 10.3113 4.57617 10.4238C4.68869 10.5363 4.84087 10.5996 5 10.5996H6.25C6.42239 10.5996 6.58806 10.6681 6.70996 10.79C6.83186 10.9119 6.90039 11.0776 6.90039 11.25C6.90039 11.4224 6.83186 11.5881 6.70996 11.71C6.58806 11.8319 6.42239 11.9004 6.25 11.9004H5C4.49609 11.9004 4.01257 11.7001 3.65625 11.3438C3.29993 10.9874 3.09961 10.5039 3.09961 10V5.40039H2C1.84087 5.40039 1.68869 5.46365 1.57617 5.57617C1.46365 5.68869 1.40039 5.84087 1.40039 6V14C1.40039 14.1591 1.46365 14.3113 1.57617 14.4238C1.68869 14.5363 1.84087 14.5996 2 14.5996H7C7.15913 14.5996 7.31131 14.5364 7.42383 14.4238C7.53635 14.3113 7.59961 14.1591 7.59961 14V13.75C7.59961 13.5776 7.66814 13.4119 7.79004 13.29C7.91194 13.1681 8.07761 13.0996 8.25 13.0996C8.42239 13.0996 8.58806 13.1681 8.70996 13.29C8.83186 13.4119 8.90039 13.5776 8.90039 13.75V14C8.90039 14.5039 8.70007 14.9874 8.34375 15.3438C7.98743 15.7001 7.50391 15.9004 7 15.9004H2C1.49609 15.9004 1.01257 15.7001 0.65625 15.3438C0.299931 14.9874 0.0996094 14.5039 0.0996094 14V6C0.0996094 5.49609 0.299931 5.01257 0.65625 4.65625C1.01257 4.29993 1.49609 4.09961 2 4.09961H3.09961V2C3.09961 1.49609 3.29993 1.01257 3.65625 0.65625C4.01257 0.299931 4.49609 0.0996094 5 0.0996094ZM12 8.09961C12.1724 8.09961 12.3381 8.16814 12.46 8.29004C12.5819 8.41194 12.6504 8.57761 12.6504 8.75V10.3496H14.25C14.4224 10.3496 14.5881 10.4181 14.71 10.54C14.8319 10.6619 14.9004 10.8276 14.9004 11C14.9004 11.1724 14.8319 11.3381 14.71 11.46C14.5881 11.5819 14.4224 11.6504 14.25 11.6504H12.6504V13.25C12.6504 13.4224 12.5819 13.5881 12.46 13.71C12.3381 13.8319 12.1724 13.9004 12 13.9004C11.8276 13.9004 11.6619 13.8319 11.54 13.71C11.4181 13.5881 11.3496 13.4224 11.3496 13.25V11.6504H9.75C9.57761 11.6504 9.41194 11.5819 9.29004 11.46C9.16814 11.3381 9.09961 11.1724 9.09961 11C9.09961 10.8276 9.16814 10.6619 9.29004 10.54C9.41194 10.4181 9.57761 10.3496 9.75 10.3496H11.3496V8.75C11.3496 8.57761 11.4181 8.41194 11.54 8.29004C11.6619 8.16814 11.8276 8.09961 12 8.09961ZM6.25 3.09961H8.75C8.92239 3.09961 9.08806 3.16814 9.20996 3.29004C9.33186 3.41194 9.40039 3.57761 9.40039 3.75C9.40039 3.92239 9.33186 4.08806 9.20996 4.20996C9.08806 4.33186 8.92239 4.40039 8.75 4.40039H6.25C6.07761 4.40039 5.91194 4.33186 5.79004 4.20996C5.66814 4.08806 5.59961 3.92239 5.59961 3.75C5.59961 3.57761 5.66814 3.41194 5.79004 3.29004C5.91194 3.16814 6.07761 3.09961 6.25 3.09961Z" fill="url(#paint0_linear_194_448)" stroke="black" stroke-width="0.2" />
                                                <defs>
                                                    <linearGradient id="paint0_linear_194_448" x1="7.5" y1="0" x2="7.5" y2="16" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#214F79" />
                                                        <stop offset="1" stop-color="#3D92DF" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                            <h3>Invoice Items</h3>
                                        </div>
                                        <div class="services-header">
                                            <div class="row mx-0">
                                                <div class="col-md-6 px-0 col-sm-12">
                                                    <h5>Service...</h5>
                                                </div>
                                                <div class="col-md-6 px-0 col-sm-12">
                                                    <div class="d-flex">
                                                        <span class="badge badge-white bg-white py-2 px-3 mr-2" style="font-size: 12px;">Quantity</span>
                                                        <span class="badge badge-white bg-white py-2 px-3 mr-2" style="font-size: 14px;">Amount</span>
                                                        <span class="badge badge-white bg-white py-2 px-3" style="font-size: 14px;">#</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="add_row" class="btn btn-sm mb-3 mt-3">+ Add Item</button>
                                        <div class="invoice-services-container">
                                            <!-- <div class="row mx-0">
                                                <div class="col-md-6 px-0 mb-2">
                                                    <select name="" id="" class="form-control invoice-select-box">
                                                        <option value="1">select 1</option>
                                                        <option value="1">select 1</option>
                                                        <option value="1">select 1</option>
                                                        <option value="1">select 1</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 px-0">
                                                    <div class="d-flex">
                                                        <input type="number" class="form-control service_quantity invoice-input-item" step="1" min="1" name="service_quantity[]" value="1">
                                                        <input type="number" class="form-control service_amount invoice-input-item" step="any" name="service_amount[]" value="0">
                                                        <button type="button" class="btn btn-sm remove-row">
                                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.0638 0.506144L7.00014 4.56991C5.64566 3.2159 4.29075 1.86059 2.93606 0.506144C1.36892 -1.06096 -1.06026 1.36904 0.505569 2.93658C1.86048 4.29015 3.21583 5.64568 4.56921 7.00013C3.21521 8.35519 1.86066 9.70971 0.505569 11.0637C-1.06026 12.6303 1.36914 15.0597 2.93606 13.4941C4.29075 12.139 5.64537 10.7844 6.99992 9.43013L11.0636 13.4941C12.6307 15.0608 15.0605 12.6306 13.494 11.0637C12.1394 9.70887 10.7844 8.35421 9.42932 6.99969C10.7842 5.64474 12.1391 4.28993 13.494 2.93527C15.0608 1.36904 12.6309 -1.06096 11.0636 0.507018" fill="#EC1C24" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="invoice-right-container">
                            <h4 class="invoice-text mb-0">Invoice</h4>
                            <hr class="mt-2">
                            <div class="invoice-right-head">
                                <div class="left-content">
                                    <label class="mb-0">Billed To:</label>
                                    <p class="customer-name"></p>
                                    <p class="customer-address"></p>
                                    <p class="customer-phone">Phone: <span class="contact"></span></p>
                                </div>
                                <div class="right-content">
                                    <label class="mb-0">Invoice No.</label>
                                    <p class="invoice-no">#<?= $invoice_no ?></p>
                                    <p class="issue-on">Issue On</p>
                                    <p class="issue-date"><?= date("M d, Y"); ?></p>
                                </div>
                            </div>
                            <h5 class="invoice-text mb-0 mt-4" style="font-size: 18px;">Services</h5>
                            <div class="services-amount-details">
                                <div class="single-item">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">0.00</span>
                                </div>
                                <hr class="my-0">
                                <div class="single-item">
                                    <span>Tax:</span>
                                    <span id="tax_amount">0.00</span>
                                </div>
                                <hr class="my-0">
                                <div class="single-item">
                                    <span>Discount:</span>
                                    <span id="discount_show">0.00</span>
                                </div>
                                <hr class="my-0">
                                <div class="single-item">
                                    <span>Total:</span>
                                    <span id="total_amount">0.00</span>
                                </div>
                                <hr class="my-0">
                                <div class="single-item">
                                    <span>Paid:</span>
                                    <span><input type="number" name="paid_amount" id="paid_amount" class="form-control" step="any" value="0"></span>
                                </div>
                                <hr class="my-0">
                                <div class="due-amount-head">
                                    <div class="single-item">
                                        <span>Due:</span>
                                        <span id="due_amount">0.00</span>
                                    </div>
                                    <div class="mt-3">
                                        <input type="checkbox" class="tc-checkbox" <?= arr_val($invoiceData, "write_off", 0) == 1 ? "checked" : "" ?> data-label="Write off" name="write_off">
                                    </div>
                                </div>
                                <hr class="my-0 w-50">
                            </div>
                        </div>
                        <div class="notes-container mt-2">
                            <div class="label">Notes:</div>
                            <textarea name="notes" class="form-control notes-input" rows="3" placeholder="Optional notes"><?= arr_val($invoiceData, "notes", ""); ?></textarea>
                        </div>
                        <div class="d-flex mt-2">
                            <label class="cp">
                                <input type="radio" name="proforma" value="0" checked>
                                Invoice
                            </label>
                            <br>
                            <label class="ml-4 cp">
                                <input type="radio" name="proforma" value="1" required>
                                Proforma
                            </label>
                        </div>
                        <div class="text-right">
                            <?php if ($get_invoice_id) { ?>
                                <input type="hidden" name="invoice_id" value="<?= $get_invoice_id ?>">
                                <input type="hidden" name="customer_id" value="<?= $get_customer_id ?>">
                            <?php } ?>
                            <input type="hidden" name="saveInvoice" value="<?= bc_code(); ?>">
                            <button type="submit" class="btn">Save Invoice</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        let SERVICES = <?= json_encode($services); ?>;
        let INVOICE_ITEMS = <?= json_encode($invoiceItems) ?>;
        let _GET = <?= json_encode($_GET) ?>;
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>