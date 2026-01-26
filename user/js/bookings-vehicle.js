
let businessHoursData = [];

/* ===========================
   FETCH GARAGE WORKING HOURS
=========================== */
function loadGarageTimetable(callback) {
    $.ajax({
        url: 'controllers/garage-setting',
        type: 'POST',
        data: { fetchGarageTimetable: true },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                businessHoursData = res.businessHours;
                callback();
            }
        }
    });
}

/* ===========================
   MAP DB → FULLCALENDAR
=========================== */
function mapBusinessHours(data) {
    return data.map(d => ({
        daysOfWeek: d.daysOfWeek,
        dow: d.daysOfWeek,
        start: d.startTime.slice(0, 5), // "08:00:00" -> "08:00"
        end: d.endTime.slice(0, 5)
    }));
}


function initCalendar() {
    $('#calendar').fullCalendar({
        themeSystem: 'bootstrap4',
        defaultView: 'agendaDay',
        defaultDate: moment(),
        editable: false,
        selectable: true,
        slotDuration: '00:30:00',
        snapDuration: '00:30:00',
        minTime: '00:00:00',
        maxTime: '24:00:00',
        allDaySlot: false,
        eventOverlap: false,
        slotEventOverlap: false,

        header: {
            left: 'title',
            center: 'month,agendaWeek,agendaDay',
            right: 'today prev,next'
        },

        /* 🔹 TIME BASED GARAGE CONTROL */
        businessHours: mapBusinessHours(businessHoursData),
        // selectConstraint: "businessHours",
        // eventConstraint: "businessHours",

        events: [],


        viewRender: function (view) {
            let startDate = view.start.format('YYYY-MM-DD');
            let endDate = view.end.format('YYYY-MM-DD');


            // 🔹 Fetch Appointments
            $.ajax({
                url: 'controllers/appointment',
                type: 'POST',
                data: {
                    start: startDate,
                    end: endDate,
                    fetchAppointments: true
                },
                dataType: 'json',
                success: function (response) {
                    $('#calendar').fullCalendar('removeEvents');
                    $('#calendar').fullCalendar('addEventSource', response);
                }
            });
        },
        select: function (start, end) {
            let startPicker = flatpickr("#startTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });
            let endPicker = flatpickr("#endTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });

            startPicker.setDate(moment(start).format('DD-MM-YYYY hh:mm A'));
            endPicker.setDate(moment(end).format('DD-MM-YYYY hh:mm A'));

            $('.add-new-appointment-model').modal("show");
        },
        eventClick: function (event) {
            $(".view-appointment-details-model").modal("show");
            $.ajax({
                url: "controllers/appointment",
                method: "POST",
                data: { appointment_id: event.id, fetchSingleAppointment: true },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        let a = res.appointment;
                        $("#app_title").text(a.title || 'No Title');
                        $("#app_desc").text(a.description || 'No Description');
                        $("#app_start").text(moment(a.start_datetime).format('DD MMM YYYY hh:mm A'));
                        $("#app_end").text(moment(a.end_datetime).format('DD MMM YYYY hh:mm A'));
                        $("#app_name").text(a.fname + ' ' + a.lname || 'No Name');
                        $("#app_email").text(a.email || 'No Email');
                        $("#app_contact").text(a.contact || 'No Contact');
                        $(".edit-appointment-btn").attr("data-id", a.id);
                        $(".delete-appointment-btn").attr("data-id", a.id);
                        $("#generateInvoiceBtn").data("id", a.id);
                        $("#generateInvoiceBtn").attr("href", "invoice?customer_id=" + a.customer_id + "&appointment_id=" + a.id + "&vehicle_id=" + a.vehicle_id);
                    }
                },
                error: makeError
            });
        },

        // 🔹 Highlight days based on timetable
        dayRender: function (date, cell) {
            let today = moment().local().startOf('day');
            let cellDate = moment(date).local().startOf('day');

            // Highlight today
            if (cellDate.isSame(today, 'day')) {
                cell.css('background-color', '#fff3cd'); // light yellow
            }

            // Determine day of week 0 = Sunday, 1 = Monday ...
            let dayOfWeek = cellDate.day();


            // Check if this day is in businessHoursData
            let isWorkingDay = businessHoursData.some(bh => bh.daysOfWeek.includes(dayOfWeek));

            if (isWorkingDay) {
                cell.css('background-color', '#fff'); // light green for working day
            } else {
                if (businessHoursData.length) {
                    cell.css('background-color', '#f8d7da'); // light red for off day
                } else {
                    cell.css('background-color', '#fff');
                }
            }

        },
        eventAfterRender: function (event, element, view) {
            if (view.name === 'month') {
                // event.start se day nikaal lo
                let day = event.start.date();    // day number
                let month = event.start.month(); // 0-11
                let year = event.start.year();

                // Calendar me day cell select karo
                $('#calendar .fc-day[data-date]').each(function () {
                    let cellDate = $(this).data('date'); // "YYYY-MM-DD"
                    let d = moment(cellDate, 'YYYY-MM-DD');
                    if (d.year() === year && d.month() === month && d.date() === day) {
                        $(this).css({
                            'background-color': '#cce5ff',
                            "border": "1px solid #fff"
                        }); // blue light
                    }
                });
            }
        }

    });

    $('.fc-right button span.fa').each(function () {
        var $span = $(this);
        var $i = $('<i />').attr('class', $span.attr('class')).html($span.html());
        $span.replaceWith($i);
    });
}
/* ===========================
   LOAD & START
=========================== */
$(document).ready(function () {
    loadGarageTimetable(function () {
        initCalendar();
    });
});

