<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [
    "add-vehicle.js",
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
], ["order_by" => "id desc"]);
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

        .custom-heading::after {
            width: 100%;
        }

        .custom-heading::before {
            left: 100%;
        }
    </style>

</head>

<body class="<?= isset($_GET['add_by']) ? "all-hide" : "" ?>">
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content reg-vehicle-container" id="mainContent">
        <?php if (!$custom_register) { ?>
            <div class="pull-away mt-4">
                <h3 class="heading text-clr">Add Registration Vehicle</h3>
                <a href="registration-vehicle" class="btn transparent-btn existing-vehicle-btn">Existing Vehicles</a>
            </div>
            <div class="card mt-3">
                <form action="mot-history" method="POST" class="ajax_form" data-callback="motHistoryCB">
                    <div class="row">
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
                        <div class="col-12 mt-2 text-right">
                            <input type="hidden" name="fetchRegistrationCar" value="<?= bc_code(); ?>">
                            <button class="btn getCustomerVehicle" type="submit">+ &nbsp; Add</button>
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
        // Initialize autocomplete **only after modal is shown**
        $('.add-new-customer-model').on('shown.bs.modal', function() {
            initAutocomplete();
        });
    </script>
    <script>
        const _GET = <?= json_encode($_GET); ?>;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>