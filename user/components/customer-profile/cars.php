 <div class="tab-content" id="cars">
     <div class="tabs-container">
         <ul class="nav nav-tabs" id="carTabs">
             <?php foreach ($cars as $index => $car) { ?>
                 <li class="nav-item">
                     <a class="nav-link"
                         data-id="<?= $car['id'] ?>"
                         href="#">
                         <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>
                     </a>
                 </li>
             <?php } ?>
         </ul>
     </div>

     <div class="cars-info-container">

     </div>
 </div>