// function initCalendar() {
//     $('#calendar').fullCalendar({
//         themeSystem: 'bootstrap4',
//         defaultView: 'agendaDay',
//         defaultDate: moment(),
//         editable: false,
//         selectable: true,
//         slotDuration: '00:30:00',
//         snapDuration: '00:30:00',
//         minTime: '00:00:00',
//         maxTime: '24:00:00',
//         allDaySlot: false,
//         header: {
//             left: 'title',
//             center: 'month,agendaWeek,agendaDay',
//             right: 'today prev,next'
//         },
//         events: [],
//         viewRender: function (view) {
//             let startDate = view.start.format('YYYY-MM-DD');
//             let endDate = view.end.format('YYYY-MM-DD');


//             // 🔹 Fetch Appointments
//             $.ajax({
//                 url: 'controllers/appointment',
//                 type: 'POST',
//                 data: {
//                     start: startDate,
//                     end: endDate,
//                     fetchAppointments: true
//                 },
//                 dataType: 'json',
//                 success: function (response) {
//                     $('#calendar').fullCalendar('removeEvents');
//                     $('#calendar').fullCalendar('addEventSource', response);
//                 }
//             });
//         },
//         select: function (start, end) {
//             let startPicker = flatpickr("#startTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });
//             let endPicker = flatpickr("#endTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });

//             startPicker.setDate(moment(start).format('DD-MM-YYYY hh:mm A'));
//             endPicker.setDate(moment(end).format('DD-MM-YYYY hh:mm A'));

//             $('.add-new-appointment-model').modal("show");
//         },
//         eventClick: function (event) {
//             $(".view-appointment-details-model").modal("show");
//             $.ajax({
//                 url: "controllers/appointment",
//                 method: "POST",
//                 data: { appointment_id: event.id, fetchSingleAppointment: true },
//                 dataType: "json",
//                 success: function (res) {
//                     if (res.status === "success") {
//                         let a = res.appointment;
//                         $("#app_title").text(a.title || 'No Title');
//                         $("#app_desc").text(a.description || 'No Description');
//                         $("#app_start").text(moment(a.start_datetime).format('DD MMM YYYY hh:mm A'));
//                         $("#app_end").text(moment(a.end_datetime).format('DD MMM YYYY hh:mm A'));
//                         $("#app_name").text(a.fname + ' ' + a.lname || 'No Name');
//                         $("#app_email").text(a.email || 'No Email');
//                         $("#app_contact").text(a.contact || 'No Contact');
//                         $(".edit-appointment-btn").attr("data-id", a.id);
//                         $(".delete-appointment-btn").attr("data-id", a.id);
//                         $("#generateInvoiceBtn").data("id", a.id);
//                         $("#generateInvoiceBtn").attr("href", "invoice?customer_id=" + a.customer_id + "&appointment_id=" + a.id + "&vehicle_id=" + a.vehicle_id);
//                     }
//                 },
//                 error: makeError
//             });
//         },

//         // 🔹 Highlight days based on timetable
//         dayRender: function (date, cell) {
//             let today = moment().local().startOf('day');
//             let cellDate = moment(date).local().startOf('day');

