<div class="tab-content" id="notes">
    <h3 class="section-title">Customer Notes</h3>
    <?php foreach ($invoice as $inv) { ?>
        <div class="notes-container mb-3">
            <?= $inv['notes'] ?>
        </div>
    <?php } ?>

    <!-- <h3 class="section-title" style="margin-top: 30px;">Add New Note</h3> -->
    <!-- <div style="background: #f8fafc; padding: 20px; border-radius: 8px;">
                    <textarea style="width: 100%; height: 120px; padding: 15px; border: 1px solid #e1e5eb; border-radius: 6px; resize: vertical;"></textarea>
                    <button class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-save"></i> Save Note
                    </button>
                </div> -->
</div>