<div class="modal fade update-vehicle-model" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%; box-shadow: 0 0 10px #5555;">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Update Vehicle Information</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-white">
                <form action="mot-history" method="POST" class="ajax_form reset" data-reset="reset" data-callback="vehicleInformationUpdateCB">
                    <div class="row mt-4">
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
                        <div class="col-12 mt-2 text-right">
                            <input type="hidden" name="updateVehicleInformation" value="<?= bc_code(); ?>">
                            <input type="hidden" name="id" value="">
                            <button class="btn" type="submit"><i class="fas fa-save"></i> Update</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>