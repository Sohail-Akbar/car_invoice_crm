$(document).ready(function () {
    if ($("#vehicleTable").length) {
        let vehicleTable = $('#vehicleTable').DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "ajax": {
                "url": "controllers/mot-history?fetchVehicleData=true",
                "type": "POST"
            },
            "pageLength": 10,
            "lengthChange": true,
            "columns": [
                { "data": "index" },
                {
                    "data": "customer_details",
                    "render": function (data) {
                        return data;
                    }
                },
                { "data": "reg_number" },
                { "data": "details" },
                {
                    "data": "status",
                    "render": function (data) {
                        return data;
                    }
                },
                { "data": "id", "visible": false }
            ],
            "order": [[0, "desc"]],
        });
        // 2️⃣ Bind custom search input
        $('.search-input').on('keyup', function () {
            vehicleTable.search(this.value).draw();
        });


        $('.dropdown-item').on('click', function () {
            var length = parseInt($(this).text());
            vehicleTable.page.len(length).draw();
        });
    }
});
