<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js",
    "report.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css",
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-paid {
            background-color: #28a745;
            color: white;
        }

        .status-unpaid {
            background-color: #dc3545;
            color: white;
        }

        .status-partial {
            background-color: #ffc107;
            color: black;
        }

        .status-cancelled {
            background-color: #6c757d;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content income-report-container" id="mainContent">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h3 mb-0"><i class="fas fa-chart-line mr-2"></i>Financial Income Reports</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Report Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="reportFilter">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Vehicle Reg</label>
                                    <input type="text" class="form-control" id="vehicle_reg" name="vehicle_reg"
                                        placeholder="Enter registration">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                        placeholder="Enter name">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-control" id="report_type" name="report_type">
                                        <option value="all">All Invoices</option>
                                        <option value="paid">Paid Income</option>
                                        <option value="unpaid">Unpaid Income</option>
                                        <option value="write_off">Write-off Income</option>
                                    </select>
                                </div>
                                <div class="col-md-9 d-flex align-items-end mt-3">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search me-1"></i> Generate Report
                                    </button>
                                    <!-- <button type="button" id="exportPdf" class="btn btn-success mr-2">
                                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                                    </button> -->
                                    <button type="button" id="exportExcel" class="btn btn-success">
                                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Summary Cards -->
        <div class="row mb-4" id="summarySection" style="display: none;">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i> Report Summary</h5>
                <div class="row" id="summaryCards">
                    <!-- Summary cards will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2"> Generating report...</p>
        </div>

        <!-- Results Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i> Income Report Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="reportTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Vehicle</th>
                                        <th>Status</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reportData">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <?php require_once('./includes/js.php'); ?>
</body>

</html>