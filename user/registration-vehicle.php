<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "register-vehicle.js",
    _DIR_ . "js/select2.min.js",
    _DIR_ . "js/jquery.dataTables.min.js",
    _DIR_ . "js/bootstrap.bundle.min.js",
];
$CSS_FILES_ = [
    "register-vehicle.css",
    _DIR_ . "css/select2.min.css",
    _DIR_ .  "css/jquery.dataTables.min.css",
];

$custom_register = _get_param("type", "");

$existing_customers = $db->select("users", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "customer",
    "is_active" => 1
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .mot-card {
            background-color: white;
            color: #333;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .fa-check-circle {
            color: #008000 !important;
        }

        .mot-status-header .test-date {
            background: #ccc;
            color: #333;
            font-size: 0.85rem;
            padding: 2px 10px;
            border-radius: 12px;
        }

        .mot-status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .mot-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .pac-container {
            z-index: 999999999 !important;
        }
    </style>

</head>

<body class="<?= isset($_GET['add_by']) ? "all-hide" : "" ?>">
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content reg-vehicle-container" id="mainContent">
        <?php if (!$custom_register) { ?>
            <?php if (!isset($_GET['search_by'])) { ?>
                <div class="pull-away mt-4">
                    <h3 class="heading text-clr">Search Registration Vehicle</h3>
                    <a href="add-vehicle" class="btn content-center br-5 transparent-btn">+ Add New Vehicle</a>
                </div>
                <div class="card mt-4">
                    <div class="buttons-grid">
                        <!-- Registration Button -->
                        <div class="btn-card">
                            <div class="btn-icon">
                                <svg width="33" height="34" viewBox="0 0 33 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.4299 16.6325C14.8157 17.2046 9.89347 21.9802 9.89347 21.9802L11.2075 23.2758C11.2075 23.2758 15.5808 19.2395 16.2376 18.6291C16.4001 18.4787 16.6751 18.1921 17.1836 18.1921H32.0987V16.1266H16.6651C16.2139 16.1272 15.7864 16.3044 15.4299 16.6325ZM24.2665 5.40552C26.0154 5.40552 26.9697 4.19454 26.9697 2.70223C26.9696 2.16772 26.8111 1.64525 26.514 1.20086C26.217 0.756474 25.7949 0.410136 25.301 0.205636C24.8072 0.00113562 24.2638 -0.0523437 23.7396 0.0519596C23.2153 0.156263 22.7338 0.413665 22.3559 0.791621C21.9779 1.16958 21.7205 1.65111 21.6162 2.17535C21.5119 2.69958 21.5654 3.24298 21.7699 3.73682C21.9744 4.23066 22.3207 4.65278 22.7651 4.9498C23.2095 5.24683 23.732 5.40542 24.2665 5.40552ZM20.2586 10.0507H21.1651L19.8369 14.8226H28.6971L27.3678 10.0507H28.2743L29.6531 14.8226H31.9951L30.1126 8.49056C29.8639 7.6161 28.7581 6.06701 26.8436 6.06701H21.6894C19.7759 6.06701 18.6711 7.6161 18.4203 8.49056L16.5405 14.8226H18.8825L20.2586 10.0507ZM12.5952 14.5723L10.2158 10.1359C10.2158 10.1359 10.0759 9.92449 9.81669 10.0502C9.58375 10.1627 9.68892 10.4251 9.68892 10.4251L10.4782 11.9316L10.9693 12.8702L10.403 12.6152C9.39971 12.1629 8.84654 11.97 8.62464 11.8706C8.5852 11.8522 8.39065 11.7454 8.2981 11.5419C8.03519 10.9541 6.91254 8.11248 6.66067 7.48412C6.44771 6.95251 5.92241 6.19742 4.84288 5.9303C3.61402 5.62637 2.37517 6.40144 2.08281 7.63451L0.40594 14.7022C0.256604 15.2722 0 16.8139 0 17.4638L0.0110424 31.2689C0.0194557 32.4336 0.860256 33.3728 2.02391 33.3675C3.186 33.3638 3.98157 32.4168 3.97789 31.2537L3.97526 19.5582L5.76361 12.0099C5.97236 12.5205 6.23107 13.1426 6.31573 13.2919C6.51712 13.6442 6.71588 13.8866 7.12182 14.0775C7.12182 14.0775 9.46229 15.0461 10.3683 15.4594C10.6271 15.5579 10.9106 15.5712 11.1774 15.4972C11.4443 15.4232 11.6805 15.2659 11.8517 15.0482L11.9768 14.7889L12.0199 14.8715C12.0199 14.8715 12.2865 15.3411 12.6804 15.3626C12.7934 15.0045 12.5957 14.5723 12.5957 14.5723H12.5952ZM8.97747 2.6791C8.97747 3.03413 8.90754 3.38569 8.77168 3.7137C8.63581 4.04171 8.43667 4.33975 8.18562 4.5908C7.93457 4.84184 7.63654 5.04098 7.30853 5.17685C6.98052 5.31272 6.62896 5.38265 6.27392 5.38265C5.91889 5.38265 5.56733 5.31272 5.23932 5.17685C4.91131 5.04098 4.61327 4.84184 4.36223 4.5908C4.11118 4.33975 3.91204 4.04171 3.77617 3.7137C3.64031 3.38569 3.57038 3.03413 3.57038 2.6791C3.58029 1.9686 3.86948 1.29056 4.37542 0.791636C4.88135 0.292706 5.56336 0.0129923 6.27392 0.0129923C6.98449 0.0129923 7.66649 0.292706 8.17243 0.791636C8.67836 1.29056 8.96756 1.9686 8.97747 2.6791Z" fill="white" />
                                    <path d="M10.1951 18.2573L15.4629 13.3545L14.7782 12.6199L9.5105 17.5232L10.1951 18.2573Z" fill="white" />
                                </svg>
                            </div>
                            <h3>Registration Number</h3>
                            <p>
                                Search vehicles quickly using the registration number.
                                Find detailed records and vehicle information instantly.
                            </p>
                            <a href="registration-vehicle?search_by=reg_no" class="redirect-btn register">
                                <i class="fas fa-external-link-alt"></i>
                                Go to Registration
                            </a>
                        </div>

                        <!-- Customer Name Button -->
                        <div class="btn-card">
                            <div class="btn-icon">
                                <svg width="32" height="37" viewBox="0 0 32 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M30.9235 26.6805C29.2654 24.5917 26.8987 23.0953 24.2133 22.4382L24.1296 22.4212L22.1949 25.4867C22.1936 25.8949 22.0203 26.286 21.7128 26.5746C21.4053 26.8632 20.9887 27.0259 20.5538 27.0271C19.6513 27.0271 18.9128 26.3339 18.0513 24.2944V24.2451C18.0513 23.7344 17.8352 23.2446 17.4505 22.8835C17.0658 22.5224 16.544 22.3195 16 22.3195C15.456 22.3195 14.9342 22.5224 14.5495 22.8835C14.1648 23.2446 13.9487 23.7344 13.9487 24.2451V24.2959V24.2929C13.0297 26.3339 12.2847 27.0256 11.3838 27.0256C10.949 27.0244 10.5323 26.8617 10.2248 26.5731C9.91738 26.2844 9.74407 25.8933 9.74277 25.4852L7.86872 22.4135C5.1647 23.0591 2.77621 24.5513 1.09785 26.6436L1.07487 26.6728C0.411328 27.6949 0.0410205 28.8609 0 30.0572V30.068C0.00820513 30.2991 0 30.5686 0 30.8382V33.9191C0 34.7362 0.345787 35.5198 0.961291 36.0976C1.57679 36.6754 2.4116 37 3.28205 37H28.7179C29.5884 37 30.4232 36.6754 31.0387 36.0976C31.6542 35.5198 32 34.7362 32 33.9191V30.8382C32 30.5702 31.9918 30.2991 32 30.068C31.9603 28.8575 31.5835 27.6779 30.9071 26.6467L30.9251 26.6775L30.9235 26.6805ZM7.38297 8.18748C7.38297 12.6856 10.3696 19.2941 15.9984 19.2941C21.5286 19.2941 24.6137 12.6856 24.6137 8.18748V8.08735C24.6137 7.0253 24.3909 5.97366 23.9579 4.99245C23.525 4.01125 22.8904 3.11971 22.0904 2.36873C21.2903 1.61775 20.3406 1.02204 19.2953 0.615613C18.2501 0.209185 17.1297 0 15.9984 0C14.867 0 13.7467 0.209185 12.7014 0.615613C11.6561 1.02204 10.7064 1.61775 9.90636 2.36873C9.10635 3.11971 8.47175 4.01125 8.03878 4.99245C7.60582 5.97366 7.38297 7.0253 7.38297 8.08735V8.1921V8.18748Z" fill="white" />
                                </svg>
                            </div>
                            <h3>Customer Name</h3>
                            <p>
                                Look up vehicles linked to any customer name.
                                View ownership details and related vehicle records.
                            </p>
                            <a href="registration-vehicle?search_by=name" class="redirect-btn customer">
                                <i class="fas fa-external-link-alt"></i>
                                Go to Customer Names
                            </a>
                        </div>

                        <!-- Customer Phone Button -->
                        <div class="btn-card">
                            <div class="btn-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.4161 8.58413L27.3836 1.61311L26.5613 0.797628C25.4986 -0.265876 23.6417 -0.265876 22.579 0.797628L19.6013 3.77769C19.0681 4.31132 18.7752 5.0197 18.7752 5.77129C18.7752 6.52289 19.0681 7.23126 19.5975 7.76301L20.4161 8.58413ZM7.78419 19.5874C6.72152 18.5239 4.86465 18.5239 3.80198 19.5874L0.824231 22.5675C0.292893 23.1011 0 23.8095 0 24.5611C0 25.3127 0.292893 26.0211 0.827986 26.5566L1.64846 27.3683L8.60091 20.4104L7.78419 19.5874ZM29.922 4.16101L29.2574 3.49585L22.2955 10.4631L23.5159 11.6826C23.6033 11.7698 23.6727 11.8735 23.72 11.9877C23.7674 12.1018 23.7917 12.2242 23.7917 12.3477C23.7917 12.4713 23.7674 12.5937 23.72 12.7078C23.6727 12.8219 23.6033 12.9256 23.5159 13.0129L13.0281 23.5089C12.8491 23.6793 12.6115 23.7743 12.3644 23.7743C12.1173 23.7743 11.8797 23.6793 11.7007 23.5089L10.4803 22.2875L3.52974 29.2586L4.19438 29.9237C4.68253 30.4123 6.53377 32 9.93207 32C12.9661 32 17.9228 30.6622 24.2951 24.2868C37.0115 11.5567 30.2243 4.46352 29.922 4.16101Z" fill="white" />
                                </svg>
                            </div>
                            <h3>Customer Phone</h3>
                            <p>
                                Find vehicles using the customer's phone number.
                                Access all associated vehicle and contact information.
                            </p>
                            <a href="registration-vehicle?search_by=phone" class="redirect-btn phone">
                                <i class="fas fa-external-link-alt"></i>
                                Go to Phone Management
                            </a>
                        </div>
                    </div>
                </div>
            <?php }  ?>
            <?php if (isset($_GET['search_by'])) { ?>
                <div class="pull-away my-3">
                    <h3 class="heading text-clr">Search Registration Vehicle</h3>
                    <div class="d-flex">
                        <a href="registration-vehicle" title="Go Back" class="btn content-center br-5 transparent-btn">
                            <svg width="7" height="12" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292769 7.071L5.94977 12.728L7.36377 11.314L2.41377 6.364L7.36377 1.414L5.94977 -1.23616e-07L0.292769 5.657C0.105298 5.84453 -1.72225e-05 6.09883 -1.72457e-05 6.364C-1.72688e-05 6.62916 0.105298 6.88347 0.292769 7.071Z" fill="#214F79" />
                            </svg>
                            <span class="ml-2">Back</span>
                        </a>
                        <a href="add-vehicle" class="btn content-center br-5 transparent-btn ml-2">+ Add New Vehicle</a>
                    </div>
                </div>
                <div class="card">
                    <form action="mot-history" method="POST" class="ajax_form" data-callback="motHistoryCB">
                        <div class="row">
                            <?php if ($_GET['search_by'] === "reg_no") { ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-group has-search-right">
                                            <a href="#" class="form-control-feedback">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </a>
                                            <input type="text" class="form-control" name="reg" data-length="[1,250]" placeholder="Registration No....">
                                        </div>
                                    </div>
                                </div>
                            <?php } else if ($_GET['search_by'] === "name" || $_GET['search_by'] === "phone") { ?>
                                <div class="col-md-12 customer-info-container">
                                    <div class="form-group">
                                        <label class="label text-clr">Customer <?= $_GET['search_by'] === "name" ? "Name" : "Phone" ?></label>
                                        <select class="form-select" name="customer_id" id="customerSelect">
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-12 mt-2 text-right">
                                <?php if ($_GET['search_by'] === "name" || $_GET['search_by'] === "phone" || $_GET['search_by'] === "reg_no") { ?>
                                    <input type="hidden" name="getCustomersVehicleData" value="<?= bc_code(); ?>">
                                <?php } else { ?>
                                    <input type="hidden" name="fetchRegistrationCar" value="<?= bc_code(); ?>">
                                <?php } ?>
                                <button class="btn getCustomerVehicle br-5" type="submit"><i class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card mt-3 d-none" id="registrationCarContainer">
                    <div class="customer-vehicle-data">
                        <div id="registrationCarContainer">
                            <div class="custom-table-header pull-away">
                                <div class="search-container">
                                    <input type="text" class="search-input search-minimal form-control" placeholder="Type to search...">
                                    <div class="search-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                                <div class="d-flex content-center">
                                    <div class="btn-group dropleft content-center br-5">
                                        <button type="button" class="btn dropdown-toggle table-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Entries
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 7H20M6.99994 12H16.9999M10.9999 17H12.9999" stroke="#454545" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">5</a>
                                            <a class="dropdown-item" href="#">25</a>
                                            <a class="dropdown-item" href="#">50</a>
                                            <a class="dropdown-item" href="#">100</a>
                                        </div>
                                    </div>
                                    <a href="add-vehicle" class="btn ml-3 add-customer-btn br-5">+ &nbsp; Add New Vehicle</a>
                                </div>
                            </div>
                            <div class="table-responsive table-custom-design mt-3 mb-4">
                                <table id="vehicleHistoryTable" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Customer Details</th>
                                            <th>Vehicle Details</th>
                                            <th>Vehicle</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <?php require_once "./components/registeration-vehicle/manual-registeration-vehicle.php"; ?>
        <?php } ?>
        </div>
    </main>

    <!-- Vehicle history model -->
    <?php require_once "./components/registeration-vehicle/vehicle-history-modal.php"; ?>

    <!-- Add Customer -->
    <div class="modal fade add-new-customer-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Customer</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <?php
                    $id = null;
                    $customer_data = [];
                    $callback = "data-callback='addCustomerCB'";
                    require_once('./components/customer-form.php'); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Update Customer model -->
    <div class="modal fade update-customer-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Update Customer</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <?php
                    $callback = "data-callback='updateCustomerData'";
                    ?>
                    <?php require "./components/update-customer-form.php"; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Vehicle -->
    <?php require_once "./components/registeration-vehicle/update-vehicle-modal.php"; ?>

    <!-- Change Customer -->
    <div class="modal fade change-vehicle-customer-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Change Customer</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <div class="existing-customer-container">
                        <form action="mot-history" method="POST" class="ajax_form reset" data-reset="reset">
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <span class="label">Existing Customer</span>
                                        <select name="customer_id" id="customersContainer" class="form-control" required="">
                                            <option value="">Select Customer</option>
                                            <?php foreach ($existing_customers as $customer) { ?>
                                                <option value="<?= $customer['id'] ?>"><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></option>
                                            <?php }; ?>
                                        </select>
                                    </div>
                                    <!-- Add new customer button -->
                                </div>
                                <div class="col-md-4">
                                    <label for=""></label><br>
                                    <!-- data-toggle="modal" data-target=".add-new-customer-model" -->
                                    <!-- add-new-customer-btn -->
                                    <button class="btn br-5 transparent-btn target-element-to-hide" data-show-target=".add-new-customer-container" data-hide-target=".existing-customer-container" type="button">+ &nbsp; Add New Customer</button>
                                </div>
                                <div id="motFields"></div>
                                <div class="col-12 mt-2">
                                    <input type="hidden" name="fetchRegistrationCar" value="0c215f194276000be6a6df6528067151">
                                    <input type="hidden" name="customerSave" value="0f20c77d6afb02422603acb0329b5a41">
                                    <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                                </div>
                            </div>
                            <input type="hidden" name="vehicle_id" value="">
                        </form>
                    </div>
                    <div class="add-new-customer-container d-none">
                        <div class="row mx-0">
                            <div class="col-md-12">
                                <button class="btn transparent-btn target-element-to-hide" data-show-target=".existing-customer-container" data-hide-target=".add-new-customer-container">
                                    <svg width="7" height="12" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292769 7.071L5.94977 12.728L7.36377 11.314L2.41377 6.364L7.36377 1.414L5.94977 -1.23616e-07L0.292769 5.657C0.105298 5.84453 -1.72225e-05 6.09883 -1.72457e-05 6.364C-1.72688e-05 6.62916 0.105298 6.88347 0.292769 7.071Z" fill="#214F79" />
                                    </svg>
                                    Back
                                </button>
                            </div>
                        </div>
                        <?php
                        $_add_customer = true;
                        $callback = "data-callback='addCustomerData'";
                        ?>
                        <?php require "./components/update-customer-form.php"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var autocompletes = {};

        function initAutocomplete() {
            // Update CUstomer
            autocompletes.update_customer = new google.maps.places.Autocomplete(
                document.getElementById('update_customer_address'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            autocompletes.update_customer.addListener('place_changed', function() {
                fillInAddress('update_customer');
            });
            // Add Customer
            autocompletes.add_customer = new google.maps.places.Autocomplete(
                document.getElementById('add_customer_address'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            autocompletes.add_customer.addListener('place_changed', function() {
                fillInAddress('add_customer');
            });

            // User Address
            autocompletes.user = new google.maps.places.Autocomplete(
                document.getElementById('user_address'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            autocompletes.user.addListener('place_changed', function() {
                fillInAddress('user');
            });
        }

        function fillInAddress(type) {
            console.log(autocompletes[type]);

            var place = autocompletes[type].getPlace();
            if (!place.geometry) {
                alert('Please select a valid address from the dropdown');
                return;
            }

            // Fill coordinates
            $('#' + type + '_lat').val(place.geometry.location.lat());
            $('#' + type + '_lng').val(place.geometry.location.lng());

            // Reset
            $('#' + type + '_city').val('');
            $('#' + type + '_postcode').val('');
            $('#display_' + type + '_city').text('');
            $('#display_' + type + '_postcode').text('');

            var city = '',
                postcode = '';
            place.address_components.forEach(function(component) {
                var addressType = component.types[0];
                if (addressType === 'locality' || addressType === 'postal_town') {
                    city = component.long_name;
                    $('#' + type + '_city').val(city);
                }
                if (addressType === 'postal_code') {
                    postcode = component.long_name;
                    $('#' + type + '_postcode').val(postcode);
                }
            });

            // UK fallback
            if (!city) {
                place.address_components.forEach(function(component) {
                    if (component.types.includes('postal_town')) {
                        city = component.long_name;
                        $('#' + type + '_city').val(city);
                    }
                });
            }

            // Display
            $('#display_' + type + '_city').text('City: ' + city);
            $('#display_' + type + '_postcode').text('Postcode: ' + postcode);
        }

        // Geolocate bias
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    Object.values(autocompletes).forEach(function(auto) {
                        auto.setBounds(circle.getBounds());
                    });
                });
            }
        }
    </script>
    <script>
        const _GET = <?= json_encode($_GET); ?>;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>