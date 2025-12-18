<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . 'vendor/autoload.php'; // Make sure DOMPDF is installed
use Dompdf\Dompdf;
use Dompdf\Options;


if (isset($_POST['saveInvoice'])) {
    $_POST['invoice_date'] = DateTime::createFromFormat('d-m-Y', $_POST['invoice_date'])->format('Y-m-d');
    $_POST['due_date'] = DateTime::createFromFormat('d-m-Y', $_POST['due_date'])->format('Y-m-d');

    $invoice_id = arr_val($_POST, "invoice_id", null);

    $invoice_no = trim($_POST['invoice_no']);
    $invoice_date = $_POST['invoice_date'];
    $status = $_POST['status'];
    $tax_rate = floatval($_POST['tax_rate']);
    $services_ids = $_POST['services_id'] ?? [];
    $service_amounts = $_POST['service_amount'] ?? [];
    $paid_amount = floatval($_POST['paid_amount']);
    $notes = trim($_POST['notes']);
    $proforma  = trim($_POST['proforma']); // this value is 1 or 0
    $invoiceTitle = $proforma ? "Proforma Invoice" : "Invoice";
    $due_date = $_POST['due_date'];
    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id = LOGGED_IN_USER['agency_id'];
    $customer_id = intval($_POST['customer_id']);
    $write_off = isset($_POST['write_off']) ? 1 : 0;
    $submit_type = arr_val($_POST, "submit_type", "");

    if (empty($customer_id)) {
        returnError("Please select a customer before saving the invoice.");
        exit;
    }

    // Filter out empty service rows & calculate line totals
    $valid_services = [];
    $_services_ids = [];
    $subtotal = 0;

    foreach ($services_ids as $key => $service_id) {
        $qty = intval($_POST['service_quantity'][$key] ?? 1);
        $amount = floatval($service_amounts[$key] ?? 0);

        if ($service_id && $amount > 0 && $qty > 0) {
            $line_total = $amount * $qty;
            $subtotal += $line_total;

            $valid_services[] = [
                'service_id' => intval($service_id),
                'amount' => $amount,
                'quantity' => $qty,
                'total' => $line_total
            ];

            $_services_ids[] = $service_id;
        }
    }

    // TAX
    $tax_rate = floatval($_POST['tax_rate'] ?? 0);
    $tax_amount = $subtotal * ($tax_rate / 100);

    // DISCOUNT
    $discount_percentage = floatval($_POST['discount_percentage'] ?? 0);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);

    // If discount % is set, calculate amount
    if ($discount_percentage > 0) {
        $discount_amount = (($subtotal * $discount_percentage) / 100) + $discount_amount;
    }

    // TOTAL
    $total_amount = $subtotal + $tax_amount - $discount_amount;

    // PAID / DUE
    $paid_amount = floatval($_POST['paid_amount'] ?? 0);
    $due_amount = $total_amount - $paid_amount;

    // echo "Subtotal: " . $subtotal . "<br>";
    // echo "Tax Rate: " . $tax_rate . "<br>";
    // echo "Tax Amount: " . $tax_amount . "<br>";
    // echo "Discount Percentage: " . $discount_percentage . "<br>";
    // echo "Discount Amount: " . $discount_amount . "<br>";
    // echo "Total Amount: " . $total_amount . "<br>";
    // echo "Paid Amount: " . $paid_amount . "<br>";
    // echo "Due Amount: " . $due_amount . "<br>";
    // die;


    if (empty($valid_services)) {
        returnError("Please select at least one valid service with an amount.");
        exit;
    }

    $services_final = [];

    foreach ($valid_services as $srv) {
        $service_id = intval($srv['service_id']);
        $amount = floatval($srv['amount']);
        $quantity = floatval($srv['quantity']);
        $total = floatval($srv['total']);

        // Get service name from database
        $service_data = $db->select("services", "text", ["id" => $service_id]);

        if (!empty($service_data)) {
            $services_final[] = [
                'description' => $service_data[0]['text'],
                'amount' => $amount,
                "quantity" => $quantity,
                "total" => $total,
            ];
        }
    }

    // PDF Part -------------------------------------
    // customer details
    $customer_data = $db->select_one("users", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "id" => $customer_id,
        "is_active" => 1,
        "type" => "customer"
    ]);
    if (empty($customer_data)) $customer_data = [];

    // Company Detail
    $company_data = $db->select_one("companies", "*", [
        "id" => LOGGED_IN_USER['company_id']
    ]);

    $invoice_data = [
        'company_name' => $company_data['company_name'],
        'company_address' => $company_data['company_address'],
        'company_phone' => $company_data['company_contact'],
        'company_email' => $company_data['company_email'],
        'invoice_no' => $invoice_no,
        'invoice_date' => $invoice_date,
        'due_date' => $due_date,
        'status' => $status,
        'client_name' => $customer_data['title'] . " " . $customer_data['fname'] . " " . $customer_data['lname'], // get from customer_id
        'client_contact' => $customer_data['contact'],
        'client_address' => $customer_data['address'],
        'client_city' => $customer_data["city"],
        'client_email' => $customer_data['email'],
        'tax_rate' => $tax_rate,
        'paid_amount' => $paid_amount,
        'services' => $services_final,
        'notes' => $notes,
        "proforma" => $proforma,
        "subtotal" => $subtotal,
        "tax_amount" => $tax_amount,
        "discount_percentage" => $discount_percentage,
        "discount_amount" => $discount_amount,
        "total_amount" => $total_amount,
        "due_amount" => $due_amount,
        "write_off" => $write_off
    ];

    // Update Invoice 
    if ($invoice_id) {
        $data = [
            "invoice_no" => $invoice_no,
            "invoice_date" => $invoice_date,
            "status" => $status,
            "tax_rate" => $tax_rate,
            "paid_amount" => $paid_amount,
            "notes" => $notes,
            "proforma" => $proforma,
            "due_date" => $due_date,
            "write_off" => $write_off,
            "discount" => $discount_amount,
            "subtotal" => $subtotal,
            "tax_amount" => $tax_amount,
            "discount_percentage" => $discount_percentage,
            "total_amount" => $total_amount,
            "due_amount" => $due_amount,
        ];

        $pdf = saveInvoicePDF($invoice_data);

        if ($pdf['success']) {
            $data['pdf_file'] =  $pdf['filename'];

            $invoiceType = $proforma ? "Proforma Invoice" : "Invoice";
            $_tc_email->send([
                'template' => 'invoice',
                // 'to' => $invoice_data['client_email'],
                'to' => "sohailakbar3324@gmail.com",
                'to_name' => $invoice_data['client_name'],
                'vars' => [
                    'invoice_type' => $invoiceType,
                    'invoice_no' => $invoice_no,
                    'invoice_date' => $invoice_date,
                    'client_name' => $invoice_data['client_name'],
                    'total_amount' => $total_amount,
                    'company_name' => $company_data['company_name']
                ],
                'attachments' => [_DIR_ . "/uploads/invoices/" . $pdf['filename']] // attach invoice PDF
            ]);
        }

        $invoiceUpdate =  $db->update("invoices", $data, [
            "id" => $invoice_id,
            "company_id" => $company_id,
            "agency_id" => $agency_id
        ]);

        // email history
        $db->insert("customer_email_history", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "customer_id" => $customer_id,
            "pdf_file" => $pdf['filename'],
            "invoice_id" => $invoice_id,
            "invoice_type" => $invoiceTitle,
        ]);

        if ($invoiceUpdate) {
            $db->delete("invoice_items", ["invoice_id" => $invoice_id]);
            foreach ($valid_services as $service_item) {
                $db->insert("invoice_items", [
                    "invoice_id" => $invoice_id,
                    "services_id" => intval($service_item['service_id']),
                    "quantity" => intval($service_item['quantity']),
                ]);
            }

            returnSuccess("Invoice Update Successfully", [
                "redirect" => "customer-profile?id=$customer_id",
            ]);
        }
        die;
    }


    // First time insert
    $mot_id = intval($_POST['mot_id'] ?? 0);
    if (empty($mot_id)) {
        returnError("Please select a Vehicle (Reg No.) before saving the invoice.");
        exit;
    }

    $data = [
        "company_id" => $company_id,
        "agency_id" => $agency_id,
        "customer_id" => $customer_id,
        "mot_id" => $mot_id,
        "invoice_no" => $invoice_no,
        "invoice_date" => $invoice_date,
        "due_date" => $due_date,
        "status" => $status,
        "subtotal" => $subtotal,
        "tax_rate" => $tax_rate,
        "tax_amount" => $tax_amount,
        "discount" => $discount_amount,
        "discount_percentage" => $discount_percentage,
        "total_amount" => $total_amount,
        "paid_amount" => $paid_amount,
        "due_amount" => $due_amount,
        "notes" => $notes,
        "proforma" => $proforma,
        "write_off" => $write_off,
    ];


    $pdf = saveInvoicePDF($invoice_data);
    if ($pdf['success']) {
        $data['pdf_file'] =  $pdf['filename'];

        $invoiceType = $proforma ? "Proforma Invoice" : "Invoice";
        $_tc_email->send([
            'template' => 'invoice',
            'to' => "sohailakbar3324@gmail.com",
            // 'to' => $invoice_data['client_email'],
            'to_name' => $invoice_data['client_name'],
            'vars' => [
                'invoice_type' => $invoiceType,
                'invoice_no' => $invoice_no,
                'invoice_date' => $invoice_date,
                'client_name' => $invoice_data['client_name'],
                'total_amount' => $total_amount,
                'company_name' => $company_data['company_name']
            ],
            'attachments' => [_DIR_ . "/uploads/invoices/" . $pdf['filename']] // attach invoice PDF
        ]);
    }


    $invoice = $db->insert("invoices", $data);

    // email history
    $db->insert("customer_email_history", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customer_id,
        "pdf_file" => $pdf['filename'],
        "invoice_id" => $invoice,
        "invoice_type" => $invoiceTitle,
    ]);

    // Save invoice items
    if ($invoice) {
        $db->delete("invoice_items", ["invoice_id" => $invoice]);
        foreach ($valid_services as $service_item) {
            $db->insert("invoice_items", [
                "invoice_id" => $invoice,
                "services_id" => intval($service_item['service_id']),
                "quantity" => intval($service_item['quantity']),
            ]);
        }

        returnSuccess("Invoice created successfully.", [
            "redirect" => $submit_type === "save_and_view_profile" ?  "customer-profile?id=$customer_id" :  "",
            "submit_type" => $submit_type,
            "pdf" => $pdf['filename']
        ]);
    } else {
        returnError("Failed to create invoice.");
    }
}