//             // Highlight today
//             if (cellDate.isSame(today, 'day')) {
//                 cell.css('background-color', '#fff3cd'); // light yellow
//             }

//             // Determine day of week 0 = Sunday, 1 = Monday ...
//             let dayOfWeek = cellDate.day();


//             $.ajax({
//                 url: 'controllers/garage-setting',
//                 type: 'POST',
//                 data: { fetchGarageTimetable: true },
//                 dataType: 'json',
//                 success: function (res) {
//                     if (res.status === 'success') {
//                         businessHoursData = res.businessHours; // store for later use

//                         // Check if this day is in businessHoursData
//                         let isWorkingDay = businessHoursData.some(bh => bh.daysOfWeek.includes(dayOfWeek));

//                         if (isWorkingDay) {
//                             cell.css('background-color', '#fff'); // light green for working day
//                         } else {
//                             cell.css('background-color', '#f8d7da'); // light red for off day
//                         }
//                     }
//                 }
//             });
//         }
//     });

//     $('.fc-right button span.fa').each(function () {
//         var $span = $(this);
//         var $i = $('<i />').attr('class', $span.attr('class')).html($span.html());
//         $span.replaceWith($i);
//     });
// }

// $('#calendar').fullCalendar({
//     themeSystem: 'bootstrap4',

//     defaultView: 'agendaDay',
//     defaultDate: moment(),

//     editable: false,
//     selectable: true,

//     slotDuration: '00:30:00',
//     snapDuration: '00:30:00',
//     minTime: '00:00:00',
//     maxTime: '24:00:00',

//     allDaySlot: false,

//     header: {
//         left: 'title',
//         center: 'month,agendaWeek,agendaDay',
//         right: 'today prev,next'
//     },

//     events: [],

//     // 🔥 THIS CONTROLS NEXT / PREV / TODAY
//     viewRender: function (view) {

//         let startDate = view.start.format('YYYY-MM-DD');
//         let endDate = view.end.format('YYYY-MM-DD');

//         console.log('View Changed');
//         console.log(startDate, endDate);

//         // 🔹 AJAX CALL
//         $.ajax({
//             url: 'controllers/appointment',
//             type: 'POST',
//             data: {
//                 start: startDate,
//                 end: endDate,
//                 fetchAppointments: true
//             },
//             dataType: 'json',
//             success: function (response) {

//                 // Remove old events
//                 $('#calendar').fullCalendar('removeEvents');

//                 // Add new events
//                 $('#calendar').fullCalendar('addEventSource', response);
//             }
//         });
//     },

//     select: function (start, end) {
//         // Initialize Flatpickr
//         let startPicker = flatpickr("#startTime", {
//             enableTime: true,
//             dateFormat: "d-m-Y h:i K", // dd-mm-yyyy hh:mm AM/PM
//             time_24hr: false
//         });

//         let endPicker = flatpickr("#endTime", {
//             enableTime: true,
//             dateFormat: "d-m-Y h:i K",
//             time_24hr: false
//         });

//         // Set values dynamically
//         startPicker.setDate(moment(start).format('DD-MM-YYYY hh:mm A'));
//         endPicker.setDate(moment(end).format('DD-MM-YYYY hh:mm A'));

//         $('.add-new-appointment-model').modal("show");
//     },

//     eventClick: function (event) {
//         $(".view-appointment-details-model").modal("show");

//         $.ajax({
//             url: "controllers/appointment",
//             method: "POST",
//             data: {
//                 appointment_id: event.id,
//                 fetchSingleAppointment: true
//             },
//             dataType: "json",
//             success: function (res) {

//                 if (res.status === "success") {

//                     let a = res.appointment;

//                     // Appointment Info
//                     $("#app_title").text(a.title || 'No Title');
//                     $("#app_desc").text(a.description || 'No Description');
//                     $("#app_start").text(moment(a.start_datetime).format('DD MMM YYYY hh:mm A'));
//                     $("#app_end").text(moment(a.end_datetime).format('DD MMM YYYY hh:mm A'));

//                     // Customer Info
//                     $("#app_name").text(a.fname + ' ' + a.lname || 'No Name');
//                     $("#app_email").text(a.email || 'No Email');
//                     $("#app_contact").text(a.contact || 'No Contact');
//                     // actions
//                     $(".edit-appointment-btn").attr("data-id", a.id);
//                     $(".delete-appointment-btn").attr("data-id", a.id);

