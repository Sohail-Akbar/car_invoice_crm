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
                        return `<a href="customer-profile?id=${row.id}"><strong>Name: </strong>${row.title} ${row.fname} ${row.lname}<br>
                                ${row.email ? `<strong>Email: </strong>${row.email}<br>` : ""}
                                <strong>Contact: </strong>${row.contact}</a>`;
                    }
                },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `${row.address}, ${row.postcode}, ${row.city}`;
                    }
                },
                // {
                //     "data": "is_active",
                //     "render": function (data) {
                //         let className = data == '1' ? 'bg-success' : 'bg-warning text-dark';
                //         let statusText = data == '1' ? 'Active' : 'Inactive';
                //         return `<span class="text-white p-1 bold small-font ${className}">${statusText}</span>`;
                //     }
                // },
                {
                    "data": null,
                    "orderable": false,
                    "render": function (data, type, row) {
                        return `<a href="invoice?customer_id=${row.id}" class="btn br-5 text-white">
                                    <i class="fas fa-file-alt"></i>
                            Generate Invoice
                        </a>`;
                    }
                }
            ],
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
