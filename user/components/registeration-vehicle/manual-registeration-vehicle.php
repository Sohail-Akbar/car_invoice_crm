<button title="Go Back" class="btn content-center br-5 transparent-btn d-block">
    <a href="add-vehicle<?= isset($_GET['add_by']) ? "?add_by=invoice" : "" ?>" class="text-clr">
        <svg width="7" height="12" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.292769 7.071L5.94977 12.728L7.36377 11.314L2.41377 6.364L7.36377 1.414L5.94977 -1.23616e-07L0.292769 5.657C0.105298 5.84453 -1.72225e-05 6.09883 -1.72457e-05 6.364C-1.72688e-05 6.62916 0.105298 6.88347 0.292769 7.071Z" fill="#214F79" />
        </svg>
        <span class="ml-2">Back</span>
    </a>
</button>
<div class="card mt-3" id="registrationCarContainer">
    <h3 class="heading mb-4 custom-heading text-clr">Manually Registration Vehicle</h3>
    <form action="mot-history" method="POST" class="ajax_form" data-callback="manuallyVehicleRegisterationCB">
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="form-group">
                    <span class="label">Existing Customer</span>
                    <select name="customer_id" id="customersContainer" class="form-control" required>
                        <option value="">Select Customer</option>
                        <?php foreach ($existing_customers as $customer) { ?>
                            <option value="<?= $customer['id'] ?>"><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></option>
                        <?php }; ?>
                    </select>
                </div>
                <!-- Add new customer button -->
            </div>
            <div class="col-md-4">
                <label for=""></label><br>
                <button class="btn btn-secondary add-new-customer-btn" type="button" data-toggle="modal" data-target=".add-new-customer-model">Add New Customer</button>
            </div>
            <div id="motFields">
                <div class="row m-0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="reg_number">Reg Number:</label>
                            <input type="text" class="form-control" id="reg_number" name="reg_number" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="make">make:</label>
                            <input type="text" class="form-control" id="make" name="make" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="model">model:</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="firstUsedDate">first Used Date:</label>
                            <input type="date" class="form-control" id="firstUsedDate" name="firstUsedDate" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="fuelType">fuel Type:</label>
                            <input type="text" class="form-control" id="fuelType" name="fuelType" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="primaryColour">primary Colour:</label>
                            <input type="text" class="form-control" id="primaryColour" name="primaryColour" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="registrationDate">registration Date:</label>
                            <input type="date" class="form-control" id="registrationDate" name="registrationDate"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="manufactureDate">manufacture Date:</label>
                            <input type="date" class="form-control" id="manufactureDate" name="manufactureDate" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="engineSize">engine Size:</label>
                            <input type="text" class="form-control" id="engineSize" name="engineSize" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="hasOutstandingRecall">has Outstanding Recall:</label>
                            <input type="text" class="form-control" id="hasOutstandingRecall" name="hasOutstandingRecall" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label" for="expiryDate">expiry Date:</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <input type="hidden" name="manuallyRegistrationCar" value="<?= bc_code(); ?>">
                <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>