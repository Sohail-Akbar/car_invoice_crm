$(document).ready(function () {
    if ($("#customersTable").length) {
        $('#customersTable').DataTable({
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
                        return `<div class="align-center child-el-margin-x">
                                    <a class="no-btn-styles text-success cp" href="add-customer?id=${row.id}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="${row.id}" data-action="customer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <a href="customer-profile?id=${row.id}" class="text-success" title="View Customer Profile">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </div>`;
                    }
                }
            ],
            "scrollX": true,
            "initComplete": function () { this.api().columns.adjust().draw(); },
            "drawCallback": function () { this.api().columns.adjust(); }
        });
    }
});
