<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "register-vehicle.js",
    _DIR_ . "js/select2.min.js",
];
$CSS_FILES_ = [
    "register-vehicle.css",
    _DIR_ . "css/select2.min.css",
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

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content reg-vehicle-container" id="mainContent">
        <?php if (!$custom_register) { ?>
            <h3 class="heading text-clr mb-4">Search Registration Vehicle</h3>
            <div class="card">
                <form action="mot-history" method="POST" class="ajax_form" data-callback="motHistoryCB">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label text-clr">Search By</label>
                                <select name="search_by" class="form-control">
                                    <option value="">---- Select ----</option>
                                    <option value="registration_no">Registration Number</option>
                                    <option value="name">Customer Name</option>
                                    <option value="phone">Customer Phone</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 register_no_container d-none">
                            <div class="form-group">
                                <label class="label text-clr">Registration Number</label>
                                <input type="text" class="form-control" name="reg" data-length="[1,250]" placeholder="Reg No ...">
                            </div>
                        </div>
                        <div class="col-md-6 customer-info-container d-none">
                            <div class="form-group">
                                <label class="label text-clr">Customer Name | Phone</label>
                                <select class="form-select" name="customer_id" id="customerSelect">
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="hidden" name="fetchRegistrationCar" value="<?= bc_code(); ?>">
                            <button class="btn getCustomerVehicle" type="submit"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php require_once "./components/registeration-vehicle/vehicle-registeration-form.php"; ?>
        <?php } else { ?>
            <?php require_once "./components/registeration-vehicle/manual-registeration-vehicle.php"; ?>
        <?php } ?>
        </div>
    </main>

    <?php require_once "./components/registeration-vehicle/vehicle-history-modal.php"; ?>

    <!-- Add Customer -->
    <div class="modal fade add-new-customer-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Customer</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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

    <!-- Update Vehicle -->
    <?php require_once "./components/registeration-vehicle/update-vehicle-modal.php"; ?>

    <script>
        var autocomplete;

        function initAutocomplete() {
            var input = document.getElementById('autocomplete');
            if (!input) return;

            autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['address'],
                componentRestrictions: {
                    country: 'GB'
                }
            });

            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                alert('Please select a valid address from the dropdown');
                return;
            }

            $("#lat").val(place.geometry.location.lat());
            $("#lng").val(place.geometry.location.lng());

            var city = '';
            var postcode = '';

            $("#locality, #postal_code").val('');
            $("#display_city, #display_postcode").text('');

            place.address_components.forEach(function(component) {
                var type = component.types[0];

                if (type === 'locality' || type === 'postal_town') {
                    city = component.long_name;
                    $("#locality").val(city);
                }
                if (type === 'postal_code') {
                    postcode = component.long_name;
                    $("#postal_code").val(postcode);
                }
            });

            // Fallback for UK postal town
            if (!city) {
                place.address_components.forEach(function(component) {
                    if (component.types.includes('postal_town')) {
                        city = component.long_name;
                        $("#locality").val(city);
                    }
                });
            }
        }

        function geolocate() {
            if (navigator.geolocation && autocomplete) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }

        // Initialize autocomplete **only after modal is shown**
        $('.add-new-customer-model').on('shown.bs.modal', function() {
            initAutocomplete();
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>