<?php

require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

$branch_logo = "http://localhost/projects/car_invoice_crm/images/logo_img.png";
$signature_img = "http://localhost/projects/car_invoice_crm/images/PR-Auto-Centre-Logo-dark.png";

$html = <<<HTML
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice INV-20556</title>
    <style>
        body {
            size: A4;
            margin: 0;
            padding: 0;
            position: relative;
        }

        @page {
            margin-top: 10mm;
            margin-right: 12mm;
            margin-bottom: 0mm; 
            margin-left: 12mm;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: -12mm;
            width: calc(100% + 15mm);
            background-color: #214F79 !important;
            color: white;
            padding: 10px 20px;
            box-sizing: border-box;
            padding-bottom:0px !important;
            padding-left:12mm; 
        }

        .main-content {
            padding-bottom: 35mm;
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

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            width: 50%;
            padding: 5px 8px;
            padding-bottom:0px !important;
        }

        .invoice-notes {
            border-left: 4px solid #214F79;
            padding: 10px 20px;
            background: #ECF9FF;
            margin-top: 20px;
            font-size: 14px;
        }
    hr{
        margin:0px !important;
        padding:0px !important;
    }
    .p-0{
        padding:0px !important;
    }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="invoice-pdf-container">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:50%; padding-right:20px; vertical-align: top;">
                        <h1 style="color:#214F79;margin:0px;">Invoice</h1>
                        <h4 style="margin:0px;margin-top:5px;font-size:15px;">Invoice number : <span style="color:#6C6C6D;font-size:13px;">#010101</span></h4>
                        <h4 style="margin:0px;margin-top:5px;font-size:15px;">Issued on: <span style="color:#6C6C6D;font-size:13px;">#010101</span></h4>
                        <h4 style="margin:0px;margin-top:5px;font-size:15px;">Due Date : <span style="color:#6C6C6D;font-size:13px;">#010101</span></h4>
                        </h4>
                    </td>
                    <td style="width:50%; padding-left:20px; vertical-align: top;text-align: right;">
                        <img src="{$signature_img}" style="width:80%;">
                    </td>
                </tr>
            </table>
            <div style="padding-left:15px;padding-right:15px;">
                <hr style="border:0.5px solid #ccc;">
            </div>
            <div class="m-0 p-0">
                <table style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td style="width:100%; padding-right:20px; vertical-align: top;">
                            <h4 style="margin:0px;margin-bottom:10px;margin-top:10px;color:#6C6C6D;font-size:15px;">Bill to
                            </h4>
                            <h4 style="margin:0px;margin-top:5px;color:#214F79;font-weight:500;font-size:16px;">Intellectual
                                Bunch Limited</h4>
                            <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">123 Business Ave New York,
                                NY </h4>
                            <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">Phone: (555) 123-4567</h4>
                            <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">hello@gmail.com</h4>
                            <h4 style="margin:0px;margin-top:5px;color:#6C6C6D;font-size:13px;">(555) 123-4567</h4>
                        </td>
                    </tr>
                </table>
            </div>
            <table style="margin:0px;">
                <thead>
                    <tr>
                        <th width="5%" style="font-size:14px !important;">#</th>
                        <th width="65%" style="font-size:14px !important;">Service Description</th>
                        <th width="30%" style="font-size:14px !important;">Quantity</th>
                        <th width="30%" style="font-size:14px !important;">Amount</th>
                        <th width="30%" style="font-size:14px !important;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>testing</td>
                        <td>1</td>
                        <td>£200.00</td>
                        <td>£200.00</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>testing</td>
                        <td>1</td>
                        <td>£200.00</td>
                        <td>£200.00</td>
                    </tr>
            </table>
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:50%; padding-right:20px; vertical-align: bottom;">
                    </td>
                    <td style="width:50%; padding-left:20px; vertical-align: top;text-align: right;">
                        <table style="margin-top:20px;">
                            <tbody>
                                <tr>
                                    <td>SubTotal:</td>
                                    <td style="text-align: right;">£1350.00</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold !important;">Vat (11.00%):</td>
                                    <td style="text-align: right;">$200.00</td>
                                </tr>
                                <tr>
                                    <td>Discount:</td>
                                    <td style="text-align: right;">£1350.00</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;">Total Amount:</td>
                                    <td style="text-align: right;font-weight:bold !important;">$200.00</td>
                                </tr>
                                <tr>
                                    <td>Discount:</td>
                                    <td style="text-align: right;">£1350.00</td>
                                </tr>
                                <tr style="background:#214F79;">
                                    <td style="color:white;font-weight:bold;">Total Amount:</td>
                                    <td style="text-align: right; color:white; font-weight:bold;">$200.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="invoice-notes">
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Illum, ex!
            </div>
            <h3 style="color:#214F79; margin-top:20px;">Thank you for your Business.</h3>
        </div>
    </div>
    
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width:50%; padding-right:20px; vertical-align: top;">
                    <strong>Payment terms:</strong><br>
                    <p style="margin-top:5px;font-size:12px;">Payment is due within seven (7) days of the invoice date. Late payments may be subject to a surcharge of 2% per month, calculated from the due date until payment is received.</p>
                </td>

                <td style="width:50%; padding-left:20px; vertical-align: top;">
                    <table class="footer-table m-0">
                        <tr>
                            <td style="width:50%; vertical-align: top;">
                                <strong>Address:</strong><br>
                                <p style="margin-top:5px; font-size:12px;">48 Catley Road, Sheffield, S9 5JF </p>
                            </td>

                            <td style="width:50%; vertical-align: top;">
                                <strong>Find Us:</strong><br>
                                <p style="margin-top:5px;font-size:12px">0114 2619965<br>
                                www.hillcliffe.com <br>
                                info@hillcliffe.com</p>
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

$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("invoice.pdf", ["Attachment" => false]);
