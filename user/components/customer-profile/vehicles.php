 <div class="tab-content active" id="vehicles">
     <div class="vehicle-list-container">
         <?php
            $count = 1;
            foreach ($cars as $index => $car) { ?>
             <div class="pull-away single-vehicle mt-3" data-id="<?= $car['id'] ?>">
                 <span><?= $count ?> - <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> (<?= $car['reg_number'] ?>)</span>
                 <div class="buttons">
                     <form action="mot-history" method="POST" class="ajax_form d-block" data-callback="ViewProfileMotHistoryCB">
                         <input type="hidden" name="reg" value="<?= $car['reg_number'] ?>">
                         <input type="hidden" name="fetchRegistrationCar" value="true">
                         <button type="submit" class="btn view-mot-history-btn">
                             <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 8.63232 11.1083 11.2 8.20002 11.2C5.29171 11.2 2.01004 8.63232 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 3.268 11.1088 0.700012 8.20054 0.700012C5.29222 0.700012 2.01004 3.26737 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                 <path d="M10.4502 5.95001C10.4502 7.19265 9.44284 8.20001 8.2002 8.20001C6.95755 8.20001 5.9502 7.19265 5.9502 5.95001C5.9502 4.70737 6.95755 3.70001 8.2002 3.70001C9.44284 3.70001 10.4502 4.70737 10.4502 5.95001Z" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                             </svg>
                             <span class="ml-1">View MOT History</span>
                         </button>
                     </form>
                     <button class="btn view-work-carried-btn" data-vehicle-id="<?= $car['id'] ?>">
                         <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 8.63232 11.1083 11.2 8.20002 11.2C5.29171 11.2 2.01004 8.63232 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 3.268 11.1088 0.700012 8.20054 0.700012C5.29222 0.700012 2.01004 3.26737 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                             <path d="M10.4502 5.95001C10.4502 7.19265 9.44284 8.20001 8.2002 8.20001C6.95755 8.20001 5.9502 7.19265 5.9502 5.95001C5.9502 4.70737 6.95755 3.70001 8.2002 3.70001C9.44284 3.70001 10.4502 4.70737 10.4502 5.95001Z" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                         </svg>
                         <span class="ml-1">View Work Carried</span>
                     </button>
                     <button class="btn view-invoices-btn" data-vehicle-id="<?= $car['id'] ?>">
                         <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 8.63232 11.1083 11.2 8.20002 11.2C5.29171 11.2 2.01004 8.63232 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7002 5.95016C14.39 3.268 11.1088 0.700012 8.20054 0.700012C5.29222 0.700012 2.01004 3.26737 0.700195 5.94985" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                             <path d="M10.4502 5.95001C10.4502 7.19265 9.44284 8.20001 8.2002 8.20001C6.95755 8.20001 5.9502 7.19265 5.9502 5.95001C5.9502 4.70737 6.95755 3.70001 8.2002 3.70001C9.44284 3.70001 10.4502 4.70737 10.4502 5.95001Z" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                         </svg>
                         <span class="ml-1">Invoices</span>
                     </button>
                 </div>
             </div>
             <hr class="my-1">
         <?php $count++;
            } ?>
     </div>
     <div class="invoices-container d-none">
         <header class="custom-header">
             <div class="header-left">
                 <button class="back-btn" id="backButton" data-hide=".invoices-container" data-show=".vehicle-list-container">
                     <i class="fas fa-arrow-left"></i>
                 </button>
                 <h1><i class="fas fa-file-invoice-dollar"></i>&nbsp; Invoices</h1>
             </div>
         </header>
         <div id="viewInvoicesContainer"></div>
     </div>
 </div>