//                     // Store appointment id for invoice
//                     $("#generateInvoiceBtn").data("id", a.id);
//                     $("#generateInvoiceBtn").attr("href", "invoice?customer_id=" + a.customer_id + "&appointment_id=" + a.id + "&vehicle_id=" + a.vehicle_id);
//                 }
//             },
//             error: makeError
//         });
//     },

//     // 🔹 Highlight Today
//     dayRender: function (date, cell) {
//         // Use local date for comparison
//         let today = moment().local().startOf('day');

//         // FullCalendar's date may be UTC or with time zone offset
//         let cellDate = moment(date).local().startOf('day');

//         if (cellDate.isSame(today, 'day')) {
//             cell.css('background-color', '#fff3cd'); // light yellow
//         }
//     },
// });




// delete appointment
$(document).on("click", ".delete-appointment-btn", function () {
    let appointmentId = $(this).data("id");
    if (appointmentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "controllers/appointment",
                    method: "POST",
                    data: {
                        appointment_id: appointmentId,
                        deleteAppointment: true
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            Swal.fire('Deleted!', 'Appointment deleted successfully.', 'success');
                            $('#calendar').fullCalendar('refetchEvents');
                            location.reload();
                        } else {
                            Swal.fire('Error!', res.data, 'error');
                        }
                    },
                    error: makeError
                });
            }
        });
    }
});

// edit appointment
$(document).on("click", ".edit-appointment-btn", function () {
    let appointmentId = $(this).attr("data-id");

    if (appointmentId) {
        $(".view-appointment-details-model").modal("hide");
        $.ajax({
            url: "controllers/appointment",
            method: "POST",
            data: {
                appointment_id: appointmentId,
                fetchSingleAppointment: true
            },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    let a = res.appointment;

                    // $('[name="startTime"]').val(moment(a.start_datetime).format('DD-MM-YYYY hh:mm A'));
                    // $('[name="endTime"]').val(moment(a.end_datetime).format('DD-MM-YYYY hh:mm A'));

                    let startPicker = flatpickr("#startTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });
                    let endPicker = flatpickr("#endTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });

                    startPicker.setDate(moment(a.start_datetime).format('DD-MM-YYYY hh:mm A'));
                    endPicker.setDate(moment(a.end_datetime).format('DD-MM-YYYY hh:mm A'));


                    $(`.customer-selectbox-parent`).find(`.custom-select-dropdown [role="option"][data-id="${a.customer_id}"]`).trigger("click");
                    setTimeout(() => {
                        $(`.vehicle-history-container`).find(`.custom-select-dropdown [role="option"][data-id="${a.vehicle_id}"]`).trigger("click");
                    }, 1000);
                    $("#appointment_notes").val(a.description);

                    $('[name="appointment_id"]').val(a.id);

                    $('.add-new-appointment-model').modal("show");
                    $(".add-new-appointment-model .modal-title").text("Edit Appointment");
                    // add input 
                    $(".add-new-appointment-model").find("form").append(`<input type="hidden" name="app_id" value="${a.id}" />`)


                }
            },
            error: makeError
        });
    }
});


// Add new customer callback
tc.fn.cb.addCustomerCB = async (form, data) => {
    if (data.status === 'success') {
        let customer = data.single_customer;

        $(".customer-selectbox-parent").find(".search-container").after(`<li role="option" data-id="${customer.id}" data-name="${customer.title + " " + customer.fname + " " + customer.lname}">
                                                        <div class="customer-info">
                                                            <div class="customer-name">${customer.title + " " + customer.fname + " " + customer.lname}</div>
                                                        </div>
                                                    </li>`);

        $(".customer-selectbox-parent").find(`[role="option"][data-id="${customer.id}"]`).trigger("click");
        $(".add-new-customer-container").addClass("d-none");
        $(".add-new-appointment-container").removeClass("d-none");
    } else {
        sAlert(data.data, 'error');
    }
};

