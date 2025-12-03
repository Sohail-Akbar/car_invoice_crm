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

$agency_data_sql = "SELECT u.*, a.name as agency_name, a.contact as agency_contact, a.address as agency_address, a.city as branch_city, a.postcode as branch_postcode, a.lat as branch_lat, a.lng as branch_lng, a.agency_logo, a.email as agency_email, a.id as agency_id,
                        u.address as user_address
                        FROM users AS u
                        LEFT JOIN agencies AS a ON u.company_id = a.company_id AND u.agency_id = a.id
                        WHERE u.company_id = a.company_id AND u.agency_id = a.id AND u.user_id = '" . LOGGED_IN_USER_ID . "' AND u.id = '" . $id . "'";

$agency_data = $db->query($agency_data_sql, ["select_query" => true]);
if (count($agency_data)) $agency_data = $agency_data[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-agency-container" id="mainContent">
        <div class="col-md-12 card">
            <div class="box-content p-2">
                <form action="agency" method="POST" class="ajax_form">
                    <div class="col-lg-12">
                        <h3 class="heading mb-5 custom-heading">Branch's Details</h3>
                    </div>
                    <!-- Branch Name -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputName" class="control-label">Branch's Name</label>
                            <input type="text" class="form-control" id="inputName" name="agency_name" value="<?= arr_val($agency_data, "agency_name", "") ?>" placeholder="Enter New Company" required="">
                        </div>
                    </div>

                    <!-- Branch Address -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="control-label">Branch's Address</label>
                            <input type="text" class="form-control" name="agency_address" value="<?= arr_val($agency_data, "agency_address", "") ?>" placeholder="Enter Company Address" id="branch_autocomplete" required>
                            <input type="hidden" name="branch_city" id="branch_city" value="<?= arr_val($agency_data, "branch_city", "") ?>">
                            <input type="hidden" name="branch_postcode" id="branch_postcode" value="<?= arr_val($agency_data, "branch_postcode", "") ?>">
                            <input type="hidden" name="branch_lat" id="branch_lat" value="<?= arr_val($agency_data, "branch_lat", "") ?>">
                            <input type="hidden" name="branch_lng" id="branch_lng" value="<?= arr_val($agency_data, "branch_lng", "") ?>">
                            <div class="mt-0 bg-light rounded">
                                <small>
                                    <strong>Detected:</strong>
                                    <span id="branch_display_city"></span>
                                    <span id="branch_display_postcode" class="ml-2"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Contact -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputPhone" class="control-label">Branch's Contact</label>
                            <input type="text" class="form-control" id="inputPhone" name="agency_contact" value="<?= arr_val($agency_data, "agency_contact", "") ?>" placeholder="Enter Company Contact" required="">
                        </div>
                    </div>

                    <!-- Branch Email -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label for="inputEmail" class="control-label">Branch's Email</label>
                            <input type="text" class="form-control" id="inputEmail" name="agency_email" value="<?= arr_val($agency_data, "agency_email", "") ?>" placeholder="Enter Company Email" required="">
                        </div>
                    </div>

                    <!-- Branch Logo -->
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label class="control-label">Branch's Logo</label>
                            <input type="file" name="agency_logo" class="form-control">
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="col-lg-12">
                        <h4 class="box-title text-style my-5 custom-heading">User's Details</h4>
                    </div>

                    <!-- Title / Gender -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="control-label">Title</label>
                                <select name="title" class="form-control" required="">
                                    <option <?= arr_val($agency_data, "title", "") == "" ? "selected" : '' ?> value="">Select Title</option>
                                    <option <?= arr_val($agency_data, "title", "") == "Mr" ? "selected" : '' ?> value="Mr">Mr</option>
                                    <option <?= arr_val($agency_data, "title", "") == "Mrs" ? "selected" : '' ?> value="Mrs">Mrs</option>
                                    <option <?= arr_val($agency_data, "title", "") == "Miss" ? "selected" : '' ?> value="Miss">Miss</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Gender</label>
                                <select name="gender" class="form-control" required="">
                                    <option <?= arr_val($agency_data, "gender", "") == "" ? "selected" : '' ?> value="">Select Gender</option>
                                    <option <?= arr_val($agency_data, "gender", "") == "Male" ? "selected" : '' ?> value="Male">Male</option>
                                    <option <?= arr_val($agency_data, "gender", "") == "Female" ? "selected" : '' ?> value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- First / Last Name -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="control-label">First Name</label>
                                <input type="text" name="first_name" value="<?= arr_val($agency_data, "fname", "") ?>" class="form-control" placeholder="Enter Your First Name" required="required">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Last Name</label>
                                <input type="text" name="last_name" value="<?= arr_val($agency_data, "lname", "") ?>" class="form-control" placeholder="Enter Your Last Name" required="required">
                            </div>
                        </div>
                    </div>

                    <!-- Contact / Email -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-6">
                                <label class="control-label">Contact</label>
                                <input type="text" name="contact" value="<?= arr_val($agency_data, "contact", "") ?>" class="form-control" placeholder="Enter contact" required="required">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Email</label>
                                <input type="email" class="form-control" value="<?= arr_val($agency_data, "email", "") ?>" placeholder="Email" name="email" required="">
                            </div>
                        </div>
                    </div>

                    <!-- User Address -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <label class="control-label">Address</label>
                                <input type="text" class="form-control" name="address" value="<?= arr_val($agency_data, "user_address", "") ?>" placeholder="Enter Address" id="user_autocomplete" required>
                                <input type="hidden" name="city" id="user_city" value="<?= arr_val($agency_data, "city", "") ?>">
                                <input type="hidden" name="postcode" id="user_postcode" value="<?= arr_val($agency_data, "postcode", "") ?>">
                                <input type="hidden" name="lat" id="user_lat" value="<?= arr_val($agency_data, "lat", "") ?>">
                                <input type="hidden" name="lng" id="user_lng" value="<?= arr_val($agency_data, "lng", "") ?>">
                                <div class="mt-0 bg-light rounded">
                                    <small>
                                        <strong>Detected:</strong>
                                        <span id="user_display_city"></span>
                                        <span id="user_display_postcode" class="ml-2"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <label class="control-label">Password</label>
                                <input type="password" data-minlength="6" class="form-control" name="password" placeholder="Password" <?= arr_val($agency_data, "password", "") ? "" : 'required' ?>>
                                <div class="help-block">Minimum of 6 characters</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden IDs & Submit -->
                    <div class="form-group">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <input type="hidden" name="addAgency" value="true">
                                <?php if (arr_val($agency_data, "id", "")) { ?>
                                    <input type="hidden" name="agency_id" value="<?= arr_val($agency_data, "agency_id", "") ?>">
                                    <input type="hidden" name="agency_user_id" value="<?= arr_val($agency_data, "id", "") ?>">
                                <?php  } ?>
                                <input type="hidden" name="company_login_id" value="<?= LOGGED_IN_USER['company_id'] ?>">
                                <button type="submit" class="btn waves-effect waves-light"><i class="fas fa-save    "></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Google Maps Autocomplete -->
    <script>
        var branchAutocomplete, userAutocomplete;

        function initAutocomplete() {
            // Branch address
            branchAutocomplete = new google.maps.places.Autocomplete(
                document.getElementById('branch_autocomplete'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            branchAutocomplete.addListener('place_changed', function() {
                fillInAddress(branchAutocomplete, 'branch');
            });

            // User address
            userAutocomplete = new google.maps.places.Autocomplete(
                document.getElementById('user_autocomplete'), {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'GB'
                    }
                }
            );
            userAutocomplete.addListener('place_changed', function() {
                fillInAddress(userAutocomplete, 'user');
            });
        }

        function fillInAddress(autocompleteObj, prefix) {
            var place = autocompleteObj.getPlace();
            if (!place.geometry) {
                alert('Please select a valid address from the dropdown');
                return;
            }

            $("#" + prefix + "_lat").val(place.geometry.location.lat());
            $("#" + prefix + "_lng").val(place.geometry.location.lng());
            $("#" + prefix + "_city").val('');
            $("#" + prefix + "_postcode").val('');
            $("#" + prefix + "_display_city").text('');
            $("#" + prefix + "_display_postcode").text('');

            var city = '';
            var postcode = '';
            place.address_components.forEach(function(comp) {
                if (comp.types.includes('locality') || comp.types.includes('postal_town')) {
                    city = comp.long_name;
                }
                if (comp.types.includes('postal_code')) {
                    postcode = comp.long_name;
                }
            });
            if (!city) {
                place.address_components.forEach(function(comp) {
                    if (comp.types.includes('postal_town')) {
                        city = comp.long_name;
                    }
                });
            }
            $("#" + prefix + "_city").val(city);
            $("#" + prefix + "_display_city").text('City: ' + city);
            $("#" + prefix + "_postcode").val(postcode);
            $("#" + prefix + "_display_postcode").text('Postcode: ' + postcode);
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
                    if (branchAutocomplete) branchAutocomplete.setBounds(circle.getBounds());
                    if (userAutocomplete) userAutocomplete.setBounds(circle.getBounds());
                });
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&libraries=places&region=GB&callback=initAutocomplete" async defer></script>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>