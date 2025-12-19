<?php
require_once('includes/db.php');
$page_name = 'Add Company';

$CSS_FILES_ = [
    "wickedpicker.min.css",
];
$JS_FILES_ = [
    "wickedpicker.min.js",
];

$id = _get_param("id", "");

$company_admin_sql = "SELECT u.*, c.company_name, c.company_email, c.company_contact, c.company_address, c.id AS company_id, c.company_lat, c.company_lng, c.company_city, c.company_postcode
                        FROM users AS u
                        LEFT JOIN companies AS c ON u.company_id = c.id
                        WHERE (u.agency_id IS NULL OR u.agency_id = '') AND u.type = 'admin' AND u.id = '$id'";

$company_admin_data = $db->query($company_admin_sql, ["select_query" => true]);
if (count($company_admin_data)) $company_admin_data = $company_admin_data[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-company-container" id="mainContent">
        <div class="col-md-12 card">
            <div class="box-content p-2">
                <form action="company" method="POST" class="ajax_form">
                    <div class="col-md-12">
                        <h3 class="heading mb-5 custom-heading">Company's Details</h3>
                    </div>
                    <!-- Company Details -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="<?= arr_val($company_admin_data, "company_name", "") ?>" placeholder="Enter Company Name" required>
                        </div>
                    </div>

                    <!-- Company Address (Autocomplete) -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="label">Company Address</label>
                            <input type="text" class="form-control autocomplete" id="company_address" name="company_address" value="<?= arr_val($company_admin_data, "company_address", "") ?>" placeholder="Enter Company Address" required>
                            <input type="hidden" id="company_lat" name="company_lat" value="<?= arr_val($company_admin_data, "company_lat", "") ?>">
                            <input type="hidden" id="company_lng" name="company_lng" value="<?= arr_val($company_admin_data, "company_lng", "") ?>">
                            <input type="hidden" id="company_city" name="company_city" value="<?= arr_val($company_admin_data, "company_city", "") ?>">
                            <input type="hidden" id="company_postcode" name="company_postcode" value="<?= arr_val($company_admin_data, "company_postcode", "") ?>">
                            <div class="mt-0 bg-light rounded">
                                <small>
                                    <strong>Detected:</strong>
                                    <span id="display_company_city"></span>
                                    <span id="display_company_postcode" class="ml-2"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="label">Company Contact</label>
                            <input type="text" class="form-control" name="company_contact" value="<?= arr_val($company_admin_data, "company_contact", "") ?>" placeholder="Enter Company Contact" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="label">Company Email</label>
                            <input type="text" class="form-control" name="company_email" value="<?= arr_val($company_admin_data, "company_email", "") ?>" placeholder="Enter Company Email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="label">Company Logo</label>
                            <input type="file" name="company_logo" class="form-control">
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="col-lg-12 mt-4">
                        <h3 class="heading mb-5 custom-heading">User's Details</h3>
                    </div>

                    <div class="form-group row m-0">
                        <div class="col-md-6">
                            <label class="label">Title</label>
                            <select name="title" class="form-control" required>
                                <option value="" <?= arr_val($company_admin_data, "title", "") == "" ? "selected" : "" ?>>Select Title</option>
                                <option value="Mr" <?= arr_val($company_admin_data, "title", "") == "Mr" ? "selected" : "" ?>>Mr</option>
                                <option value="Mrs" <?= arr_val($company_admin_data, "title", "") == "Mrs" ? "selected" : "" ?>>Mrs</option>
                                <option value="Miss" <?= arr_val($company_admin_data, "title", "") == "Miss" ? "selected" : "" ?>>Miss</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="label">Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="" <?= arr_val($company_admin_data, "gender", "") == "" ? "selected" : "" ?>>Select Gender</option>
                                <option value="Male" <?= arr_val($company_admin_data, "gender", "") == "Male" ? "selected" : "" ?>>Male</option>
                                <option value="Female" <?= arr_val($company_admin_data, "gender", "") == "Female" ? "selected" : "" ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row m-0 mt-3">
                        <div class="col-md-6">
                            <label class="label">First Name</label>
                            <input type="text" name="first_name" value="<?= arr_val($company_admin_data, "fname", "") ?>" class="form-control" placeholder="Enter Your First Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="label">Last Name</label>
                            <input type="text" name="last_name" value="<?= arr_val($company_admin_data, "lname", "") ?>" class="form-control" placeholder="Enter Your Last Name" required>
                        </div>
                    </div>

                    <div class="form-group row m-0 mt-3">
                        <div class="col-md-6">
                            <label class="label">Contact</label>
                            <input type="text" name="contact" value="<?= arr_val($company_admin_data, "contact", "") ?>" class="form-control" placeholder="Enter Contact" required>
                        </div>
                        <div class="col-md-6">
                            <label class="label">Email</label>
                            <input type="email" name="email" value="<?= arr_val($company_admin_data, "email", "") ?>" class="form-control" placeholder="Email" required>
                        </div>
                    </div>

                    <!-- User Address (Autocomplete) -->
                    <div class="form-group mt-3">
                        <div class="col-md-12">
                            <label class="label">Address</label>
                            <input type="text" class="form-control autocomplete" id="user_address" name="address" value="<?= arr_val($company_admin_data, "address", "") ?>" placeholder="Enter Address" required>
                            <input type="hidden" id="user_lat" name="lat" value="<?= arr_val($company_admin_data, "lat", "") ?>">
                            <input type="hidden" id="user_lng" name="lng" value="<?= arr_val($company_admin_data, "lng", "") ?>">
                            <input type="hidden" id="user_city" name="city" value="<?= arr_val($company_admin_data, "city", "") ?>">
                            <input type="hidden" id="user_postcode" name="postcode" value="<?= arr_val($company_admin_data, "postcode", "") ?>">
                            <div class="mt-0 bg-light rounded">
                                <small>
                                    <strong>Detected:</strong>
                                    <span id="display_user_city"></span>
                                    <span id="display_user_postcode" class="ml-2"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="label">Password</label>
                            <input type="password" data-minlength="6" class="form-control" name="password" placeholder="Password" <?= arr_val($company_admin_data, "password", "") ? "" : 'required' ?>>
                            <div class="help-block">Minimum of 6 characters</div>
                        </div>
                    </div>

                    <!-- Hidden inputs and submit -->
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="hidden" name="addCompany" value="true">
                            <?php if (arr_val($company_admin_data, "id", "")) { ?>
                                <input type="hidden" name="user_id" value="<?= arr_val($company_admin_data, "id", "") ?>">
                                <input type="hidden" name="company_id" value="<?= arr_val($company_admin_data, "company_id", "") ?>">
                            <?php } ?>
                            <button type="submit" class="btn waves-effect waves-light"><i class="fas fa-save    "></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Google Places Autocomplete for both fields -->
    <script>
        var autocompletes = {};

        function initAutocomplete() {
            // Company Address
            autocompletes.company = new google.maps.places.Autocomplete(
                document.getElementById('company_address'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            autocompletes.company.addListener('place_changed', function() {
                fillInAddress('company');
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
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>