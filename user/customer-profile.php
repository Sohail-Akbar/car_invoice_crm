<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "customer-profile.js",
    _DIR_ . "js/select2.min.js",
    _DIR_ . "js/jquery.dataTables.min.js",
    _DIR_ . "js/tinymce/tinymce.min.js"
];
$CSS_FILES_ = [
    "customer-profile.css",
    _DIR_ . "css/select2.min.css",
    _DIR_ . "css/jquery.dataTables.min.css"
];


$get_id = $_GET['id'];

$customer = $db->select_one("users", "*", [
    "id" => $get_id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "customer"
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



$agency_id = LOGGED_IN_USER['agency_id'];
$assigned_staff = $db->query("
    SELECT 
        cs.id,
        cs.staff_id,
        cs.assignment_date,
        u.fname,
        u.lname,
        u.email,
        u.contact,
        u.title
    FROM customer_staff cs
    INNER JOIN users u ON cs.staff_id = u.id
    WHERE cs.customer_id = $get_id
      AND cs.company_id = " . LOGGED_IN_USER['company_id'] . "
      AND cs.agency_id = $agency_id
      AND cs.is_active = 1
      AND u.type = 'staff'
", [
    "select_query" => true,
]);


// Customer Cars
$cars = $db->select("customer_car_history", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1,
    "customer_id" => $get_id,
]);

