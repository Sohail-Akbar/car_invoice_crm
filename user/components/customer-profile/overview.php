 <div class="tab-content active" id="overview">
     <h3 class="section-title">Customer Information</h3>
     <div class="info-grid">
         <div class="info-item">
             <div class="info-label">Full Name</div>
             <div class="info-value"><?= $customer['title'] . " " . $customer['fname'] . " " . $customer['lname'] ?></div>
         </div>
         <div class="info-item">
             <div class="info-label">Company</div>
             <div class="info-value"><?= $company['company_name'] ?></div>
         </div>
         <div class="info-item">
             <div class="info-label">Email</div>
             <div class="info-value"><?= $customer['email'] ?></div>
         </div>
         <div class="info-item">
             <div class="info-label">Phone</div>
             <div class="info-value"><?= $customer['contact'] ?></div>
         </div>
         <div class="info-item">
             <div class="info-label">Address</div>
             <div class="info-value"><?= $customer['address'] ?></div>
         </div>
     </div>

     <h3 class="section-title d-none" style="margin-top: 30px;">Recent Invoices</h3>
     <div class="table-container d-none">
         <table>
             <thead>
                 <tr>
                     <th>Invoice #</th>
                     <th>Date</th>
                     <th>Amount</th>
                     <th>Status</th>
                 </tr>
             </thead>
             <tbody>
                 <?php
                    $total_paid = 0;
                    $total_due  = 0;

                    if (!empty($invoice)) {
                        foreach ($invoice as $inv) {
                            $total_paid += $inv['paid_amount'];
                            $total_due  += $inv['due_amount'];

                            $status_class = '';
                            if ($inv['status'] === 'paid') $status_class = 'status-paid';
                            elseif ($inv['status'] === 'pending') $status_class = 'status-pending';
                            elseif ($inv['status'] === 'overdue') $status_class = 'status-overdue';
                    ?>
                         <tr>
                             <td><?= $inv['invoice_no'] ?></td>
                             <td><?= date('M d, Y', strtotime($inv['invoice_date'])) ?></td>
                             <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                             <td><span class="status <?= $status_class ?>"><?= ucfirst($inv['status']) ?></span></td>
                         </tr>
                 <?php
                        }
                    } else {
                        echo '<tr><td colspan="4" style="text-align:center;">No invoices found</td></tr>';
                    }
                    ?>
             </tbody>

             <?php if (!empty($invoice)) { ?>
                 <tfoot>
                     <tr>
                         <th colspan="2" style="text-align:right;">Total Paid:</th>
                         <th colspan="2" style="text-align:left;">$<?= number_format($total_paid, 2) ?></th>
                     </tr>
                     <tr>
                         <th colspan="2" style="text-align:right;">Total Due:</th>
                         <th colspan="2" style="text-align:left;">$<?= number_format($total_due, 2) ?></th>
                     </tr>
                 </tfoot>
             <?php } ?>
         </table>
     </div>
 </div>