<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];

$id = isset($_GET['id']) ? $_GET['id'] : null;


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
    <div class="all-content">
        <div class="card">
            <div class="card-body">
                <h3 class="heading mb-4">Create Customer </h3>
                <?php
                $callback = "";
                require_once('./components/customer-form.php'); ?>
            </div>
        </div>
    </div>
    <script>
        var placeSearch, autocomplete;

        function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById('autocomplete'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            var place = autocomplete.getPlace();
            console.log('Place details:', place);

            if (!place.geometry) {
                alert('Please select a valid address from the dropdown');
                return;
            }

            // Coordinates
            $("#lat").val(place.geometry.location.lat());
            $("#lng").val(place.geometry.location.lng());

            // Reset previous values
            $("#locality").val('');
            $("#postal_code").val('');
            $("#display_city").text('');
            $("#display_postcode").text('');

            // Extract address components
            var city = '';
            var postcode = '';

            for (var i = 0; i < place.address_components.length; i++) {
                var component = place.address_components[i];
                var addressType = component.types[0];

                if (addressType === 'locality' || addressType === 'postal_town') {
                    city = component.long_name;
                    $("#locality").val(city);
                    $("#display_city").text('City: ' + city);
                }

                if (addressType === 'postal_code') {
                    postcode = component.long_name;
                    $("#postal_code").val(postcode);
                    $("#display_postcode").text('Postcode: ' + postcode);
                }
            }

            // Also check for UK specific postal town
            if (!city) {
                for (var i = 0; i < place.address_components.length; i++) {
                    var component = place.address_components[i];
                    if (component.types.includes('postal_town')) {
                        city = component.long_name;
                        $("#locality").val(city);
                        $("#display_city").text('City: ' + city);
                        break;
                    }
                }
            }
        }

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
                    if (autocomplete) {
                        autocomplete.setBounds(circle.getBounds());
                    }
                });
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDFeQ9V13F9lHKxCry0MmMQaRH32C8zIJY&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?= require_once('./includes/js.php'); ?>
</body>

</html>