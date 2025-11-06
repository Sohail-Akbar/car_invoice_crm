<div class="card mt-3 d-none" id="registrationCarContainer">
    <div class="card-body">
        <div class="pull-away vehicle-information-header">
            <h3 class="heading">Registration Vehicle</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".mot-history-model">View MOT History</button>
        </div>
        <form action="mot-history" method="POST" class="ajax_form reset customer-vehicle-form" data-reset="reset">
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
                    <button class="btn btn-secondary" type="button" data-toggle="modal" data-target=".add-new-customer-model">Add New Customer</button>
                </div>
                <div id="motFields">
                    <div class="row m-0"></div>
                </div>
                <div class="col-12 mt-2">
                    <input type="hidden" name="fetchRegistrationCar" value="<?= bc_code(); ?>">
                    <input type="hidden" name="customerSave" value="<?= bc_code(); ?>">
                    <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>
        </form>
        <div class="already-register-details d-none">
        </div>
    </div>
</div>