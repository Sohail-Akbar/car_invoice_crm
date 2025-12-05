 <form action="customer" method="POST" class="mt-4 ajax_form reset" data-reset="reset" <?= $callback ?>>
     <div class="form-group has-error has-danger">
         <div class="row m-0">
             <div class="col-md-12">
                 <div class="form-group">
                     <label class="label">Title:</label>
                     <select name="title" class="form-control" required="">
                         <option value="">Select Title</option>
                         <option value="Mr">Mr</option>
                         <option value="Mrs">Mrs</option>
                         <option value="Miss">Mrs</option>
                         <option value="Ms">Ms</option>
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
                         <option value="">Select Gender</option>
                         <option value="Male">Male</option>
                         <option value="Female">Female</option>
                     </select>
                 </div>
             </div>
             <div class="col-lg-12">
                 <div class="form-group">
                     <label class="control-label">Address</label>
                     <input type="text" class="form-control autocomplete" id="<?= isset($_add_customer) ? "add_customer_address" : "update_customer_address"  ?>" name="address" placeholder=" Enter Company Address" required>
                     <input type="hidden" id="<?= isset($_add_customer) ? "add_customer_lat" : "update_customer_lat"  ?>" name="lat">
                     <input type="hidden" id="<?= isset($_add_customer) ? "add_customer_lng" : "update_customer_lng"  ?>" name="lng">
                 </div>
             </div>
             <div class="col-md-6">
                 <div class="form-group">
                     <label class="label">Postcode</label>
                     <input type="text" id="<?= isset($_add_customer) ? "add_customer_postcode" : "update_customer_postcode"  ?>" class="form-control" placeholder="Postcode" name="postcode" aria-describedby="basic-addon1" id="postal_code" readonly required="" value="<?= arr_val($customer_data, "postcode", "") ?>">
                 </div>
             </div>
             <div class="col-md-6">
                 <div class="form-group">
                     <label class="label">City</label>
                     <input type="text" class="form-control" id="<?= isset($_add_customer) ? "add_customer_city" : "update_customer_city"  ?>" placeholder="City" id="locality" name="city" aria-describedby="basic-addon1" readonly required="" value="<?= arr_val($customer_data, "city", "") ?>">
                 </div>
             </div>
             <div class="col-md-12 mt-4">
                 <div class="form-group text-right mb-0">
                     <?php if (isset($_add_customer)) { ?>

                     <?php } else { ?>
                         <input type="hidden" name="id" value="">
                     <?php  } ?>
                     <input type="hidden" name="createCustomer" value="<?= bc_code(); ?>">
                     <button class="btn" type="submit"><i class="fas fa-save"></i> <?= isset($_add_customer) ? "Save" : "Update" ?> </button>
                 </div>
             </div>
         </div>
     </div>
 </form>