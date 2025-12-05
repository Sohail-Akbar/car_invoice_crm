<div class="card mt-3 d-none" id="registrationCarContainer">
    <div class="vehicle-information-header text-right">
        <button type="button" class="btn br-5" data-toggle="modal" data-target=".mot-history-model"><i class="fa fa-eye" aria-hidden="true"></i> View Vehicle History</button>
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
                <!-- data-toggle="modal" data-target=".add-new-customer-model" -->
                <button class="btn btn-secondary add-new-customer-btn br-5" type="button">+ &nbsp; Add New Customer</button>
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
    <!-- d-none -->
    <div class="already-register-details d-none">
        <div class="compact-info-card">
            <div class="pull-away">
                <h3 class="heading custom-heading text-clr"><i class="fas fa-id-card"></i> Customer &amp; Vehicle Information</h3>
                <div class="header-buttons">
                    <button class="btn change-customer-btn toggle-edit content-center br-5" title="Change Customer" data-vehicle="{&quot;id&quot;:&quot;2&quot;,&quot;company_id&quot;:&quot;1&quot;,&quot;agency_id&quot;:&quot;1&quot;,&quot;customer_id&quot;:&quot;6&quot;,&quot;reg_number&quot;:&quot;AB12CDE&quot;,&quot;make&quot;:&quot;VAUXHALL&quot;,&quot;model&quot;:&quot;ASTRA&quot;,&quot;firstUsedDate&quot;:&quot;2017-06-19&quot;,&quot;fuelType&quot;:&quot;Petrol&quot;,&quot;primaryColour&quot;:&quot;White&quot;,&quot;registrationDate&quot;:&quot;2017-06-19&quot;,&quot;manufactureDate&quot;:&quot;2017-06-19&quot;,&quot;engineSize&quot;:&quot;1399&quot;,&quot;hasOutstandingRecall&quot;:&quot;Unavailable&quot;,&quot;expiryDate&quot;:&quot;2026-06-11&quot;,&quot;created_at&quot;:&quot;2025-11-11 11:59:16&quot;,&quot;is_active&quot;:&quot;1&quot;,&quot;is_manual&quot;:&quot;0&quot;}">
                        <i class="fas fa-exchange-alt"></i> Change Customer
                    </button>
                    <a href="invoice?customer_id=6&amp;vehicle_id=2" class="btn content-center br-5" title="Generate Invoice">
                        <i class="fas fa-file-alt"></i> Generate Invoice
                    </a>
                </div>
            </div>

            <div class="info-content">
                <!-- Customer Information Section -->
                <div class="customer-section">
                    <div class="section-title">
                        <i class="fas fa-user"></i> Customer Details &nbsp; <i class="fas fa-edit text-success cp edit-btn customer-edit-btn toggle-edit" data-customer="{&quot;id&quot;:&quot;6&quot;,&quot;company_id&quot;:&quot;1&quot;,&quot;agency_id&quot;:&quot;1&quot;,&quot;user_id&quot;:&quot;4&quot;,&quot;role_id&quot;:&quot;0&quot;,&quot;fname&quot;:&quot;Customer&quot;,&quot;lname&quot;:&quot;2&quot;,&quot;name&quot;:&quot;&quot;,&quot;gender&quot;:&quot;Male&quot;,&quot;title&quot;:&quot;Mr&quot;,&quot;email&quot;:&quot;customer1@gmail.com&quot;,&quot;type&quot;:&quot;customer&quot;,&quot;address&quot;:&quot;Lahore Road, Tidworth&quot;,&quot;contact&quot;:&quot;3523452345&quot;,&quot;city&quot;:&quot;Tidworth&quot;,&quot;lat&quot;:&quot;&quot;,&quot;lng&quot;:&quot;&quot;,&quot;postcode&quot;:&quot;SP9&quot;,&quot;image&quot;:&quot;avatar.png&quot;,&quot;password&quot;:&quot;$2y$10$SgowOzdUC8ZyVXCAOrMQ0OO7esBWyFRi5lSW9NMPOgtSV1SgoKaqC&quot;,&quot;is_admin&quot;:&quot;0&quot;,&quot;verify_status&quot;:&quot;1&quot;,&quot;verify_token&quot;:&quot;&quot;,&quot;password_forgot_token&quot;:&quot;&quot;,&quot;token_expiry_date&quot;:null,&quot;date_added&quot;:&quot;2025-11-11 10:37:52&quot;,&quot;uid&quot;:&quot;&quot;,&quot;is_active&quot;:&quot;1&quot;,&quot;twofa_code&quot;:null,&quot;twofa_expire&quot;:null,&quot;remember_token&quot;:null}"></i>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">Mr Customer 2</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Email Address</div>
                                <div class="info-value">customer1@gmail.com</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value">3523452345</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Address</div>
                                <div class="info-value">Lahore Road, Tidworth, Tidworth, SP9</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Vehicle Information Section -->
                <div class="vehicle-section">
                    <div class="section-title">
                        <i class="fas fa-car"></i> Vehicle Details &nbsp;
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-car-side"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Make &amp; Model</div>
                                <div class="info-value">VAUXHALL ASTRA</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Registration</div>
                                <div class="info-value">2017-06-19</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">First Used Date</div>
                                <div class="info-value">2017-06-19</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-gas-pump"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Fuel Type</div>
                                <div class="info-value">Petrol</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Color</div>
                                <div class="info-value">White</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Engine Size</div>
                                <div class="info-value">1399 cc</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Registration Expiry</div>
                                <div class="info-value highlight">2026-06-11</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="customer-vehicle-data">
    </div>
</div>