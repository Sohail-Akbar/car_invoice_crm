<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];

$id = isset($_GET['id']) ? $_GET['id'] : null;
$get_redirectTo = isset($_GET['redirectTo']) ? $_GET['redirectTo'] : null;


$customer_data = $db->select_one("users", "*", [
    "id" => $id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "customer"
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-customer-container" id="mainContent">
        <div class="card">
            <div class="row mx-0">
                <div class="col-md-12">
                    <h3 class="heading mb-4 custom-heading text-clr"><?= $id ? "Update" : "Add New"  ?> Customer</h3>
                </div>
            </div>
            <?php
            $callback = "";
            require_once('./components/customer-form.php'); ?>
        </div>
    </main>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?= require_once('./includes/js.php'); ?>
</body>

</html>