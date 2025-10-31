<?php
require_once('includes/db.php');
// Include DomPDF library
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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
                // padding: 20px;
                background-color: #f5f5f5;
            }
            .invoice-container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #ddd;
                padding: 30px;
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
                                    <h2>' . ($invoice_data['company_name'] ?? 'intellectual bunch limited.') . '</h2>
                                    <p>' . ($invoice_data['company_address'] ?? '123 Business Street, Suite 100') . '</p>
                                    <p>' . ($invoice_data['company_city'] ?? 'New York, NY 10001') . '</p>
                                    <p>Phone: ' . ($invoice_data['company_phone'] ?? '(555) 123-4567') . '</p>
                                    <p>Email: ' . ($invoice_data['company_email'] ?? 'info@techsolutions.com') . '</p>
                                    <p>Website: ' . ($invoice_data['company_website'] ?? 'www.techsolutions.com') . '</p>
                                </div>
                            </td>
                            <td style="width:30%; vertical-align: top; border:none;">
                                <div class="invoice-info">
                                    <h1>INVOICE</h1>
                                    <p><strong>Invoice No:</strong> ' . ($invoice_data['invoice_no'] ?? 'INV-2024-001') . '</p>
                                    <p><strong>Invoice Date:</strong> ' . ($invoice_data['invoice_date'] ?? 'Jan 15, 2024') . '</p>
                                    <p><strong>Due Date:</strong> ' . ($invoice_data['due_date'] ?? 'Feb 14, 2024') . '</p>
                                    <span class="status status-' . ($invoice_data['status'] ?? 'pending') . '">' . ucfirst($invoice_data['status'] ?? 'pending') . '</span>
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
                    <p><strong>' . ($invoice_data['client_name'] ?? 'ABC Corporation') . '</strong></p>
                    <p>Attn: ' . ($invoice_data['client_contact'] ?? 'John Smith') . '</p>
                    <p>' . ($invoice_data['client_address'] ?? '456 Client Avenue') . '</p>
                    <p>' . ($invoice_data['client_city'] ?? 'Boston, MA 02108') . '</p>
                    <p>Phone: ' . ($invoice_data['client_phone'] ?? '(555) 987-6543') . '</p>
                    <p>Email: ' . ($invoice_data['client_email'] ?? 'john.smith@abccorp.com') . '</p>
                </div>
                            </td>
                            <td style="width:50%; vertical-align: top; border:none;">
                                <div class="invoice-details">
                    <div class="section-title">Invoice Details</div>
                    <p><strong>Invoice Date:</strong> ' . ($invoice_data['invoice_date'] ?? 'Jan 15, 2024') . '</p>
                    <p><strong>Due Date:</strong> ' . ($invoice_data['due_date'] ?? 'Feb 14, 2024') . '</p>
                    <p><strong>Tax Rate:</strong> ' . ($invoice_data['tax_rate'] ?? '10.00') . '%</p>
                    <p><strong>Payment Terms:</strong> ' . ($invoice_data['payment_terms'] ?? 'Net 30') . '</p>
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
    $services = $invoice_data['services'] ?? [
        ['description' => 'Website Development - Corporate Website', 'amount' => 1500.00],
        ['description' => 'E-commerce Platform Integration', 'amount' => 2000.00],
        ['description' => 'Mobile App Development', 'amount' => 3500.00],
        ['description' => 'SEO Optimization Services', 'amount' => 800.00],
        ['description' => 'Monthly Maintenance & Support', 'amount' => 300.00]
    ];

    $subtotal = 0;
    foreach ($services as $index => $service) {
        $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . htmlspecialchars($service['description']) . '</td>
                        <td>$' . number_format($service['amount'], 2) . '</td>
                    </tr>';
        $subtotal += $service['amount'];
    }

    $tax_rate = $invoice_data['tax_rate'] ?? 10.00;
    $tax_amount = $subtotal * ($tax_rate / 100);
    $total_amount = $subtotal + $tax_amount;
    $paid_amount = $invoice_data['paid_amount'] ?? 2000.00;
    $due_amount = $total_amount - $paid_amount;

    $html .= '
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="totals">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="amount">$' . number_format($subtotal, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Tax (' . number_format($tax_rate, 2) . '%):</td>
                        <td class="amount">$' . number_format($tax_amount, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Discount:</td>
                        <td class="amount">$' . number_format($invoice_data['discount'] ?? 0.00, 2) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td class="amount"><strong>$' . number_format($total_amount, 2) . '</strong></td>
                    </tr>
                    <tr>
                        <td>Paid Amount:</td>
                        <td class="amount">$' . number_format($paid_amount, 2) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Due Amount:</strong></td>
                        <td class="amount"><strong>$' . number_format($due_amount, 2) . '</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Payment Instructions -->
            <div class="notes">
                <strong>Payment Instructions:</strong><br>
                ' . ($invoice_data['payment_instructions'] ?? 'Please make payment within 30 days of invoice date. You can pay by bank transfer or credit card.<br>
                <strong>Bank Details:</strong> ABC Bank | Account: 123456789 | Routing: 021000021<br>
                <strong>For credit card payments:</strong> Visit our payment portal at payments.techsolutions.com') . '
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>Thank you for your business!</strong></p>
                <p>' . ($invoice_data['company_name'] ?? 'intellectual bunch limited.') . ' | ' . ($invoice_data['company_phone'] ?? '(555) 123-4567') . ' | ' . ($invoice_data['company_email'] ?? 'info@techsolutions.com') . ' | ' . ($invoice_data['company_website'] ?? 'www.techsolutions.com') . '</p>
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
        $invoice_dir = __DIR__ . '/invoices/';
        if (!is_dir($invoice_dir)) {
            mkdir($invoice_dir, 0755, true);
        }

        // Generate filename
        // $filename = 'invoice_' . ($invoice_data['invoice_no'] ?? 'INV-2024-001') . '.pdf';
        $filename = "invoice_test.pdf";
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