// Email Templates
$email_template = $db->select("email_template", "id,email_title,email_body", [
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "company_id" => LOGGED_IN_USER['company_id'],
    "is_active" => 1
]);
if (empty($email_template)) $email_template = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        <?php
        if (LOGGED_IN_USER['type']  === "customer") { ?>.sidebar {
            display: none;
        }

        .main-content {
            margin-left: 0 !important;
        }

        .navbar {
            padding-left: 20px;
        }

        #toggleSidebar {
            display: none !important;
        }

        <?php   }
        ?>
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content customer-profile-container" id="mainContent">
        <div class="profile-header">
            <div class="row mx-0">
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="d-flex justify-content-center">
                                <div class="avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="profile-info">
                                <div class="head">
                                    <h2 class="name mt-4"><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></h2>
                                    <span class="badge active d-none">Active</span>
                                    <span class="badge premium d-none" style="background-color: #e7eef5;">Premium Client</span>
                                </div>
                                <div class="single-info mt-5 mb-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 3.5H7C4 3.5 2 5 2 8.5V15.5C2 19 4 20.5 7 20.5H17C20 20.5 22 19 22 15.5V8.5C22 5 20 3.5 17 3.5ZM17.47 9.59L14.34 12.09C13.68 12.62 12.84 12.88 12 12.88C11.16 12.88 10.31 12.62 9.66 12.09L6.53 9.59C6.21 9.33 6.16 8.85 6.41 8.53C6.67 8.21 7.14 8.15 7.46 8.41L10.59 10.91C11.35 11.52 12.64 11.52 13.4 10.91L16.53 8.41C16.85 8.15 17.33 8.2 17.58 8.53C17.84 8.85 17.79 9.33 17.47 9.59Z" fill="#214F79" />
                                    </svg>
                                    <span class="text-clr ml-2 mb-1"><?= $customer['email'] ?></span>
                                </div>
                                <div class="single-info mb-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.05 14.95L9.2 16.8C8.81 17.19 8.19 17.19 7.79 16.81C7.68 16.7 7.57 16.6 7.46 16.49C6.43 15.45 5.5 14.36 4.67 13.22C3.85 12.08 3.19 10.94 2.71 9.81C2.24 8.67 2 7.58 2 6.54C2 5.86 2.12 5.21 2.36 4.61C2.6 4 2.98 3.44 3.51 2.94C4.15 2.31 4.85 2 5.59 2C5.87 2 6.15 2.06 6.4 2.18C6.66 2.3 6.89 2.48 7.07 2.74L9.39 6.01C9.57 6.26 9.7 6.49 9.79 6.71C9.88 6.92 9.93 7.13 9.93 7.32C9.93 7.56 9.86 7.8 9.72 8.03C9.59 8.26 9.4 8.5 9.16 8.74L8.4 9.53C8.29 9.64 8.24 9.77 8.24 9.93C8.24 10.01 8.25 10.08 8.27 10.16C8.3 10.24 8.33 10.3 8.35 10.36C8.53 10.69 8.84 11.12 9.28 11.64C9.73 12.16 10.21 12.69 10.73 13.22C10.83 13.32 10.94 13.42 11.04 13.52C11.44 13.91 11.45 14.55 11.05 14.95Z" fill="#214F79" />
                                        <path d="M21.9696 18.33C21.9696 18.61 21.9196 18.9 21.8196 19.18C21.7896 19.26 21.7596 19.34 21.7196 19.42C21.5496 19.78 21.3296 20.12 21.0396 20.44C20.5496 20.98 20.0096 21.37 19.3996 21.62C19.3896 21.62 19.3796 21.63 19.3696 21.63C18.7796 21.87 18.1396 22 17.4496 22C16.4296 22 15.3396 21.76 14.1896 21.27C13.0396 20.78 11.8896 20.12 10.7496 19.29C10.3596 19 9.96961 18.71 9.59961 18.4L12.8696 15.13C13.1496 15.34 13.3996 15.5 13.6096 15.61C13.6596 15.63 13.7196 15.66 13.7896 15.69C13.8696 15.72 13.9496 15.73 14.0396 15.73C14.2096 15.73 14.3396 15.67 14.4496 15.56L15.2096 14.81C15.4596 14.56 15.6996 14.37 15.9296 14.25C16.1596 14.11 16.3896 14.04 16.6396 14.04C16.8296 14.04 17.0296 14.08 17.2496 14.17C17.4696 14.26 17.6996 14.39 17.9496 14.56L21.2596 16.91C21.5196 17.09 21.6996 17.3 21.8096 17.55C21.9096 17.8 21.9696 18.05 21.9696 18.33Z" fill="#214F79" />
                                    </svg>
                                    <span class="text-clr ml-2 mb-1"><?= $customer['contact'] ?></span>
                                </div>
                                <div class="single-info">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.6201 8.45C19.5701 3.83 15.5401 1.75 12.0001 1.75C12.0001 1.75 12.0001 1.75 11.9901 1.75C8.4601 1.75 4.4201 3.82 3.3701 8.44C2.2001 13.6 5.3601 17.97 8.2201 20.72C9.2801 21.74 10.6401 22.25 12.0001 22.25C13.3601 22.25 14.7201 21.74 15.7701 20.72C18.6301 17.97 21.7901 13.61 20.6201 8.45ZM12.0001 13.46C10.2601 13.46 8.8501 12.05 8.8501 10.31C8.8501 8.57 10.2601 7.16 12.0001 7.16C13.7401 7.16 15.1501 8.57 15.1501 10.31C15.1501 12.05 13.7401 13.46 12.0001 13.46Z" fill="#214F79" />
                                    </svg>
                                    <span class="text-clr ml-2"><?= $customer['address'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-12">
                    <div class="pull-away px-5 customer-info-status">
                        <button class="btn d-flex">
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0V16.7143H10.4615V15.4286H1.30769V1.28571H7.84615V5.14286H11.7692V6.42857H13.0769V4.24286L12.8808 4.05L8.95769 0.192857L8.76154 0H0ZM9.15385 2.18571L10.8538 3.85714H9.15385V2.18571ZM2.61538 6.42857V7.71429H10.4615V6.42857H2.61538ZM13.7308 7.71429V9C12.6192 9.19286 11.7692 10.0929 11.7692 11.25C11.7692 12.5357 12.75 13.5 14.0577 13.5H14.7115C15.2346 13.5 15.6923 13.95 15.6923 14.4643C15.6923 14.9786 15.2346 15.4286 14.7115 15.4286H12.4231V16.7143H13.7308V18H15.0385V16.7143C16.15 16.5214 17 15.6214 17 14.4643C17 13.1786 16.0192 12.2143 14.7115 12.2143H14.0577C13.5346 12.2143 13.0769 11.7643 13.0769 11.25C13.0769 10.7357 13.5346 10.2857 14.0577 10.2857H16.3462V9H15.0385V7.71429H13.7308ZM2.61538 9.64286V10.9286H7.19231V9.64286H2.61538ZM8.5 9.64286V10.9286H10.4615V9.64286H8.5ZM2.61538 12.2143V13.5H7.19231V12.2143H2.61538ZM8.5 12.2143V13.5H10.4615V12.2143H8.5Z" fill="white" />
                            </svg>
                            <span class="ml-1">(<?= $total_invoice ?>) Total Invoice</span>
                        </button>
                        <button class="btn d-flex">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 16L7 14M7 14L1 17V4L7 1M7 14V1M7 1L13 4M13 4L19 1V7.5M13 4V9M19 12H16.5C16.1022 12 15.7206 12.158 15.4393 12.4393C15.158 12.7206 15 13.1022 15 13.5C15 13.8978 15.158 14.2794 15.4393 14.5607C15.7206 14.842 16.1022 15 16.5 15H17.5C17.8978 15 18.2794 15.158 18.5607 15.4393C18.842 15.7206 19 16.1022 19 16.5C19 16.8978 18.842 17.2794 18.5607 17.5607C18.2794 17.842 17.8978 18 17.5 18H15M17 18V19M17 11V12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <span class="ml-1">(<?= _CURRENCY_SYMBOL . $total_paid ?>) Total Paid</span>
                        </button>
                        <button class="btn d-flex">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.1 17H10.85V15.75C11.6833 15.6 12.4 15.275 13 14.775C13.6 14.275 13.9 13.5333 13.9 12.55C13.9 11.85 13.7 11.2083 13.3 10.625C12.9 10.0417 12.1 9.53333 10.9 9.1C9.9 8.76667 9.20833 8.475 8.825 8.225C8.44167 7.975 8.25 7.63333 8.25 7.2C8.25 6.76667 8.40434 6.425 8.713 6.175C9.02167 5.925 9.46733 5.8 10.05 5.8C10.5833 5.8 11 5.929 11.3 6.187C11.6 6.445 11.8167 6.766 11.95 7.15L13.55 6.5C13.3667 5.91667 13.0293 5.40833 12.538 4.975C12.0467 4.54167 11.5007 4.3 10.9 4.25V3H9.15V4.25C8.31667 4.43333 7.66667 4.8 7.2 5.35C6.73333 5.9 6.5 6.51667 6.5 7.2C6.5 7.98333 6.72933 8.61667 7.188 9.1C7.64667 9.58333 8.36733 10 9.35 10.35C10.4 10.7333 11.1293 11.075 11.538 11.375C11.9467 11.675 12.1507 12.0667 12.15 12.55C12.15 13.1 11.9543 13.5043 11.563 13.763C11.1717 14.0217 10.7007 14.1507 10.15 14.15C9.59933 14.1493 9.11167 13.9787 8.687 13.638C8.26233 13.2973 7.95 12.7847 7.75 12.1L6.1 12.75C6.33333 13.55 6.696 14.196 7.188 14.688C7.68 15.18 8.31734 15.5173 9.1 15.7V17ZM10 20C8.61667 20 7.31667 19.7373 6.1 19.212C4.88334 18.6867 3.825 17.9743 2.925 17.075C2.025 16.1757 1.31267 15.1173 0.788001 13.9C0.263335 12.6827 0.000667933 11.3827 1.26582e-06 10C-0.000665401 8.61733 0.262001 7.31733 0.788001 6.1C1.314 4.88267 2.02633 3.82433 2.925 2.925C3.82367 2.02567 4.882 1.31333 6.1 0.788C7.318 0.262667 8.618 0 10 0C11.382 0 12.682 0.262667 13.9 0.788C15.118 1.31333 16.1763 2.02567 17.075 2.925C17.9737 3.82433 18.6863 4.88267 19.213 6.1C19.7397 7.31733 20.002 8.61733 20 10C19.998 11.3827 19.7353 12.6827 19.212 13.9C18.6887 15.1173 17.9763 16.1757 17.075 17.075C16.1737 17.9743 15.1153 18.687 13.9 19.213C12.6847 19.739 11.3847 20.0013 10 20Z" fill="white" />
                            </svg>
                            <span class="ml-1">(<?= _CURRENCY_SYMBOL . $total_due ?>) Total Due</span>
                        </button>
                    </div>
                    <div class="card customer-note-container mb-0 p-3 mt-3">
                        <?php if (LOGGED_IN_USER['type']  !== "customer") { ?>
                            <h4 class="card-title mb-0 pull-away note-header">
                                <div class="algin-center d-flex cp add-note">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.8552 2.08221H8.1308C4.34121 2.08221 2.08203 4.34139 2.08203 8.13098V16.845C2.08203 20.645 4.34121 22.9041 8.1308 22.9041H16.8448C20.6344 22.9041 22.8935 20.645 22.8935 16.8554V8.13098C22.9039 4.34139 20.6448 2.08221 16.8552 2.08221ZM16.6574 13.274H13.2738V16.6576C13.2738 17.0844 12.9198 17.4384 12.493 17.4384C12.0661 17.4384 11.7122 17.0844 11.7122 16.6576V13.274H8.32861C7.90176 13.274 7.54778 12.92 7.54778 12.4932C7.54778 12.0663 7.90176 11.7124 8.32861 11.7124H11.7122V8.32879C11.7122 7.90194 12.0661 7.54797 12.493 7.54797C12.9198 7.54797 13.2738 7.90194 13.2738 8.32879V11.7124H16.6574C17.0842 11.7124 17.4382 12.0663 17.4382 12.4932C17.4382 12.92 17.0842 13.274 16.6574 13.274Z" fill="#214F79" />
                                    </svg>
                                    <span>&nbsp;Add Note</span>
                                </div>
                                <button class="btn py-1 px-2 view-note">
                                    <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 8.63232 11.1083 11.2 8.20002 11.2C5.29171 11.2 2.01004 8.63232 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 3.268 11.1088 0.700012 8.20054 0.700012C5.29222 0.700012 2.01004 3.26737 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10.4502 5.95001C10.4502 7.19265 9.44284 8.20001 8.2002 8.20001C6.95755 8.20001 5.9502 7.19265 5.9502 5.95001C5.9502 4.70737 6.95755 3.70001 8.2002 3.70001C9.44284 3.70001 10.4502 4.70737 10.4502 5.95001Z" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    View Note
                                </button>
                            </h4>
                            <div class="mt-3 customer-note-tinymce">
                                <textarea class="customer-note form-control" id="customerNote" rows="8" name="note" placeholder="Start typing to leave a note..."></textarea>
                                <div class="col-md-12 text-right" style="position: absolute;right: 0px;bottom: 12px;">
                                    <button class="btn save-customer-note" type="submit">
                                        <i class="fas fa-save"></i>
                                        Save
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="view-customer-note <?= LOGGED_IN_USER['type']  !== "customer" ? 'd-none' : 'customer-login' ?>">
                            <div id="notesContainer" class="mt-3"></div>
                            <div class="text-center mt-3">
                                <button id="loadMoreNotes" class="btn btn-outline-secondary btn-sm">Load More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mx-0 mt-3">
            <div class="col-md-12 customer-action-btn">
                <a href="add-customer?id=<?= $_GET['id'] ?>&redirectTo=customer-profile?id=<?= $_GET['id'] ?>" class="btn mx-1 br-5 text-white">Update Profile</a>
                <button class="btn mx-1 br-5 text-white"
                    onclick="new bootstrap.Modal(document.querySelector('.view-send-email-model')).show();">
                    Send Email
                </button>
                <a href="send-sms?customer_id=<?= $_GET['id'] ?>&redirectTo=customer-profile?id=<?= $_GET['id'] ?>" class="btn mx-1 br-5 text-white">Send SMS</a>
            </div>
        </div>
        <div class="row mx-0 mt-4">
            <div class="col-md-12">
                <?php
                // Header
                if (LOGGED_IN_USER['type'] === "agency") {
                    // require_once "./components/customer-profile/header.php";
                }
                // Profile info
                // require_once "./components/customer-profile/profile-info.php";
                // tabs
                require_once "./components/customer-profile/tabs.php";
                // Overview
                require_once "./components/customer-profile/vehicles.php";
                // Invoice
                require_once "./components/customer-profile/invoices.php";
                // Proforma Invoice
                require_once "./components/customer-profile/proforma-invoices.php";
                // Email History
                require_once "./components/customer-profile/invoice-email-history.php";
                // Notes
                require_once "./components/customer-profile/notes.php";
                ?>
            </div>
        </div>
    </main>

    <!-- View Work Carried -->
    <div class="modal fade view-work-carried-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">View Work Carried</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <div id="carsInfoContainer"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Email Popup -->
    <div class="modal fade view-send-email-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 50%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Send Email</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <form action="customer" method="POST" class="ajax_form reset" data-reset="reset">
                        <div class="row mx-0">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="label">Template</div>
                                    <select name="email_template_id" class="form-control select2-list">
                                        <option value="">--- Select ----</option>
                                        <?php foreach ($email_template as $email_temp) { ?>
                                            <option value="<?= $email_temp['id'] ?>"><?= $email_temp['email_title'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="label">Message</div>
                                    <textarea class="customer-message form-control" id="customerMessage" rows="6" name="message" placeholder="Start typing to leave a note..."></textarea>
                                </div>
                                <div class="col-md-12 px-0 text-right">
                                    <input type="hidden" name="sendEmailToCustomer" value="true">
                                    <input type="hidden" name="customer_id" value="<?= $get_id ?>">
                                    <div class="form-group">
                                        <button class="btn br-5" type="submit">
                                            <i class="fas fa-save    "></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const _GET = <?= json_encode($_GET); ?>;
        const SITE_URL = '<?= SITE_URL ?>';
        const LOGIN_TYPE = '<?= LOGGED_IN_USER['type'] ?>';
    </script>
    <?php require_once('./includes/js.php'); ?>
</body>