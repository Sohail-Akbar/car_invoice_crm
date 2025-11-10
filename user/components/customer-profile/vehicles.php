 <div class="tab-content active" id="vehicles">
     <div class="table-container">
         <table>
             <tbody>

                 <?php
                    $count = 1;
                    foreach ($cars as $index => $car) { ?>
                     <tr data-id="<?= $car['id'] ?>">
                         <td class="w-50"><?= $count ?> - <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></td>
                         <td>
                             <button class="btn btn-primary cp view-work-carried-btn" data-vehicle-id="<?= $car['id'] ?>">
                                 <i class="fa fa-eye" aria-hidden="true"></i> View Work Carried
                             </button>
                             <button class="btn btn-primary cp view-invoices-btn" data-vehicle-id="<?= $car['id'] ?>">
                                 <i class="fa fa-eye" aria-hidden="true"></i> Invoices
                             </button>
                         </td>
                     </tr>
                 <?php $count++;
                    } ?>
             </tbody>
         </table>
     </div>
     <div class="invoices-container d-none">
         <header class="custom-header">
             <div class="header-left">
                 <button class="back-btn" id="backButton" data-hide=".invoices-container" data-show=".tab-content .table-container">
                     <i class="fas fa-arrow-left"></i>
                 </button>
                 <h1><i class="fas fa-file-invoice-dollar"></i>&nbsp; Invoices</h1>
             </div>
         </header>
         <div id="viewInvoicesContainer"></div>
     </div>
 </div>