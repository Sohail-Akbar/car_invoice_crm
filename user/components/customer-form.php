<form action="customer" method="POST" class="mt-4 ajax_form reset" data-reset="reset" <?= $callback ?>>
    <div class="form-group has-error has-danger">
        <div class="row m-0">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="label">Title:</label>
                    <select name="title" class="form-control" required="">
                        <option <?= arr_val($customer_data, "title", "") == "" ? 'selected' : '' ?> value="">Select Title</option>
                        <option <?= arr_val($customer_data, "title", "") == "Mr" ? 'selected' : '' ?> value="Mr">Mr</option>
                        <option <?= arr_val($customer_data, "title", "") == "Mrs" ? 'selected' : '' ?> value="Mrs">Mrs</option>
                        <option <?= arr_val($customer_data, "title", "") == "Miss" ? 'selected' : '' ?> value="Miss">Miss</option>
                        <option <?= arr_val($customer_data, "title", "") == "Ms" ? 'selected' : '' ?> value="Ms">Ms</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">First Name:</label>
                    <input type="text" name="fname" class="form-control" placeholder="Enter Your First Name" required="required" value="<?= arr_val($customer_data, "fname", "") ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class=" label">Last Name</label>
                    <input type="text" name="lname" class="form-control" placeholder="Enter Your Last Name" required="required" value="<?= arr_val($customer_data, "lname", "") ?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="inputEmail" class="label">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email" required="" value="<?= arr_val($customer_data, "email", "") ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Contact</label>
                    <input type="text" name="contact" class="form-control" placeholder="Enter contact" required="required" value="<?= arr_val($customer_data, "contact", "") ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Gender:</label>
                    <select name="gender" class="form-control" required="">
                        <option <?= arr_val($customer_data, "gender", "") == "" ? 'selected' : '' ?> value="">Select Gender</option>
                        <option <?= arr_val($customer_data, "gender", "") == "Male" ? 'selected' : '' ?> value="Male">Male</option>
                        <option <?= arr_val($customer_data, "gender", "") == "Female" ? 'selected' : '' ?> value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class=" label">Address</label>
                    <input type="text" class="form-control pac-target-input" placeholder="Enter Address" name="address" autocomplete="off" aria-describedby="basic-addon1" id="autocomplete" onfocus="geolocate()" required="required" value="<?= arr_val($customer_data, "address", "") ?>">
                    <input type="hidden" id="lat" name="lat">
                    <input type="hidden" id="lng" name="lng">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Postcode</label>
                    <input type="text" class="form-control" placeholder="Postcode" name="postcode" aria-describedby="basic-addon1" id="postal_code" readonly required="" value="<?= arr_val($customer_data, "postcode", "") ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">City</label>
                    <input type="text" class="form-control" placeholder="City" id="locality" name="city" aria-describedby="basic-addon1" readonly required="" value="<?= arr_val($customer_data, "city", "") ?>">
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="form-group text-right mb-0">
                    <?php if ($id) { ?>
                        <input type="hidden" name="id" value="<?= $id; ?>">
                    <?php } ?>
                    <?php if (isset($get_redirectTo)) { ?>
                        <input type="hidden" name="redirectTo" value="<?= $get_redirectTo; ?>">
                    <?php } ?>
                    <input type="hidden" name="createCustomer" value="<?= bc_code(); ?>">
                    <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>