   <div class="tab-content" id="invoices">
       <h3 class="section-title">Invoice History</h3>
       <div class="table-container">
           <table>
               <thead>
                   <tr>
                       <th>Invoice #</th>
                       <th>Date</th>
                       <th>Due Date</th>
                       <th>Amount</th>
                       <th>Paid</th>
                       <th>Due</th>
                       <th>Status</th>
                       <th>Actions</th>
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

                            // Status color classes
                            $status_class = '';
                            if ($inv['status'] === 'paid') $status_class = 'status-paid';
                            elseif ($inv['status'] === 'pending') $status_class = 'status-pending';
                            elseif ($inv['status'] === 'overdue') $status_class = 'status-overdue';
                    ?>
                           <tr>
                               <td><?= htmlspecialchars($inv['invoice_no']) ?></td>
                               <td><?= date('M d, Y', strtotime($inv['invoice_date'])) ?></td>
                               <td><?= date('M d, Y', strtotime($inv['due_date'])) ?></td>
                               <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                               <td>$<?= number_format($inv['paid_amount'], 2) ?></td>
                               <td>$<?= number_format($inv['due_amount'], 2) ?></td>
                               <td><span class="status <?= $status_class ?>"><?= ucfirst($inv['status']) ?></span></td>
                               <td>
                                   <a class="btn text-white"
                                       href="<?= _DIR_ ?>/uploads/invoices/<?= htmlspecialchars($inv['pdf_file']) ?>"
                                       target="_blank"
                                       style="padding: 5px 10px; font-size: 12px;">
                                       <i class="fas fa-eye"></i> View
                                   </a>
                               </td>
                           </tr>
                   <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" style="text-align:center;">No invoices found</td></tr>';
                    }
                    ?>
               </tbody>

               <?php if (!empty($invoice)) { ?>
                   <tfoot>
                       <tr>
                           <th colspan="4" style="text-align:right;">Total Paid:</th>
                           <th colspan="4" style="text-align:left;">$<?= number_format($total_paid, 2) ?></th>
                       </tr>
                       <tr>
                           <th colspan="4" style="text-align:right;">Total Due:</th>
                           <th colspan="4" style="text-align:left;">$<?= number_format($total_due, 2) ?></th>
                       </tr>
                   </tfoot>
               <?php } ?>
           </table>

       </div>
   </div>