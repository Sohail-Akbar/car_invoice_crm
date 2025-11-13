<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];

$id = isset($_GET['id']) ? $_GET['id'] : null;

$roles_data = $db->select("roles", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id']
]);

$staff_data = $db->select_one("users", "*", [
    "id" => $id,
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "staff"
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
                <h3 class="heading mb-4">Create staff </h3>
                <form action="staff" method="POST" class="mt-4 ajax_form reset" data-reset="reset">
                    <div class="form-group has-error has-danger">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="label">Title</label>
                                <select name="title" class="form-control" required="">
                                    <option <?= arr_val($staff_data, "title", "") == "" ? 'selected' : '' ?> value="">Select Title</option>
                                    <option <?= arr_val($staff_data, "title", "") == "Mr" ? 'selected' : '' ?> value="Mr">Mr</option>
                                    <option <?= arr_val($staff_data, "title", "") == "Mrs" ? 'selected' : '' ?> value="Mrs">Mrs</option>
                                    <option <?= arr_val($staff_data, "title", "") == "Miss" ? 'selected' : '' ?> value="Miss">Miss</option>
                                    <option <?= arr_val($staff_data, "title", "") == "Ms" ? 'selected' : '' ?> value="Ms">Ms</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="label">Gender</label>
                                <select name="gender" class="form-control" required="">
                                    <option <?= arr_val($staff_data, "gender", "") == "" ? 'selected' : '' ?> value="">Select Gender</option>
                                    <option <?= arr_val($staff_data, "gender", "") == "Male" ? 'selected' : '' ?> value="Male">Male</option>
                                    <option <?= arr_val($staff_data, "gender", "") == "Female" ? 'selected' : '' ?> value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="label">First Name</label>
                                <input type="text" name="fname" class="form-control" placeholder="Enter Your First Name" required="required" value="<?= arr_val($staff_data, "fname", "") ?>">
                            </div>
                            <div class="col-md-6">
                                <label class=" label">Last Name</label>
                                <input type="text" name="lname" class="form-control" placeholder="Enter Your Last Name" required="required" value="<?= arr_val($staff_data, "lname", "") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label for="inputEmail" class="label">Email</label>
                                <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email" required="" value="<?= arr_val($staff_data, "email", "") ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="label">Contact</label>
                                <input type="text" name="contact" class="form-control" placeholder="Enter contact" required="required" value="<?= arr_val($staff_data, "contact", "") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="label">Role</label>
                                <select name="role_id" class="form-control" required="">
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles_data as $role) { ?>
                                        <option <?= arr_val($staff_data, "role_id", "") == $role['id'] ? 'selected' : '' ?> value="<?= $role['id']; ?>"><?= $role['text']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class=" label">Address</label>
                                <input type="text" class="form-control pac-target-input" placeholder="Enter Address" onfocusout="checkBlackList(this)" name="address" autocomplete="off" aria-describedby="basic-addon1" id="autocomplete" onfocus="geolocate()" required="required" value="<?= arr_val($staff_data, "address", "") ?>">
                                <input type="hidden" id="lat" name="lat">
                                <input type="hidden" id="lng" name="lng">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="label">Postcode</label>
                                <input type="text" class="form-control" placeholder="Postcode" name="postcode" aria-describedby="basic-addon1" id="postal_code" readonly required="" value="<?= arr_val($staff_data, "postcode", "") ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="label">City</label>
                                <input type="text" class="form-control" placeholder="City" id="locality" name="city" aria-describedby="basic-addon1" readonly required="" value="<?= arr_val($staff_data, "city", "") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <div class="col-md-12">
                            <?php if ($id) { ?>
                                <input type="hidden" name="id" value="<?= $id; ?>">
                            <?php } ?>
                            <input type="hidden" name="createStaff" value="<?= bc_code(); ?>">
                            <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                        </div>
                    </div>
            </div>
            </form>
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