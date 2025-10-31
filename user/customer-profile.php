<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];
$CSS_FILES_ = [
    "customer-profile.css"
];


$get_id = $_GET['id'];

$customer = $db->select_one("customers", "*", [
    "id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$invoice = $db->select("invoices", "*", [
    "customer_id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$company = $db->select_one("companies", "*", [
    "id" => LOGGED_IN_USER['company_id']
]);


$total_paid = 0;
$total_due = 0;

foreach ($invoice as $inv) {
    $total_paid += $inv['paid_amount'];
    $total_due  += $inv['due_amount'];
}

$total_invoice = count($invoice);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <div class="container">
            <div class="header">
                <h1>Customer Profile</h1>
                <div class="header-actions">
                    <a class="btn btn-secondary" href="dashboard">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a class="btn btn-primary" href="add-customer?id=<?= $get_id ?>">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a class="btn btn-success" href="invoice">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-info">
                        <h2><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></h2>
                        <p><?= $customer['address'] ?></p>
                        <p><i class="fas fa-envelope"></i> &nbsp;&nbsp;<?= $customer['email'] ?> &nbsp;&nbsp;| &nbsp;&nbsp;<i class="fas fa-phone"></i> &nbsp;&nbsp; <?= $customer['contact'] ?></p>
                        <div>
                            <span class="badge badge-active">Active</span>
                            <span class="badge badge-premium">Premium Client</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3><?= $total_invoice ?></h3>
                    <p>Total Invoices</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>$<?= $total_paid ?></h3>
                    <p>Total Paid</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>$<?= $total_due ?></h3>
                    <p>Total Due</p>
                </div>
            </div>

            <div class="tabs">
                <div class="tab active" data-tab="overview">
                    <i class="fas fa-user-circle"></i> Overview
                </div>
                <div class="tab" data-tab="invoices">
                    <i class="fas fa-file-invoice"></i> Invoices
                </div>
                <div class="tab" data-tab="notes">
                    <i class="fas fa-sticky-note"></i> Notes
                </div>
            </div>

            <div class="tab-content active" id="overview">
                <h3 class="section-title">Customer Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Company</div>
                        <div class="info-value"><?= $company['company_name'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= $customer['email'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?= $customer['contact'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?= $customer['address'] ?></div>
                    </div>
                </div>

                <h3 class="section-title" style="margin-top: 30px;">Recent Invoices</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_paid = 0;
                            $total_due  = 0;

                            if (!empty($invoice)) {
                                foreach ($invoice as $inv) {
                                    $total_paid += $inv['paid_amount'];
                                    $total_due  += $inv['due_amount'];

                                    $status_class = '';
                                    if ($inv['status'] === 'paid') $status_class = 'status-paid';
                                    elseif ($inv['status'] === 'pending') $status_class = 'status-pending';
                                    elseif ($inv['status'] === 'overdue') $status_class = 'status-overdue';
                            ?>
                                    <tr>
                                        <td><?= $inv['invoice_no'] ?></td>
                                        <td><?= date('M d, Y', strtotime($inv['invoice_date'])) ?></td>
                                        <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                                        <td><span class="status <?= $status_class ?>"><?= ucfirst($inv['status']) ?></span></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="4" style="text-align:center;">No invoices found</td></tr>';
                            }
                            ?>
                        </tbody>

                        <?php if (!empty($invoice)) { ?>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Total Paid:</th>
                                    <th colspan="2" style="text-align:left;">$<?= number_format($total_paid, 2) ?></th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Total Due:</th>
                                    <th colspan="2" style="text-align:left;">$<?= number_format($total_due, 2) ?></th>
                                </tr>
                            </tfoot>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="invoices">
                <h3 class="section-title">Invoice History</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_paid = 0;
                            $total_due  = 0;

                            if (!empty($invoice)) {
                                foreach ($invoice as $inv) {
                                    $total_paid += $inv['paid_amount'];
                                    $total_due  += $inv['due_amount'];

                                    // Status color classes
                                    $status_class = '';
                                    if ($inv['status'] === 'paid') $status_class = 'status-paid';
                                    elseif ($inv['status'] === 'pending') $status_class = 'status-pending';
                                    elseif ($inv['status'] === 'overdue') $status_class = 'status-overdue';
                            ?>
                                    <tr>
                                        <td><?= htmlspecialchars($inv['invoice_no']) ?></td>
                                        <td><?= date('M d, Y', strtotime($inv['invoice_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($inv['due_date'])) ?></td>
                                        <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                                        <td>$<?= number_format($inv['paid_amount'], 2) ?></td>
                                        <td>$<?= number_format($inv['due_amount'], 2) ?></td>
                                        <td><span class="status <?= $status_class ?>"><?= ucfirst($inv['status']) ?></span></td>
                                        <td>
                                            <a class="btn text-white"
                                                href="<?= _DIR_ ?>/uploads/invoices/<?= htmlspecialchars($inv['pdf_file']) ?>"
                                                target="_blank"
                                                style="padding: 5px 10px; font-size: 12px;">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" style="text-align:center;">No invoices found</td></tr>';
                            }
                            ?>
                        </tbody>

                        <?php if (!empty($invoice)) { ?>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right;">Total Paid:</th>
                                    <th colspan="4" style="text-align:left;">$<?= number_format($total_paid, 2) ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4" style="text-align:right;">Total Due:</th>
                                    <th colspan="4" style="text-align:left;">$<?= number_format($total_due, 2) ?></th>
                                </tr>
                            </tfoot>
                        <?php } ?>
                    </table>

                </div>
            </div>

            <div class="tab-content" id="notes">
                <h3 class="section-title">Customer Notes</h3>
                <?php foreach ($invoice as $inv) { ?>
                    <div class="notes-container mb-3">
                        <?= $inv['notes'] ?>
                    </div>
                <?php } ?>

                <!-- <h3 class="section-title" style="margin-top: 30px;">Add New Note</h3> -->
                <!-- <div style="background: #f8fafc; padding: 20px; border-radius: 8px;">
                    <textarea style="width: 100%; height: 120px; padding: 15px; border: 1px solid #e1e5eb; border-radius: 6px; resize: vertical;"></textarea>
                    <button class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-save"></i> Save Note
                    </button>
                </div> -->
            </div>

            <!-- <div class="tab-content" id="activity">
                <h3 class="section-title">Recent Activity</h3>
                <div class="activity-timeline">
                    <div class="timeline-item">
                        <div class="timeline-date">March 10, 2023</div>
                        <div class="timeline-content">
                            <strong>New invoice created</strong> - INV-2023-105 for $1,250.00
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">March 5, 2023</div>
                        <div class="timeline-content">
                            <strong>Customer note updated</strong> by Sarah Johnson
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">March 1, 2023</div>
                        <div class="timeline-content">
                            <strong>Payment received</strong> for invoice INV-2023-098 - $2,500.00
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">February 28, 2023</div>
                        <div class="timeline-content">
                            <strong>New invoice created</strong> - INV-2023-098 for $2,500.00
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">February 20, 2023</div>
                        <div class="timeline-content">
                            <strong>Customer contacted</strong> regarding overdue invoice INV-2023-087
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <?php require_once('./includes/js.php'); ?>
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetTab = tab.getAttribute('data-tab');

                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to current tab and content
                    tab.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });
    </script>
</body>

</html>