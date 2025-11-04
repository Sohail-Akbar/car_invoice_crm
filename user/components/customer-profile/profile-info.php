 <div class="profile-card">
     <div class="profile-header">
         <div class="avatar">
             <i class="fas fa-user"></i>
         </div>
         <div class="profile-info">
             <h2><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></h2>
             <p><?= $customer['address'] ?></p>
             <p><i class="fas fa-envelope"></i> &nbsp;&nbsp;<?= $customer['email'] ?> &nbsp;&nbsp;| &nbsp;&nbsp;<i class="fas fa-phone"></i> &nbsp;&nbsp; <?= $customer['contact'] ?></p>
             <div>
                 <span class="badge badge-active">Active</span>
                 <span class="badge badge-premium">Premium Client</span>
             </div>
         </div>
     </div>
 </div>

 <div class="stats-container">
     <div class="stat-card">
         <i class="fas fa-file-invoice-dollar"></i>
         <h3><?= $total_invoice ?></h3>
         <p>Total Invoices</p>
     </div>
     <div class="stat-card">
         <i class="fas fa-hand-holding-usd"></i>
         <h3>$<?= $total_paid ?></h3>
         <p>Total Paid</p>
     </div>
     <div class="stat-card">
         <i class="fas fa-money-bill-wave"></i>
         <h3>$<?= $total_due ?></h3>
         <p>Total Due</p>
     </div>
 </div>