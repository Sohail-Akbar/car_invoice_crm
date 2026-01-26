<?php
require_once('includes/db.php');
$page_name = 'View Booking Lists';


$CSS_FILES_ = [
    "flatpickr.min.css",
    "calendar.css",
    _DIR_ .  "css/jquery.dataTables.min.css",
];
$JS_FILES_ = [
    "flatpickr.js",
    "moment.js",
    _DIR_ . "js/jquery.dataTables.min.js",
    _DIR_ . "js/bootstrap.bundle.min.js",
    "bookings-vehicle.js",
];

$customers = $db->select("users", "id,title,fname,lname,address,contact", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "is_active" => 1,
    "type" => "customer"
], ["order_by" => "id desc"]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content view-customer-container" id="mainContent">
        <div class="card">
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
                    <a href="booking" class="btn ml-3 add-customer-btn br-5">+ &nbsp;Add New Booking</a>
                </div>
            </div>
            <div class="table-responsive table-custom-design mt-5">
                <table class="table table-striped" id="bookingList" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Customer Details</th>
                            <th>Vehicle</th>
                            <th>Description</th>
                            <th>Time</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>


    <!-- Add Appointment -->
    <div class="modal fade add-new-appointment-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content bg-white">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Appointment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-white">
                    <!-- Add new Customer -->
                    <div class="add-new-customer-container d-none">
                        <div class="pull-away">
                            <button title="Go Back" class="content-center br-5 px-3 py-1 ml-3 transparent-btn back-to-appointment-btn">
                                <svg width="7" height="12" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292769 7.071L5.94977 12.728L7.36377 11.314L2.41377 6.364L7.36377 1.414L5.94977 -1.23616e-07L0.292769 5.657C0.105298 5.84453 -1.72225e-05 6.09883 -1.72457e-05 6.364C-1.72688e-05 6.62916 0.105298 6.88347 0.292769 7.071Z" fill="#214F79" />
                                </svg>
                                <span class="ml-2">Back</span>
                            </button>
                        </div>
                        <?php
                        $id = null;
                        $customer_data = [];
                        $callback = "data-callback='addCustomerCB'";
                        require_once('./components/customer-form.php'); ?>
                    </div>
                    <div class="add-new-vehicle-container d-none">
                        <div class="pull-away mb-2">
                            <button title="Go Back" class="content-center br-5 px-3 py-1 ml-3 transparent-btn back-to-appointment-btn">
                                <svg width="7" height="12" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292769 7.071L5.94977 12.728L7.36377 11.314L2.41377 6.364L7.36377 1.414L5.94977 -1.23616e-07L0.292769 5.657C0.105298 5.84453 -1.72225e-05 6.09883 -1.72457e-05 6.364C-1.72688e-05 6.62916 0.105298 6.88347 0.292769 7.071Z" fill="#214F79" />
                                </svg>
                                <span class="ml-2">Back</span>
                            </button>
                        </div>
                        <iframe id="addVehicleIframe" class="w-100" style="height:500px"></iframe>
                    </div>
                    <!-- Add new Appointment -->
                    <div class="add-new-appointment-container">
                        <form action="appointment" method="POST" class="ajax_form">
                            <div class="row mx-0">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label">Start Time</label>
                                        <input type="text" name="startTime" id="startTime" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label">End Time</label>
                                        <input type="text" name="endTime" id="endTime" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-select-list-container customer-selectbox-parent" data-callback="customerSelectCB">
                                            <label class="label fw-semibold">Customer</label>
                                            <div class="custom-select-wrapper">
                                                <div class="custom-select" id="custom-select" tabindex="0">
                                                    <span class="custom-select-placeholder">--- Select Customer ---</span>
                                                    <input type="hidden" class="selected_id" name="customer_id" value="">
                                                    <span class="custom-select-arrow"><i class="fas fa-chevron-down"></i></span>
                                                </div>
                                                <ul class="custom-select-dropdown">
                                                    <li class="search-container">
                                                        <input type="text" class="search-input" placeholder="Search customers...">
                                                    </li>

                                                    <!-- Customers are pre-rendered -->
                                                    <?php foreach ($customers as $customer): ?>
                                                        <li role="option" data-id="<?= $customer['id'] ?>" data-name="<?= htmlspecialchars($customer['title'] . ' ' . $customer['fname'] . ' ' . $customer['lname']) ?>">
                                                            <div class="customer-info">
                                                                <div class="customer-name"><?= htmlspecialchars($customer['title'] . ' ' . $customer['fname'] . ' ' . $customer['lname']) ?></div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>

                                                    <!-- Add new & No results -->
                                                    <li class="add-new-option" data-target="customer"><i class="fas fa-plus-circle me-1"></i> Add a
                                                        new customer</li>
                                                    <li class="no-results" style="display: none;">No customers found</li>
                                                </ul>
                                            </div>

                                            <div class="selected-info" id="selected-info">
                                                <div class="selected-label">Currently Selected:</div>
                                                <div class="selected-value" id="selected-value"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none" id="motHistoryDiv">
                                    <div class="custom-select-list-container vehicle-history-container">
                                        <label class="label fw-semibold">Vehicle (Reg No.)</label>
                                        <div class="custom-select-wrapper">
                                            <div class="custom-select" id="custom-select" tabindex="0">
                                                <span class="custom-select-placeholder">--- Select Vehicle History ---</span>
                                                <input type="hidden" class="selected_id" name="mot_id" value="">
                                                <span class="custom-select-arrow"><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                            <ul class="custom-select-dropdown">
                                                <li class="search-container">
                                                    <input type="text" class="search-input" placeholder="Search customers...">
                                                </li>

                                                <!-- Add new & No results -->
                                                <!-- data-popup=".add-new-vehicle-model" -->
                                                <li class="add-new-option" data-target="vehicle"><i class="fas fa-plus-circle me-1"></i> Add a
                                                    new vehicle</li>
                                                <li class="no-results" style="display: none;">No Vehicle History found</li>
                                            </ul>
                                        </div>

                                        <div class="selected-info" id="selected-info">
                                            <div class="selected-label">Currently Selected:</div>
                                            <div class="selected-value" id="selected-value"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="label">Notes</label>
                                        <textarea name="appointment_notes" id="appointment_notes" class="form-control" style="height: 120px;"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <input type="hidden" name="add_booking_appointment" value="<?= bc_code(); ?>">
                                    <button class="btn br-5" type="submit"><i class="fas fa-save"></i> Save</button>

                                </div>
                            </div>
                        </form>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    </script>
    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            if ($("#bookingList").length) {
                let bookingTable = $('#bookingList').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "controllers/appointment?fetchBookings=true",
                        "type": "POST"
                    },
                    "pageLength": 10,
                    "lengthChange": true,
                    "columns": [{ // Serial number
                            "data": null,
                            "render": function(data, type, row, meta) {
                                return meta.settings._iDisplayStart + meta.row + 1;
                            }
                        },
                        { // Customer Details
                            "data": null,
                            "render": function(data, type, row) {
                                return `<a href="customer-profile?id=${row.customer_id}">
                                <strong>Name:</strong> ${row.fname} ${row.lname}<br>
                                ${row.email ? `<strong>Email:</strong> ${row.email}<br>` : ""}
                                <strong>Contact:</strong> ${row.contact}<br>
                                <strong>Address:</strong> ${row.address}</a>`;
                            }
                        },
                        {
                            "data": "title"
                        },
                        {
                            "data": "description"
                        },
                        { // Time
                            "data": null,
                            "render": function(data, type, row) {
                                return `${moment(row.start_datetime).format('DD MMM YYYY hh:mm A')} - ${moment(row.end_datetime).format('DD MMM YYYY hh:mm A')}`;
                            }
                        },
                        { // Created At
                            "data": null,
                            "render": function(data, type, row) {
                                return moment(row.created_at).format('DD MMM YYYY hh:mm A');
                            }
                        },
                        { // Actions
                            "data": null,
                            "orderable": false,
                            "render": function(data, type, row) {
                                return `
                            <i class="fas fa-edit edit-appointment-btn" data-id="${row.id}"></i>
                            <i class="fas fa-trash delete-appointment-btn text-danger" data-id="${row.id}"></i>`;
                            }
                        }
                    ],
                    "scrollX": true,
                    "initComplete": function() {
                        this.api().columns.adjust().draw();
                    },
                    "drawCallback": function() {
                        this.api().columns.adjust();
                    }
                });

                // Search input
                $('.search-input').on('keyup', function() {
                    bookingTable.search(this.value).draw();
                });

                // Entries dropdown
                $('.dropdown-item').on('click', function() {
                    var length = parseInt($(this).text());
                    bookingTable.page.len(length).draw();
                });
            }
        });
    </script>
</body>

</html>