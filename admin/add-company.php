<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$CSS_FILES_ = [
    "wickedpicker.min.css"
];
$JS_FILES_ = [
    "wickedpicker.min.js",
];

$id = _get_param("id", "");

$company_admin_sql = "SELECT u.*, c.company_name, c.company_email, c.company_contact, c.company_address, c.id AS company_id
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
    <div class="all-content">
        <div class="col-md-12 px-0">
            <div class="box-content px-4 py-3">
                <h4 class="box-title text-style">Add Company</h4>
                <span class="bottom-text">Please Enter Company and user details</span>
            </div>
        </div>
        <div class="col-lg-12 mb-0 mt-4">
            <h2 class="box-title text-style"><b>Company's Details</b></h2>
        </div>
        <div class="col-xs-12 mt-3">
            <div class="box-content p-2">
                <form action="company" method="POST" class="mt-4 ajax_form reset" data-reset="reset">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputName" class="control-label">Company Name</label>
                            <input type="text" class="form-control" id="inputName" name="company_name" value="<?= arr_val($company_admin_data, "company_name", "") ?>" placeholder="Enter New Company" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="control-label">Company Address</label>
                            <input type="text" class="form-control" name="company_address" value="<?= arr_val($company_admin_data, "company_address", "") ?>" placeholder="Enter Company Address" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputPhone" class="control-label">Company Contact</label>
                            <input type="text" class="form-control" id="inputPhone" name="company_contact" value="<?= arr_val($company_admin_data, "company_contact", "") ?>" placeholder="Enter Company Contact" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputPhone" class="control-label">Company Email</label>
                            <input type="text" class="form-control" id="inputEmail" name="company_email" value="<?= arr_val($company_admin_data, "company_email", "") ?>" placeholder="Enter Company Email" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="control-label">Company Logo</label>
                            <input type="file" name="company_logo" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <h4 class="box-title text-style">User's Details</h4>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="control-label">Title</label>
                                <select name="title" class="form-control" required="">
                                    <option <?= arr_val($company_admin_data, "title", "") == "" ? "selected" : '' ?> value="">Select Title</option>
                                    <option <?= arr_val($company_admin_data, "title", "") == "Mr" ? "selected" : '' ?> value="Mr">Mr</option>
                                    <option <?= arr_val($company_admin_data, "title", "") == "Mrs" ? "selected" : '' ?> value="Mrs">Mrs</option>
                                    <option <?= arr_val($company_admin_data, "title", "") == "Miss" ? "selected" : '' ?> value="Miss">Miss</option>
                                </select>
                                <div class="clearfix"></div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Gender</label>
                                <select name="gender" class="form-control" required="">
                                    <option <?= arr_val($company_admin_data, "gender", "") == "" ? "selected" : '' ?> value="">Select Gender</option>
                                    <option <?= arr_val($company_admin_data, "gender", "") == "Male" ? "selected" : '' ?> value="Male">Male</option>
                                    <option <?= arr_val($company_admin_data, "gender", "") == "Female" ? "selected" : '' ?> value="Female">Female</option>
                                </select>
                                <div class="clearfix"></div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="control-label">First Name</label>
                                <input type="text" name="first_name" value="<?= arr_val($company_admin_data, "fname", "") ?>" class="form-control" placeholder="Enter Your First Name" required="required">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-md-6">
                                <label class=" control-label">Last Name</label>
                                <input type="text" name="last_name" value="<?= arr_val($company_admin_data, "lname", "") ?>" class="form-control" placeholder="Enter Your Last Name" required="required">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class=" control-label">Contact</label>
                                <input type="text" name="contact" value="<?= arr_val($company_admin_data, "contact", "") ?>" class="form-control" placeholder="Enter contact" required="required">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail" class="control-label">Email</label>
                                <input type="email" class="form-control" value="<?= arr_val($company_admin_data, "email", "") ?>" id="inputEmail" placeholder="Email" name="email" data-error="Whoops, that email address is invalid" required="">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <label class="control-label">Address</label>
                                <input type="text" class="form-control" name="address" value="<?= arr_val($company_admin_data, "address", "") ?>" placeholder="Enter Address" id="autocomplete" required>
                                <input type="hidden" value="<?= arr_val($company_admin_data, "city", "") ?>" id="locality" name="city">
                                <input type="hidden" value="<?= arr_val($company_admin_data, "lat", "") ?>" id="lat" name="lat">
                                <input type="hidden" value="<?= arr_val($company_admin_data, "lng", "") ?>" id="lng" name="lng">
                                <input type="hidden" value="<?= arr_val($company_admin_data, "postcode", "") ?>" id="postal_code" name="postcode">
                                <div class="mt-0 bg-light rounded">
                                    <small>
                                        <strong>Detected:</strong>
                                        <span id="display_city"></span>
                                        <span id="display_postcode" class="ml-2"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <label for="inputPassword" class="control-label">Password</label>
                                <input type="password" data-minlength="6" class="form-control" id="inputPassword" name="password" placeholder="Password" <?= arr_val($company_admin_data, "password", "") ? "" : 'required' ?>>
                                <div class="help-block">Minimum of 6 characters</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <input type="hidden" name="addCompany" value="true">
                                <?php if (arr_val($company_admin_data, "id", "")) { ?>
                                    <input type="hidden" name="user_id" value="<?= arr_val($company_admin_data, "id", "") ?>">
                                    <input type="hidden" name="company_id" value="<?= arr_val($company_admin_data, "company_id", "") ?>">
                                <?php  } ?>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.box-content -->
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
    <?php require_once('./includes/js.php'); ?>
</body>

</html>