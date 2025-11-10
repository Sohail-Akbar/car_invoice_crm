<div class="tab-content" id="notes">
    <div class="pull-away mb-3">
        <span></span>
        <button class="btn btn-info" data-toggle="modal" data-target=".add-customer-notes-model">
            <i class="fas fa-notes-medical"></i> Add Notes
        </button>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-sticky-note"></i> Customer Notes</h5>
            <div class="d-flex gap-2">
                <input type="date" id="from_date" class="form-control form-control-sm mx-1">
                <input type="date" id="to_date" class="form-control form-control-sm mx-1">
                <button id="filterNotes" class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>

        <div class="card-body">
            <div id="notesContainer"></div>
            <div class="text-center mt-3">
                <button id="loadMoreNotes" class="btn btn-outline-secondary btn-sm">Load More</button>
            </div>
        </div>
    </div>

</div>