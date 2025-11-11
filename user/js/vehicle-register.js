$(document).ready(function () {
    if ($("#vehicleTable").length) {
        $('#vehicleTable').DataTable({
            "processing": true,
            "serverSide": true,
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
    }
});
