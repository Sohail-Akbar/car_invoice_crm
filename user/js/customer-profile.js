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
            // $('.select2').select2({
            //     placeholder: "Select one or more options",
            //     allowClear: true
            // });
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
    $(this).parents(".vehicle-list-container").addClass("d-none");
    $(".invoices-container").removeClass("d-none");
});

$(document).on("click", "#backButton", function () {
    let showCon = $(this).data("show");
    let hideCon = $(this).data("hide");
    $(showCon).removeClass("d-none");
    $(hideCon).addClass("d-none");
});


// Customer notes ----------------
let offset = 0;
const limit = 5;
let isLoading = false;
let hasMore = true;

function loadNotes(reset = false) {

    if (isLoading || !hasMore) return;

    isLoading = true;
    $('#notesLoader').removeClass('d-none');

    if (reset) {
        offset = 0;
        hasMore = true;
        $('#notesContainer').html('');
    }

    $.ajax({
        type: 'POST',
        url: 'controllers/customer',
        data: {
            fetchCustomerNotes: true,
            customer_id: _GET.id,
            offset: offset,
            limit: limit,
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
                                <i class="far fa-calendar-alt"></i> ${note.created_at}
                                <i class="fas fa-trash text-danger cp tc-delete-btn float-right"
                                   data-parent=".notes-container"
                                   data-target="${note.id}"
                                   data-action="customer_notes"></i>
                            </div>
                            <div>${note.note}</div>
                        </div>
                    `);
                });

                offset += limit;

                if (res.notes.length < limit) {
                    hasMore = false;
                }

            } else {
                hasMore = false;

                if (offset === 0) {
                    $('#notesContainer').html('<div class="text-center text-muted">No notes found.</div>');
                }
            }
        },
        complete: function () {
            isLoading = false;
            $('#notesLoader').addClass('d-none');
        }
    });
}

// ✅ Initial Load
loadNotes();

// ✅ Infinite Scroll (inside container)
$('#notesContainer').on('scroll', function () {

    let container = $(this);
    let scrollTop = container.scrollTop();
    let containerHeight = container.height();
    let scrollHeight = container[0].scrollHeight;

    if (scrollTop + containerHeight >= scrollHeight - 50) {
        loadNotes();
    }
});

// ✅ Reload on date filter change
$('#from_date, #to_date').on('change', function () {
    loadNotes(true);
});
// Customer notes ----------------


// Invoice
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
                                    <i class="fas fa-eye"></i> View Invoice
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

// Proforma (invoice)
$(document).ready(function () {
    if ($("#proformaInvoiceTable").length) {

        const customerId = _GET.id; // table attribute se id lo

        $('#proformaInvoiceTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "controllers/customer.php?fetchPorformaInvoiceData=true",
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
                { "data": "invoice_no" },
                { "data": "invoice_date" },
                { "data": "due_date" },
                {
                    "data": "total_amount",
                    "render": data => _CURRENCY_SYMBOL + parseFloat(data).toFixed(2)
                },
                {
                    "data": "paid_amount",
                    "render": data => _CURRENCY_SYMBOL + parseFloat(data).toFixed(2)
                },
                {
                    "data": "due_amount",
                    "render": data => _CURRENCY_SYMBOL + parseFloat(data).toFixed(2)
                },
                {
                    "data": "pdf_file",
                    "render": function (data, type, row) {
                        if (!data) return '<span class="text-muted">No file</span>';
                        let editBtn = '';
                        if (LOGIN_TYPE !== "customer") {
                            editBtn = `<a class="btn btn-view text-white" href="invoice?id=${row.id}&customer_id=${_GET.id}" style="padding:5px 10px;font-size:12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>`;
                        }
                        return `<a class="btn btn-view text-white mr-2" href="${SITE_URL}/uploads/invoices/${data}" target="_blank" style="padding:5px 10px;font-size:12px;">
                                    <i class="fas fa-eye"></i> View Invoice
                                </a>${editBtn}`;
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


// Customer Email History 
$(document).ready(function () {
    if ($("#invoicesEmailHistoryTable").length) {

        const customerId = _GET.id; // customer ID from URL

        $('#invoicesEmailHistoryTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "controllers/customer.php?fetchInvoiceEmailHistory=true",
                "type": "POST",
                "data": function (d) {
                    d.customer_id = customerId;
                }
            },
            "pageLength": 10,
            "lengthChange": true,
            "scrollY": "400px",
            "scrollCollapse": true,
            "autoWidth": false,
            "columns": [
                { "data": "id", "width": "10%" },
                { "data": "invoice_type", "width": "30%" },
                { "data": "created_at", "width": "30%" },
                {
                    "data": "pdf_file",
                    "render": function (data, type, row) {
                        if (!data) return '<span class="text-muted">No file</span>';
                        return `<a class="btn btn-view text-white" href="${SITE_URL}/uploads/invoices/${data}" target="_blank" style="padding:5px 10px;font-size:12px;">
                                    <i class="fas fa-eye"></i> View Invoice
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

// Add Customer note
$(document).on("click", ".customer-note-container .add-note", function () {
    $(".customer-note-tinymce").removeClass("d-none");
    $(".view-note").removeClass("d-none");
    $(".view-customer-note").addClass("d-none");
});
// View Customer note
$(document).on("click", ".customer-note-container .view-note", function () {
    $(".customer-note-tinymce").addClass("d-none");
    $(".view-customer-note").removeClass("d-none");
    $(".view-note").addClass("d-none");
});


$(document).ready(function () {
    tinymce.init({
        selector: '#customerNote',
        height: 200,
        menubar: false,
        plugins: ['lists link', 'textcolor'],
        toolbar: 'alignleft aligncenter alignright alignjustify | bold italic underline',
        font_formats: 'Serif=serif; Sans-serif=sans-serif; Arial=arial,helvetica,sans-serif; Courier New=courier,courier new,monospace;',
        content_style: "body { font-family: 'Serif', sans-serif; line-height:0.5; }",
        setup: function (editor) {
            const placeholderText = "Start typing to leave a note...";

            function setPlaceholder() {
                if (editor.getContent() === '') {
                    editor.setContent(`<p style="color:#888;">${placeholderText}</p>`);
                }
            }

            editor.on('init', setPlaceholder);
            editor.on('focus', function () {
                if (editor.getContent().includes(placeholderText)) {
                    editor.setContent('');
                }
            });
            editor.on('blur', setPlaceholder);
        }
    });

    if ($("#customerMessage").length) {
        tinymce.init({
            selector: '#customerMessage',
            height: 300, // thoda bada height better view ke liye
            menubar: true, // menubar enable
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount',
                'textcolor colorpicker'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic underline strikethrough | forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat | help',
            font_formats: 'Serif=serif; Sans-serif=sans-serif; Arial=arial,helvetica,sans-serif; Courier New=courier,courier new,monospace;',
            content_style: "body { font-family: 'Serif', sans-serif; line-height:0.5; }",
            setup: function (editor) {
                const placeholderText = "Start typing to leave a note...";

                function setPlaceholder() {
                    if (editor.getContent() === '') {
                        editor.setContent(`<p style="color:#888;">${placeholderText}</p>`);
                    }
                }

                editor.on('init', setPlaceholder);
                editor.on('focus', function () {
                    if (editor.getContent().includes(placeholderText)) {
                        editor.setContent('');
                    }
                });
                editor.on('blur', setPlaceholder);
            }
        });
    }
});

// jQuery form submit validation
$(document).on('click', ".save-customer-note", function (e) {
    let editorContent = tinymce.get('customerNote').getContent().trim();

    if (editorContent === '' || editorContent === '<p style="color: #888;">Start typing to leave a note...</p>') {
        e.preventDefault();
        sAlert('Note cannot be empty!', "warning");
        tinymce.get('customerNote').focus();
        return false;
    }

    $.ajax({
        url: "controllers/customer",
        method: "POST",
        data: {
            note: editorContent,
            customer_id: _GET.id,
            addCustomerNotes: true
        },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                sAlert(res.data, "success");
                tinymce.get('customerNote').setContent('');
                setTimeout(() => {
                    location.reload();
                }, 200);
            } else {
                sAlert(res.message, "error");
            }
        },
        error: makeError
    });
});

// get email template
$('select[name="email_template_id"]').on('select2:select', function (e) {
    let template_id = $(this).val();
    if (!template_id) return false;

    $.ajax({
        url: "controllers/customer",
        method: "POST",
        data: {
            template_id,
            getEmailTemplateBody: true
        },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                // Set HTML content in TinyMCE
                tinymce.get('customerMessage').setContent(res.data);
            } else {
                sAlert(data.data, "error");
            }
        },
        error: makeError
    });
});