// add new option btn
$(document).on("click", ".add-new-option", function () {
    let target = $(this).data("target");

    $(".add-new-appointment-container").addClass("d-none");
    $(".add-new-vehicle-container").addClass("d-none");
    $(".add-new-customer-container").addClass("d-none");

    if (target === "customer") {
        // add new customer
        $(".add-new-customer-container").removeClass("d-none");
        return;
    } else if (target === "vehicle") {
        // add new vehicle
        $(".add-new-vehicle-container").removeClass("d-none");

        let customerId = $(".customer-selectbox-parent").find(".selected_id").val();

        if (!customerId) {
            sAlert('Please select a customer first', "error");
            return;
        }

        var url = _SITE_URL + "user/add-vehicle"
            + "?add_by=invoice"
            + "&customer_id=" + customerId;

        $('#addVehicleIframe').attr('src', url);
        return;
    }
});

// back btn 
$(document).on("click", ".back-to-appointment-btn", function () {
    $(".add-new-customer-container").addClass("d-none");
    $(".add-new-appointment-container").removeClass("d-none");
    $(".add-new-vehicle-container").addClass("d-none");
});

// customer select callback
function customerSelectCB(customer) {
    let customerId = customer.id;
    if (customerId) {
        $.ajax({
            url: "controllers/invoice",
            method: "POST",
            data: { customer_id: customerId, fetchCustomerMotHistory: true },
            dataType: "json",
            success: function (data) {
                let $vehicleSelectBoxContainer = $(".vehicle-history-container")
                if (data.status === "success") {
                    $vehicleSelectBoxContainer.find(`[role="option"]`).remove();
                    $vehicleSelectBoxContainer.find(".custom-select-placeholder").text("--- Select Vehicle History ---");
                    $vehicleSelectBoxContainer.find(".selected_id").val("");
                    $.each(data.data, function (index, item) {
                        $vehicleSelectBoxContainer.find(".search-container").after(`<li role="option" data-id="${item.id}" data-name="${item.reg_number}">
                                                        <div class="customer-info">
                                                            <div class="customer-name">${item.reg_number}</div>
                                                        </div>
                                                    </li>`);
                        $("#motHistoryDiv").removeClass("d-none");

                        $(".invoice-right-container .customer-name").text(data.fname + " " + data.lname);
                        $(".invoice-right-container .customer-address").text(data.address);
                        $(".invoice-right-container .customer-phone .contact").text(data.contact);
                    });
                } else {
                    $("#motHistoryDiv").removeClass("d-none");
                    $vehicleSelectBoxContainer.find(`[role="option"]`).remove();
                    $vehicleSelectBoxContainer.find(".custom-select-placeholder").text("--- Select Vehicle History ---");
                    $vehicleSelectBoxContainer.find(".selected_id").val("");
                }
            }
        });
    } else {
        sAlert("Please select a customer to fetch vehicle history.", "warning");
    }
}

window.addEventListener("message", function (event) {
    if (event.data.type === "FORM_SUBMITTED") {
        $("modal").modal("hide");
        let vehicleData = event.data.vehicleData;
        $(".vehicle-history-container").find(".search-container").after(`<li role="option" data-id="${vehicleData.vehicle_id}" data-name="${vehicleData.reg_number}">
                                                        <div class="customer-info">
                                                            <div class="customer-name">${vehicleData.reg_number}</div>
                                                        </div>
                                                    </li>`);

        $(".vehicle-history-container").find(`[role="option"][data-id="${vehicleData.vehicle_id}"]`).trigger("click");
        $(".back-to-appointment-btn").trigger("click");

        // location.reload();
        console.log("Iframe form submitted!");
        // Yahan aap apna callback ya koi bhi action kar sakte ho
    }
});


$('.modal').on('hide.bs.modal', function (e) {
    $(".add-new-appointment-model").find(`[action="appointment"]`)[0].reset();
    $(".add-new-appointment-model .modal-title").text("Appointment");
    $(".add-new-appointment-model").find(`form [name="app_id"]`).remove();
});

// add new appointment 
$(document).on("click", ".add-appointment-btn", function () {
    $('.add-new-appointment-model').modal("show");

    // Get the selected day (or today if you want)
    let selectedDate = moment().format('DD-MM-YYYY'); // e.g., "26-01-2026"

    // Initialize Flatpickr
    let startPicker = flatpickr("#startTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });
    let endPicker = flatpickr("#endTime", { enableTime: true, dateFormat: "d-m-Y h:i K", time_24hr: false });

    // Set start = 00:00, end = 23:59 for all-day
    startPicker.setDate(selectedDate + " 00:00");
    endPicker.setDate(selectedDate + " 23:59");

});