<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a0db5 0%, #1c68e6 100%);
            transform: translateY(-2px);
        }

        .table th {
            background-color: #e9ecef;
            border-top: none;
        }

        .badge-paid {
            background-color: #28a745;
        }

        .badge-unpaid {
            background-color: #dc3545;
        }

        .badge-partial {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-cancelled {
            background-color: #6c757d;
        }

        .search-form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .invoice-actions .btn {
            margin: 2px;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-search me-2"></i> Invoice Search</h3>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <form id="searchForm" class="search-form">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="invoice_no" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" id="invoice_no" name="invoice_no">
                                </div>
                                <div class="col-md-3">
                                    <label for="customer_name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name">
                                </div>
                                <div class="col-md-3">
                                    <label for="reg_number" class="form-label">Vehicle Registration</label>
                                    <input type="text" class="form-control" id="reg_number" name="reg_number">
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="paid">Paid</option>
                                        <option value="unpaid">Unpaid</option>
                                        <option value="partial">Partial</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-search me-2"></i>Search Invoices
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Loading Indicator -->
                        <div id="loading" class="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Searching invoices...</p>
                        </div>

                        <!-- Results Table -->
                        <div id="resultsSection" class="mt-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Search Results</h4>
                                <span id="resultCount" class="badge bg-primary"></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Vehicle</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
                                    </tbody>
                                </table>
                            </div>
                            <div id="noResults" class="alert alert-info text-center" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>No invoices found matching your search criteria.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const SITE_URL = '<?= SITE_URL ?>';
    </script>
    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            // Form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                searchInvoices();
            });

            // Reset form
            $('#resetBtn').on('click', function() {
                $('#searchForm')[0].reset();
                $('#resultsSection').hide();
                location.href = "";
            });

            // Search invoices function
            function searchInvoices() {
                // Show loading indicator
                $('#loading').show();
                $('#resultsSection').hide();
                $('#noResults').hide();

                // Get form data
                const formData = $('#searchForm').serialize();

                // Send AJAX request
                $.ajax({
                    url: 'controllers/search-invoices?fetchFilteredInvoices=true',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();

                        if (response.success) {
                            displayResults(response.invoices);
                        } else {
                            $('#resultsBody').empty();
                            $('#noResults').show();
                            $('#resultsSection').show();
                            $('#resultCount').text('0 invoices found');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading').hide();
                        console.error('Error:', error);
                        alert('An error occurred while searching invoices. Please try again.');
                    }
                });
            }

            // Display results
            function displayResults(invoices) {
                const resultsBody = $('#resultsBody');
                resultsBody.empty();

                if (invoices.length === 0) {
                    $('#noResults').show();
                    $('#resultCount').text('0 invoices found');
                } else {
                    let totalAmount = 0;
                    let totalPaid = 0;
                    let totalDue = 0;

                    invoices.forEach(function(invoice) {
                        const statusClass = `badge-${invoice.status}`;
                        const statusText = invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1);

                        totalAmount += parseFloat(invoice.total_amount);
                        totalPaid += parseFloat(invoice.paid_amount);
                        totalDue += parseFloat(invoice.due_amount);

                        const row = `
                <tr>
                    <td>${invoice.invoice_no}</td>
                    <td>${invoice.invoice_date}</td>
                    <td>${invoice.customer_name}</td>
                    <td>${invoice.reg_number}</td>
                    <td>£${parseFloat(invoice.total_amount).toFixed(2)}</td>
                    <td>£${parseFloat(invoice.paid_amount).toFixed(2)}</td>
                    <td>£${parseFloat(invoice.due_amount).toFixed(2)}</td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td class="invoice-actions">
                        <a href="${SITE_URL}/uploads/invoices/${invoice.pdf_file}" class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>
            `;
                        resultsBody.append(row);
                    });

                    // Append total row
                    const totalRow = `
            <tr style="font-weight:bold; background-color:#f1f1f1;">
                <td colspan="4" class="text-end">Total:</td>
                <td>£${totalAmount.toFixed(2)}</td>
                <td>£${totalPaid.toFixed(2)}</td>
                <td>£${totalDue.toFixed(2)}</td>
                <td colspan="2"></td>
            </tr>
        `;
                    resultsBody.append(totalRow);

                    $('#resultCount').text(`${invoices.length} invoice(s) found`);
                }

                $('#resultsSection').show();
            }

        });
    </script>
</body>

</html>