// Usage Example:
if (isset($_POST['saveInvoice'])) {
    // Your existing invoice saving code...

    // After saving invoice to database, generate PDF
    $invoice_data = [
        'company_name' => 'Your Company Name',
        'company_address' => 'Your Company Address',
        'company_city' => 'Your City, State ZIP',
        'company_phone' => '(555) 123-4567',
        'company_email' => 'your@email.com',
        'company_website' => 'www.yourcompany.com',
        'invoice_no' => $invoice_no, // from your form
        'invoice_date' => $invoice_date, // from your form
        'due_date' => $due_date, // from your form
        'status' => $status, // from your form
        'client_name' => 'Client Company Name', // get from customer_id
        'client_contact' => 'Contact Person',
        'client_address' => 'Client Address',
        'client_city' => 'Client City, State ZIP',
        'client_phone' => '(555) 987-6543',
        'client_email' => 'client@email.com',
        'tax_rate' => $tax_rate, // from your form
        'paid_amount' => $paid_amount, // from your form
        'services' => $valid_services, // from your form processing
        'payment_instructions' => 'Your payment instructions...',
        'notes' => $notes // from your form
    ];

    $result = saveInvoicePDF($invoice_data);

    if ($result['success']) {
        echo "Invoice PDF saved successfully: " . $result['filepath'];

        // You can also download it immediately
        // header('Content-Type: application/pdf');
        // header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        // readfile($result['filepath']);
        // exit;
    } else {
        echo "Error generating PDF: " . $result['error'];
    }
}

// Simple test function
function testGenerateInvoice()
{
    $test_data = [
        'invoice_no' => 'INV-2024-001',
        'invoice_date' => date('M d, Y'),
        'due_date' => date('M d, Y', strtotime('+30 days')),
        'status' => 'pending',
        'tax_rate' => 10.00,
        'paid_amount' => 2000.00,
    ];

    $result = saveInvoicePDF($test_data);

    if ($result['success']) {
        echo "Test invoice generated successfully!<br>";
        echo "File saved: " . $result['filepath'] . "<br>";
        echo "Filename: " . $result['filename'] . "<br>";
    } else {
        echo "Error: " . $result['error'] . "<br>";
    }
}

// Uncomment to test
// testGenerateInvoice();

// $db->insert("users", [
//     "fname" => ["encrypt" => "Super"],
//     "lname" => ["encrypt" => "Admin"],
//     "gender" => ["encrypt" => "Male"],
//     "title" => ["encrypt" => "Mr"],
//     "name" => ["encrypt" => "Super Admin"],
//     "email" => "admin@gmail.com",
//     "password" => ["encrypt" => "$2y$10$101hiV2jGn2sTvMpszu/peorZ4Uj6oZZS/CeaA0.4OKi4WJ7T8EMm"],
//     "type" => "main_admin",
//     "address" => ["encrypt" => ""],
//     "contact" => ["encrypt" => "+923081438096"],
//     "city" => ["encrypt" => "Mian Channu"],
//     "image" => ["encrypt" => "avatar.png"],
//     "is_admin" =>  1,
//     "verify_status" => 1,
// ]);
