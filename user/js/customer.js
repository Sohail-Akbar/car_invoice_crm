$(document).ready(function () {
    if ($("#customersTable").length) {
        let customerTable = $('#customersTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "controllers/customer?fetchCustomers=true",
                "type": "POST"
            },
            "pageLength": 10,
            "lengthChange": true,
            "columns": [
                { "data": "id", "render": function (data, type, row, meta) { return meta.row + 1; } },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `<strong>Name: </strong>${row.title} ${row.fname} ${row.lname}<br>
                                <strong>Email: </strong>${row.email}<br>
                                <strong>Contact: </strong>${row.contact}`;
                    }
                },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `${row.address}, ${row.postcode}, ${row.city}`;
                    }
                },
                {
                    "data": "is_active",
                    "render": function (data) {
                        let className = data == '1' ? 'bg-success' : 'bg-warning text-dark';
                        let statusText = data == '1' ? 'Active' : 'Inactive';
                        return `<span class="text-white p-1 bold small-font ${className}">${statusText}</span>`;
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "render": function (data, type, row) {
                        return `<div class="dropdown">
                                    <button class="btn dropdown-toggle action-table-btn" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i> Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="add-customer?id=${row.id}"><i class="fas fa-edit me-2 text-primary"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="customer-profile?id=${row.id}"><i class="fa fa-eye text-success" aria-hidden="true"></i>View Profile</a>
                                        </li> 
                                        <li>
                                            <a class="dropdown-item" href="invoice?customer_id=${row.id}"><i class="fas fa-file-alt text-success"></i>Generate Invoice</a>
                                        </li> 
                                        <li>
                                            <a class="dropdown-item" href="send-sms?customer_id=${row.id}"><i class="fas fa-envelope me-2 text-warning"></i>Send Message</a>
                                        </li>
                                        </ul>
                                        </div>`;
                    }
                }
            ],
            // <li>
            //     <button class="no-btn-styles dropdown-item text-danger cp tc-delete-btn" title="Delete" data-target="${row.id}" data-action="customer">
            //         <i class="fas fa-trash-alt"></i> Delete
            //      </button>
            // </li>
            "scrollX": true,
            "initComplete": function () { this.api().columns.adjust().draw(); },
            "drawCallback": function () { this.api().columns.adjust(); }
        });

        // 2️⃣ Bind custom search input
        $('.search-input').on('keyup', function () {
            customerTable.search(this.value).draw();
        });


        $('.dropdown-item').on('click', function () {
            var length = parseInt($(this).text());
            customerTable.page.len(length).draw();
        });
    }
});
