<div class="tabs">
    <div class="tab active" data-tab="vehicles">
        <i class="fas fa-car"></i> Vehicles</i>
    </div>
    <div class="tab" data-tab="invoices">
        <i class="fas fa-file-invoice"></i> Invoices
    </div>
    <div class="tab" data-tab="proformaInvoices">
        <i class="fas fa-file-invoice"></i> Proforma
    </div>
    <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
        <div class="tab" data-tab="invoicesEmailHistory">
            <i class="fas fa-file-invoice"></i> Invoice Email History
        </div>
        <div class="tab" data-tab="notes">
            <i class="fas fa-sticky-note"></i> Notes
        </div>
    <?php  } ?>
</div>