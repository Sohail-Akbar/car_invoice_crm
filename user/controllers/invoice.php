<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . 'vendor/autoload.php'; // Make sure DOMPDF is installed
use Dompdf\Dompdf;
use Dompdf\Options;


if (isset($_POST['saveInvoice'])) {
    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id = LOGGED_IN_USER['agency_id'];
    $customer_id = intval($_POST['customer_id']);
    $mot_id = intval($_POST['mot_id'] ?? 0);
    $invoice_no = trim($_POST['invoice_no']);
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $tax_rate = floatval($_POST['tax_rate']);
    $paid_amount = floatval($_POST['paid_amount']);
    $notes = trim($_POST['notes']);

    $services_ids = $_POST['services_id'] ?? [];
    $service_amounts = $_POST['service_amount'] ?? [];

    // Filter out empty service rows
    $valid_services = [];
    $_services_ids = [];
    foreach ($services_ids as $key => $service_id) {
        if ($service_id && isset($service_amounts[$key]) && floatval($service_amounts[$key]) > 0) {
            $valid_services[] = [
                'service_id' => intval($service_id),
                'amount' => floatval($service_amounts[$key])
            ];
            array_push($_services_ids, $service_id);
        }
    }
    if (empty($valid_services)) {
        returnError("Please select at least one valid service with an amount.");
        exit;
    }

    // Calculate totals
    $subtotal = array_sum(array_column($valid_services, 'amount'));
    $tax_amount = $subtotal * ($tax_rate / 100);
    $total_amount = $subtotal + $tax_amount;
    $due_amount = $total_amount - $paid_amount;

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
        "discount" => 0,
        "total_amount" => $total_amount,
        "paid_amount" => $paid_amount,
        "due_amount" => $due_amount,
        "notes" => $notes
    ];
    // PDF Part -------------------------------------
    // customer details
    $customer_data = $db->select_one("customers", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "id" => $customer_id
    ]);
    if (empty($customer)) $customer = [];

    // Company Detail
    $company_data = $db->select_one("companies", "*", [
        "id" => LOGGED_IN_USER['company_id']
    ]);

    $services_final = [];

    foreach ($valid_services as $srv) {
        $service_id = intval($srv['service_id']);
        $amount = floatval($srv['amount']);

        // Get service name from database
        $service_data = $db->select("services", "text", ["id" => $service_id]);

        if (!empty($service_data)) {
            $services_final[] = [
                'description' => $service_data[0]['text'],
                'amount' => $amount
            ];
        }
    }

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
        'notes' => $notes
    ];

    $pdf = saveInvoicePDF($invoice_data);
    if ($pdf['success']) {
        $data['pdf_file'] =  $pdf['filename'];
    }

    // PDF Part -------------------------------------


    $invoice = $db->insert("invoices", $data);


    // Save invoice items
    if ($invoice) {
        $db->insert("invoice_items", [
            "invoice_id" => $invoice,
            "services_id" => implode(",", $_services_ids),
        ]);

        returnSuccess("Invoice created successfully.", ["redirect" => ""]);
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

    if (empty($mot_history)) {
        returnError("No MOT history found for the selected customer.");
        exit;
    }

    returnSuccess($mot_history);
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

    // HTML content for the invoice
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Invoice ' . ($invoice_data['invoice_no'] ?? 'INV-001') . '</title>
        <style>
            body { 
                font-family: "Helvetica", Arial, sans-serif; 
                font-size: 12px; 
                color: #333;
                margin: 0;
                background-color: #f5f5f5;
            }
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .header {
                display: flex;
                justify-content: space-between;
                border-bottom: 2px solid #333;
            }
            .company-info h2 {
                color: #2c3e50;
                margin: 0 0 10px 0;
                font-size: 24px;
            }
            .company-info p {
                margin: 2px 0;
                color: #666;
            }
            .invoice-info {
                text-align: right;
            }
            .invoice-info h1 {
                color: #e74c3c;
                margin: 0 0 10px 0;
                font-size: 28px;
            }
            .invoice-info p {
                margin: 3px 0;
            }
            .billing-info {
                display: flex;
                justify-content: space-between;
            }
            .bill-to, .invoice-details {
                width: 100%;
            }
            .section-title {
                background-color: #f8f9fa;
                padding: 8px 12px;
                font-weight: bold;
                border-left: 4px solid #3498db;
                margin-bottom: 10px;
                color: #2c3e50;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 10px 0;
            }
            table th {
                background-color: #34495e;
                color: white;
                padding: 12px;
                text-align: left;
                border: 1px solid #ddd;
                font-weight: bold;
            }
            table td {
                padding: 12px;
                border: 1px solid #ddd;
            }
            table tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            .totals {
                width: 300px;
                margin-left: auto;
                margin-top: 20px;
            }
            .totals table {
                width: 100%;
                margin: 0;
            }
            .totals td {
                padding: 8px;
                border: none;
            }
            .totals tr:last-child {
                font-weight: bold;
                font-size: 14px;
                background-color: #ecf0f1;
                border-top: 2px solid #bdc3c7;
            }
            .amount {
                text-align: right;
            }
            .status {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 10px;
                margin-top: 5px;
            }
            .status-paid { background-color: #d4edda; color: #155724; }
            .status-pending { background-color: #fff3cd; color: #856404; }
            .status-overdue { background-color: #f8d7da; color: #721c24; }
            .footer {
                margin-top: 50px;
                text-align: center;
                color: #7f8c8d;
                font-size: 10px;
                border-top: 1px solid #ddd;
                padding-top: 20px;
            }
            .notes {
                margin-top: 30px;
                padding: 15px;
                background-color: #f8f9fa;
                border-left: 4px solid #3498db;
                border-radius: 4px;
            }
            .watermark {
                opacity: 0.1;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 80px;
                color: #000;
                pointer-events: none;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Watermark -->
            <div class="watermark">INVOICE</div>

<div class="header">
                <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td style="width:70%; vertical-align: top; border:none;">
                                <div class="company-info">
                                    <h2>' . ($invoice_data['company_name']) . '</h2>
                                    <p>' . ($invoice_data['company_address']) . '</p>
                                    <p>Phone: ' . ($invoice_data['company_phone']) . '</p>
                                    <p>Email: ' . ($invoice_data['company_email']) . '</p>
                                </div>
                            </td>
                            <td style="width:30%; vertical-align: top; border:none;">
                                <div class="invoice-info">
                                    <h1>INVOICE</h1>
                                    <p><strong>Invoice No:</strong> ' . ($invoice_data['invoice_no']) . '</p>
                                    <p><strong>Invoice Date:</strong> ' . ($invoice_data['invoice_date']) . '</p>
                                    <p><strong>Due Date:</strong> ' . ($invoice_data['due_date']) . '</p>
                                    <span class="status status-paid">' . ucfirst($invoice_data['status']) . '</span>
                                </div>
                            </td>
                        </tr>
                    </table>
</div>
            <!-- Billing Information -->
            <div class="billing-info">
             <table style="width:100%;">
                        <tr>
                            <td style="width:50%; vertical-align: top; border:none;">
                                 <div class="bill-to">
                    <div class="section-title">Bill To</div>
                    <p><strong>' . ($invoice_data['client_name']) . '</strong></p>
                    <p>Attn: ' . ($invoice_data['client_contact']) . '</p>
                    <p>' . ($invoice_data['client_address']) . '</p>
                    <p>' . ($invoice_data['client_city']) . '</p>
                    <p>Email: ' . ($invoice_data['client_email']) . '</p>
                </div>
                            </td>
                            <td style="width:50%; vertical-align: top; border:none;">
                                <div class="invoice-details">
                    <div class="section-title">Invoice Details</div>
                    <p><strong>Invoice Date:</strong> ' . ($invoice_data['invoice_date']) . '</p>
                    <p><strong>Due Date:</strong> ' . ($invoice_data['due_date']) . '</p>
                    <p><strong>Tax Rate:</strong> ' . ($invoice_data['tax_rate']) . '%</p>
                </div>
                            </td>
                        </tr>
                    </table>
               
                
            </div>

            <!-- Services Table -->
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="65%">Service Description</th>
                        <th width="30%">Amount</th>
                    </tr>
                </thead>
                <tbody>';

    // Add services dynamically
    $services = $invoice_data['services'];

    $subtotal = 0;
    foreach ($services as $index => $service) {
        $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . htmlspecialchars($service['description']) . '</td>
                        <td>' . CURRENCY_SYMBOL . number_format($service['amount'], 2) . '</td>
                    </tr>';
        $subtotal += $service['amount'];
    }

    $tax_rate = $invoice_data['tax_rate'];
    $tax_amount = $subtotal * ($tax_rate / 100);
    $total_amount = $subtotal + $tax_amount;
    $paid_amount = $invoice_data['paid_amount'];
    $due_amount = $total_amount - $paid_amount;

    $html .= '
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="totals">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="amount">' . CURRENCY_SYMBOL . number_format($subtotal, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Tax (' . number_format($tax_rate, 2) . '%):</td>
                        <td class="amount">' . CURRENCY_SYMBOL . number_format($tax_amount, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Discount:</td>
                        <td class="amount">' . CURRENCY_SYMBOL . number_format($invoice_data['discount'] ?? 0.00, 2) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td class="amount"><strong>' . CURRENCY_SYMBOL . number_format($total_amount, 2) . '</strong></td>
                    </tr>
                    <tr>
                        <td>Paid Amount:</td>
                        <td class="amount">' . CURRENCY_SYMBOL . number_format($paid_amount, 2) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Due Amount:</strong></td>
                        <td class="amount"><strong>' . CURRENCY_SYMBOL . number_format($due_amount, 2) . '</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Payment Instructions -->
            <div class="notes">
               ' . $invoice_data["notes"] . '
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>Thank you for your business!</strong></p>
                <p>' . ($invoice_data['company_name']) . ' | ' . ($invoice_data['company_phone']) . ' | ' . ($invoice_data['company_email']) . '</p>
                <p>This is a computer generated invoice. No signature required.</p>
            </div>
        </div>
    </body>
    </html>';

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

    // select invoice
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

    // customer details
    $customer_data = $db->select_one("customers", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "id" => $customer_id
    ]);

    // Fetch invoice items
    $invoice_items = $db->select_one("invoice_items", "*", [
        "invoice_id" => $invoice_id
    ]);

    $services_final = [];

    if (!empty($invoice_items) && !empty($invoice_items['services_id'])) {
        // Convert "6,12,10" → [6, 12, 10]
        $service_ids = explode(",", $invoice_items['services_id']);
        $service_ids = array_map('intval', $service_ids);

        if (!empty($service_ids)) {
            // Fetch all matching services
            $placeholders = implode(',', $service_ids);
            $services = $db->query("SELECT id, text, amount FROM services WHERE id IN ($placeholders)", ["select_query" => true]);
            // Format result
            foreach ($services as $srv) {
                $services_final[] = [
                    'description' => $srv['text'],
                    'amount' => $srv['amount']
                ];
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
        'notes' => $invoice['notes']
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
