// Tab functionality
$(document).ready(function () {
    $('.tab').on('click', function () {
        var targetTab = $(this).data('tab');
        var activeFirstTab = $(this).data('callback');

        // Remove active class from all tabs and contents
        $('.tab').removeClass('active');
        $('.tab-content').removeClass('active');

        // Add active class to current tab and related content
        $(this).addClass('active');
        $('#' + targetTab).addClass('active');
        console.log(this);

        if (activeFirstTab) {
            $("#carTabs").find(".nav-item").first().find(".nav-link").click()
        }
    });
});


// On tab click
$(document).on('click', '#carTabs .nav-link', function (e) {
    e.preventDefault();
    $('#carTabs .nav-link').removeClass('active');
    $(this).addClass('active');

    let carId = $(this).data('id'),
        customerId = _GET['id']; // assuming _GET provides customer id

    let $container = $(".cars-info-container");
    $container.html('<div class="text-center py-4 text-muted">Loading...</div>');

    showVehicleDetails(carId, customerId, $container);
});


// Vehicle invoices
function showVehicleDetails(vehicle_id, customer_id, container) {
    let data = {
        car_id: vehicle_id,
        customer_id: customer_id,
        fetchCarInfo: true
    };

    $.ajax({
        url: "controllers/customer",
        method: "POST",
        data: data,
        success: function (res) {
            // show HTML directly in container
            container.html(res);
            $('.select2').select2({
                placeholder: "Select one or more options",
                allowClear: true
            });
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            container.html('<div class="alert alert-danger">Error loading car details.</div>');
        }
    });
}



// Add new customer callback
tc.fn.cb.assignedStaffCB = async (form, data) => {
    sAlert(data.data, data.status);
    if (data.status === 'success') {

        // form reset 
        form[0].reset();
        // popup close
        $('.modal').modal('hide');
        setTimeout(() => {
            console.log(data.invoice_id);
            showVehicleDetails(data.invoice_id, _GET.id, $("#viewInvoicesContainer"));
        }, 200);
    }
}

// View Work Carried 
$(document).on("click", ".view-work-carried-btn", function () {
    let car_id = $(this).data("vehicle-id");
    let $container = $("#carsInfoContainer");
    showVehicleDetails(car_id, _GET.id, $container);
    $(".view-work-carried-model").modal("show");
});

// View Invoices
$(document).on("click", ".view-invoices-btn", function () {
    let car_id = $(this).data("vehicle-id");
    let $container = $("#viewInvoicesContainer");
    showVehicleDetails(car_id, _GET.id, $container);
    $(this).parents(".table-container").addClass("d-none");
    $(".invoices-container").removeClass("d-none");
});

$(document).on("click", "#backButton", function () {
    let showCon = $(this).data("show");
    let hideCon = $(this).data("hide");
    $(showCon).removeClass("d-none");
    $(hideCon).addClass("d-none");
});



let offset = 0;
const limit = 10;

function loadNotes(reset = false) {
    if (reset) {
        offset = 0;
        $('#notesContainer').html('');
    }

    $.ajax({
        type: 'POST',
        url: 'controllers/customer',
        data: {
            fetchCustomerNotes: true,
            customer_id: _GET.id,
            offset: offset,
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val()
        },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success' && res.notes.length > 0) {
                res.notes.forEach(note => {
                    $('#notesContainer').append(`
                        <div class="notes-container mb-3 border rounded p-2">
                            <div class="text-muted small mb-1">
                                <span><i class="far fa-calendar-alt"></i> ${note.created_at}</span>
                                <i class="fas fa-trash text-danger cp tc-delete-btn" title="Delete" data-target="${note.id}" data-action="customer_notes"></i>
                            </div>
                            <div>${note.note}</div>
                        </div>
                    `);
                });
                offset += limit;
            } else {
                if (offset === 0) {
                    $('#notesContainer').html('<div class="text-center text-muted">No notes found.</div>');
                }
                $('#loadMoreNotes').hide();
            }
        }
    });
}

// Initial load
loadNotes();

// Load more button
$('#loadMoreNotes').on('click', function () {
    loadNotes();
});

// Filter notes
$('#filterNotes').on('click', function () {
    loadNotes(true);
});


$(document).ready(function () {
    if ($("#invoiceTable").length) {

        const customerId = _GET.id; // table attribute se id lo

        $('#invoiceTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "controllers/customer.php?fetchInvoiceData=true",
                "type": "POST",
                "data": function (d) {
                    d.customer_id = customerId; // 👈 send customer id to PHP
                }
            },
            "pageLength": 10,
            "lengthChange": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "autoWidth": false,
            "columns": [
                { "data": "invoice_no", "width": "30%" },
                { "data": "invoice_date", "width": "30%" },
                { "data": "due_date", "width": "30%" },
                {
                    "data": "total_amount",
                    "render": data => '$' + parseFloat(data).toFixed(2)
                },
                {
                    "data": "paid_amount",
                    "render": data => '$' + parseFloat(data).toFixed(2)
                },
                {
                    "data": "due_amount",
                    "render": data => '$' + parseFloat(data).toFixed(2)
                },
                {
                    "data": "status",
                    "render": function (data) {
                        let className = '', displayText = '';
                        switch (data) {
                            case 'paid': className = 'bg-success'; displayText = 'Paid'; break;
                            case 'unpaid': className = 'bg-danger'; displayText = 'Unpaid'; break;
                            case 'partial': className = 'bg-warning text-dark'; displayText = 'Partial'; break;
                            default: className = 'bg-secondary'; displayText = data;
                        }
                        return `<span class="badge ${className}">${displayText}</span>`;
                    }
                },
                {
                    "data": "pdf_file",
                    "render": function (data) {
                        if (!data) return '<span class="text-muted">No file</span>';
                        return `<a class="btn btn-view text-white" href="${SITE_URL}/uploads/invoices/${data}" target="_blank" style="padding:5px 10px;font-size:12px;">
                                    <i class="fas fa-eye"></i> View
                                </a>`;
                    },
                    "orderable": false
                }
            ],
            "scrollX": true,
            "initComplete": function () { this.api().columns.adjust().draw(); },
            "drawCallback": function () { this.api().columns.adjust(); }
        });
    }
});