if (isset($_POST['fetchCustomerMotHistory'])) {
    $customer_id = $_POST['customer_id'];

    $mot_history = $db->select("customer_car_history", "*", [
        "customer_id" => $customer_id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "is_active" => 1
    ]);

    $customers = $db->select_one("users", "id,title,fname,lname,address,contact", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "is_active" => 1,
        "type" => "customer",
        "id" => $customer_id
    ], ["select_query" => true]);

    if (empty($mot_history)) {
        returnError("No Vehicle history found for the selected customer.");
        exit;
    }

    returnSuccess($mot_history, $customers);
}



function saveInvoicePDF($invoice_data = [])
{
    // Configure DomPDF options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);

    $is_proforma_div_hide = $invoice_data['proforma'] ? "d-none" : "";
    $invoiceTitle = $invoice_data["proforma"] ? "Quotation" : "INVOICE";



    // Branch Logo
    $user_img = "";
    $uploaded_img = _DIR_ . "uploads/" . LOGGED_IN_USER['image'];
    // If no custom image OR file doesn't exist → fallback to default
    if (
        LOGGED_IN_USER['image'] == "avatar.png" ||
        !file_exists($uploaded_img)
    ) {
        $user_img = "images/logo_img.png";
    } else {
        $user_img = "uploads/" . LOGGED_IN_USER['image'];
    }
    $branch_logo = SITE_URL . $user_img;
    $branch_logo = SITE_URL . "images/Hillcliffe-Garage-Logo.png";
    // $branch_logo = SITE_URL . "images/PR-Auto-Centre-Logo-dark.png";
    // Signature img
    $signature_img = SITE_URL . "images/signature.png";
    // Invoice Title
    $invoice_title =    'Invoice ' . ($invoice_data['invoice_no'] ?? 'INV-001') . '';
    $invoice_no = $invoice_data['invoice_no'];
    $invoice_date = DateTime::createFromFormat('Y-m-d', $invoice_data['invoice_date'])->format('d F Y');
    $company_name = $invoice_data['company_name'];
    $company_address = $invoice_data['company_address'];
    $company_phone = $invoice_data['company_phone'];
    $company_email = $invoice_data['company_email'];
    $due_date = DateTime::createFromFormat('Y-m-d', $invoice_data['due_date'])->format('d F Y');
    $tax_rate = $invoice_data['tax_rate'];
    $discount_percentage = $invoice_data['discount_percentage'];
    $client_name = $invoice_data['client_name'];
    $client_address = $invoice_data['client_address'];
    $client_contact = $invoice_data['client_contact'];
    $client_email = $invoice_data['client_email'];
    // Serviices Table
    $services = $invoice_data['services'];

    $services_table = '
<table style="margin:0px;">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="65%">Service Description</th>
            <th width="30%">Amount</th>
            <th width="30%">Quantity</th>
            <th width="30%">Total</th>
        </tr>
    </thead>
    <tbody>';
    foreach ($services as $index => $service) {

        $lineTotal = $service['amount'] * $service['quantity'];

        $services_table .= '
        <tr>
            <td>' . ($index + 1) . '</td>
            <td>' . htmlspecialchars($service['description']) . '</td>
            <td>' . _CURRENCY_SYMBOL . number_format($service['amount'], 2) . '</td>
            <td>' . $service['quantity'] . '</td>
            <td>' . _CURRENCY_SYMBOL . number_format($lineTotal, 2) . '</td>
        </tr>';
    }
    $services_table .= '
    </tbody>
</table>';

    $subtotal = _CURRENCY_SYMBOL . number_format($invoice_data['subtotal'], 2);
    $tax_rate = number_format($invoice_data['tax_rate'], 2);
    $tax_amount = _CURRENCY_SYMBOL . number_format($invoice_data['tax_amount'], 2);
    $discount_amount = _CURRENCY_SYMBOL . number_format($invoice_data['discount_amount'] ?? 0.00, 2);
    $total_amount = _CURRENCY_SYMBOL . number_format($invoice_data['total_amount'], 2);
    $paid_amount = _CURRENCY_SYMBOL . number_format($invoice_data['paid_amount'], 2);
    $due_amount = _CURRENCY_SYMBOL . number_format($invoice_data['due_amount'], 2);
    $client_note = $invoice_data["notes"];
    $l_user_contact = LOGGED_IN_USER['contact'];
    $l_user_email = LOGGED_IN_USER['email'];

    $is_client_note = empty($client_note) ? "d-none" : "";

    // HTML content for the invoice
    $html = <<<HTML
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{$invoice_title}</title>
    <style>
        body {
            size: A4;
        }

        @page {
            margin-top: 10mm;
            margin-right: 12mm;
            margin-bottom: 0mm;
            margin-left: 12mm;
        }


        .m-0 {
            margin: 0px
        }

        .invoice-pdf-container {
            width: 100%;
        }

        .content-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table th {
            background-color: #214F79;
            color: white;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 15px !important;
        }

        table td {
            padding: 12px;
            font-size: 14px;
        }

        table tr:nth-child(even) {
            background-color: #ECF9FF;
        }

        .footer {
            background-color: #214F79 !important;
            width: 100%;
        }

        .invoice-notes {
            border-left: 4px solid #214F79;
            padding: 10px 20px;
            background: #ECF9FF;
            margin-top: 20px;
            font-size: 14px;
        }

        .footer {
            margin-left: -12mm;
            margin-right: -12mm;
            width: calc(100% + 15mm);
            background-color: #214F79 !important;
            padding: 5px 20px;
            padding-bottom:0px !important;
            color: #fff;
            margin-top:30px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            width: 50%;
            padding: 5px 10px;
        }
        .d-none{
            display:none !important;
        }
    </style>
</head>

<body>
    <div class="invoice-pdf-container">
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="width:50%; padding-right:20px; vertical-align: top;">
                    <h1 style="color:#214F79;margin:0px;">{$invoiceTitle}</h1>
                    <h4 style="margin:0px;margin-top:10px;color:#6C6C6D;font-size:15px;">Invoice No.</h4>
                    <h4 style="margin:0px;margin-top:5px;color:#010101;font-weight:500;font-size:15px;">{$invoice_no}</h4>
                    <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:15px;">Issued on</h4>
                    <h4 style="margin:0px;margin-top:5px;color:#010101;font-weight:500;font-size:15px;">{$invoice_date}
                    </h4>
                </td>
                <td style="width:50%; padding-left:20px; vertical-align: top;text-align: right;">
                    <img src="{$branch_logo}" style="width:100%;">
                </td>
            </tr>
        </table>
        <div style="padding-left:20px;padding-right:20px;">
            <hr style="margin-top:10px;border:0.5px solid #ccc;">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:50%; padding-right:20px; vertical-align: top;">
                        <h4 style="margin:0px;margin-bottom:10px;margin-top:10px;color:#6C6C6D;font-size:15px;">Bill to
                        </h4>
                        <h4 style="margin:0px;margin-top:5px;color:#214F79;font-weight:500;font-size:16px;">{$client_name}</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">{$client_address}</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">Phone: {$client_contact}</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">{$client_email}</h4>
                    </td>
                    <td style="width:50%; padding-left:20px; vertical-align: top;">
                        <h4 style="margin:0px;margin-bottom:10px;margin-top:10px;color:#6C6C6D;font-size:15px;">Invoice
                            Details</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-weight:500;font-size:13px;">Invoice
                            Date: {$invoice_date}</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">Due Date: {$due_date}</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">Tax Rate: {$tax_rate}%</h4>
                        <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">Branch Discount: {$discount_percentage}%</h4>
                    </td>
                </tr>
            </table>
        </div>
        {$services_table}
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="width:50%; padding-right:20px; vertical-align: bottom;">
                    <img class="d-none" src="{$signature_img}" style="max-width:200px;">
                    <h1 class="d-none" style="color:#214F79;margin:0px;font-size:15px;">{$company_name}</h1>
                </td>
                <td style="width:50%; padding-left:20px; vertical-align: top;text-align: right;">
                    <table style="margin-top:20px;">
                        <tbody>
                            <tr>
                                <td>SubTotal:</td>
                                <td style="text-align: right;">{$subtotal}</td>
                            </tr>
                            <tr>
                                <td style="font-weight:bold !important;">Vat ({$tax_rate}%):</td>
                                <td style="text-align: right;">{$tax_amount}</td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td style="text-align: right;">{$discount_amount}</td>
                            </tr>
                             <tr style="background:#F2F2F7;">
                                <td style="font-weight:bold;">Total Amount:</td>
                                <td style="text-align: right; font-weight:bold;">{$total_amount}</td>
                            </tr>
                            <tr class="{$is_proforma_div_hide}">
                                <td>Paid:</td>
                                <td style="text-align: right;">{$paid_amount}</td>
                            </tr>
                            <tr class="{$is_proforma_div_hide}" style="background:#214F79; ">
                                <td style="color:white;">Due Amount:</td>
                                <td style="text-align: right;color:white;">{$due_amount}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <div class="invoice-notes {$is_client_note}">
            {$client_note}
        </div>
        <h3 style="color:#214F79; margin-top:20px;">Thank you for your Business.</h3>
    </div>
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width:50%; padding-right:20px; vertical-align: top;">
                    <strong>Terms And Conditions:</strong><br>
                    <p style="margin-top:5px;">Payment is due within 07 days from the invoice date. <br>
                    Late payment may incur a 2% monthly fee.</p>
                </td>

                <td style="width:50%; padding-left:20px; vertical-align: top;">
                    <table class="footer-table m-0">
                        <tr>
                            <td style="width:50%; vertical-align: top;">
                                <strong>Contact Us:</strong><br>
                                <p style="margin-top:5px;">{$company_phone}<br>
                                {$l_user_contact}<br></p>
                            </td>

                            <td style="width:50%; vertical-align: top;">
                                <strong>Find Us:</strong><br>
                                <p style="margin-top:5px;">{$company_email}<br>
                                {$l_user_email}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
HTML;
    try {
        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Create invoices directory if it doesn't exist
        $invoice_dir = _DIR_ . '/uploads/invoices/';
        if (!is_dir($invoice_dir)) {
            mkdir($invoice_dir, 0755, true);
        }

        // Generate filename
        $filename = 'invoice_' . ($invoice_data['invoice_no'] ?? 'INV-2024-001') . '.pdf';
        // $filename = "invoice_test.pdf";
        $filepath = $invoice_dir . $filename;

        // Save PDF to file
        file_put_contents($filepath, $dompdf->output());

        return [
            'success' => true,
            'filepath' => $filepath,
            'filename' => $filename
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


// Update invoice payment
if (isset($_POST['updateInvoicePayment'])) {
    $invoice_id     = $_POST['invoice_id'];
    $customer_id    = $_POST['customer_id'];
    $total_amount   = floatval($_POST['total_amount']);
    $paid_old       = floatval($_POST['paid_amount_old']);
    $new_payment    = floatval($_POST['new_payment']);
    $status         = $_POST['status'];

    // Calculate remaining due before applying new payment
    $due_before = $total_amount - $paid_old;

    // 🧠 Validation: Prevent overpayment
    if ($new_payment > $due_before) {
        returnError("Payment exceeds the remaining due amount! You can only pay up to " . number_format($due_before, 2) . ".");
        exit;
    }

    // Calculate new totals
    $paid_total = $paid_old + $new_payment;
    $due_amount = $total_amount - $paid_total;

    // Ensure due amount doesn’t go negative
    if ($due_amount < 0) $due_amount = 0;

    // Auto-adjust status if user didn't set manually
    if (empty($status)) {
        if ($paid_total >= $total_amount) {
            $status = 'paid';
        } elseif ($paid_total > 0 && $paid_total < $total_amount) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }
    }

    // Update invoice
    $updated = $db->update('invoices', [
        'paid_amount' => $paid_total,
        'due_amount'  => $due_amount,
        'status'      => $status,
    ], [
        'id' => $invoice_id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customer_id
    ]);

    // Select invoice
    $invoice = $db->select_one("invoices", "*", [
        'id' => $invoice_id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customer_id
    ]);

    // Company Detail
    $company_data = $db->select_one("companies", "*", [
        "id" => LOGGED_IN_USER['company_id']
    ]);

    // Customer details
    $customer_data = $db->select_one("users", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "id" => $customer_id,
        "is_active" => 1,
        "type" => "customer"
    ]);

    // Fetch all invoice item rows
    $invoice_items = $db->select("invoice_items", "*", [
        "invoice_id" => $invoice_id
    ]);

    $services_final = [];

    if (!empty($invoice_items)) {

        foreach ($invoice_items as $invItem) {

            if (empty($invItem['services_id'])) {
                continue;
            }

            // Current row ki services
            $service_ids = explode(",", $invItem['services_id']);
            $service_ids = array_map('intval', $service_ids);

            // Current row ki quantity
            $quantity = intval($invItem['quantity']);

            if (!empty($service_ids)) {

                $ids = implode(',', $service_ids);

                $services = $db->query("SELECT id, text, amount FROM services WHERE id IN ($ids)", [
                    "select_query" => true
                ]);

                foreach ($services as $srv) {
                    $total = $srv['amount'] * $quantity;

                    $services_final[] = [
                        'description' => $srv['text'],
                        'amount' => $srv['amount'],
                        'quantity' => $quantity,
                        'total' => $total
                    ];
                }
            }
        }
    }


    $invoice_no = generateInvoiceNo($db);

    $invoice_data = [
        'company_name' => $company_data['company_name'],
        'company_address' => $company_data['company_address'],
        'company_phone' => $company_data['company_contact'],
        'company_email' => $company_data['company_email'],
        'invoice_no' => $invoice_no,
        'invoice_date' => $invoice['invoice_date'],
        'due_date' => $invoice['due_date'],
        'status' => $invoice['status'],
        'client_name' => $customer_data['title'] . " " . $customer_data['fname'] . " " . $customer_data['lname'], // get from customer_id
        'client_contact' => $customer_data['contact'],
        'client_address' => $customer_data['address'],
        'client_city' => $customer_data["city"],
        'client_email' => $customer_data['email'],
        'tax_rate' => $invoice['tax_rate'],
        'paid_amount' => $invoice['paid_amount'],
        'services' => $services_final,
        'notes' => $invoice['notes'],
        "proforma" => $invoice['proforma'],
        "subtotal" => $invoice['subtotal'],
        "tax_amount" => $invoice['tax_amount'],
        "discount_percentage" => $invoice['discount_percentage'],
        "discount_amount" => $invoice['discount'],
        "total_amount" => $invoice['total_amount'],
        "due_amount" => $invoice['due_amount'],
        "write_off" => $invoice['write_off']
    ];

    $pdf = saveInvoicePDF($invoice_data);

    if ($pdf['success']) {
        $db->update("invoices", [
            "pdf_file" => $pdf['filename']
        ], [
            'id' => $invoice_id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "customer_id" => $customer_id
        ]);
    }

    if ($updated) {
        returnSuccess("Invoice payment updated successfully.", [
            "redirect" => ""
        ]);
    } else {
        returnError("Failed to update invoice payment. Please try again.");
    }
} else {
    returnError("Invalid request.");
}
