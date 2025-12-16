// Function to append fields dynamically
function appendFormFields(data) {
    const $container = $("#motFields").find(".row");
    $container.empty(); // clear old data

    $.each(data, function (key, value) {
        // Skip unwanted keys
        if (key === "status" || key === "data") return;

        // Create label + input
        const field = `
        <div class="col-md-6">
            <div class="form-group">
                <label class="label" for="${key}">${key.replace(/([A-Z])/g, ' $1')}:</label>
                <input type="text" class="form-control" id="${key}" name="${key}" value="${value}" readonly>
            </div>
        </div>
        `;

        $container.append(field);
    });
}

// Render vehicle history (jQuery)
async function renderMotHistory(data) {
    const $container = $("#modalMotContent").empty();

    if (!data.length) {
        $container.html("<p>No vehicle history available.</p>");
        return;
    }

    $.each(data, function (index, test) {
        // Format dates
        const testDate = new Date(test.completedDate).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        const expiryDate = test.expiryDate ? new Date(test.expiryDate).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }) : 'N/A';

        // Determine status class and icon
        const statusClass = test.testResult === 'PASSED' ? 'mot-pass' : 'mot-fail';
        const statusIcon = test.testResult === 'PASSED' ? 'fa-check-circle' : 'fa-times-circle';
        const statusText = test.testResult === 'PASSED' ? 'Passed' : 'Failed';

        // Format mileage
        const mileage = test.odometerValue ? `${test.odometerValue} ${test.odometerUnit}` : 'N/A';

        // Filter defects for advisories only
        const advisories = test.defects.filter(defect => defect.type === 'ADVISORY');

        // Create MOT card
        const $motCard = $('<div>').addClass('mot-card');

        // Status header
        const $statusHeader = $('<div>').addClass('mot-status-header');
        $statusHeader.append(
            $('<div>').addClass('status ' + statusClass).html(
                `<i class="fas ${statusIcon}"></i> ${statusText}`
            )
        );
        $statusHeader.append(
            $('<div>').addClass('test-date').text(testDate)
        );

        // MOT info box
        const $infoBox = $('<div>').addClass('mot-info-box');
        $infoBox.append(
            $('<div>').addClass('mot-row').html(
                `<span class="label">MOT test number:</span><span class="value">${test.motTestNumber}</span>`
            )
        );
        $infoBox.append(
            $('<div>').addClass('mot-row').html(
                `<span class="label">Mileage:</span><span class="value">${mileage}</span>`
            )
        );
        $infoBox.append(
            $('<div>').addClass('mot-row').html(
                `<span class="label">Expiry Date:</span><span class="value">${expiryDate}</span>`
            )
        );

        // Advisories section
        const $advisories = $('<div>').addClass('advisories');
        $advisories.append(
            $('<p>').html('<i class="fas fa-file-signature"></i> <strong>Monitor and repair if necessary (advisories):</strong>')
        );

        const $advisoryList = $('<ul>');
        if (advisories.length > 0) {
            $.each(advisories, function (i, advisory) {
                $advisoryList.append($('<li>').text(advisory.text));
            });
        } else {
            $advisoryList.append($('<li>').text('No advisories'));
        }
        $advisories.append($advisoryList);

        // Assemble the card
        $motCard.append($statusHeader);
        $motCard.append($('<hr>'));
        $motCard.append($infoBox);
        $motCard.append($('<hr>'));
        $motCard.append($advisories);

        // Add to container
        $container.append($motCard);
    });
}

// function renderCustomerAndVehicle(data) {
//     const c = data.data.customer;
//     const v = data.data.existingRecord;

//     let $vehicleEditHtml = "";
//     if (v.is_manual == 1) $vehicleEditHtml = `<button class="edit-btn car-edit-btn" data-vehicle='${JSON.stringify(v)}'><i class="fas fa-edit"></i> Edit</button>`;

//     return `
//         <div class="content-grid row m-0">
//             <!-- Customer Info -->
//             <div class="col-md-7">
//                 <div class="card">
//                     <div class="card-header customer-header">
//                         <div class="card-title">
//                             <i class="fas fa-user"></i> Customer Information
//                         </div>
//                         <button class="edit-btn customer-edit-btn toggle-edit" data-customer='${JSON.stringify(c)}'>
//                             <i class="fas fa-edit"></i> Edit
//                         </button>
//                         <button class="edit-btn change-customer-btn toggle-edit" title="Change Customer" data-vehicle='${JSON.stringify(v)}'>
//                             <i class="fas fa-edit"></i> Change Customer
//                         </button>
//                     </div>
//                     <div class="card-content">
//                         <div class="detail-row">
//                             <div class="detail-icon customer-icon"><i class="fas fa-user-circle"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Full Name</div>
//                                 <div class="detail-value">${c.title} ${c.fname} ${c.lname}</div>
//                             </div>
//                         </div>
//                         <div class="detail-row">
//                             <div class="detail-icon customer-icon"><i class="fas fa-envelope"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Email Address</div>
//                                 <div class="detail-value">${c.email}</div>
//                             </div>
//                         </div>
//                         <div class="detail-row">
//                             <div class="detail-icon customer-icon"><i class="fas fa-phone"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Phone Number</div>
//                                 <div class="detail-value">${c.contact}</div>
//                             </div>
//                         </div>
//                         <div class="detail-row">
//                             <div class="detail-icon customer-icon"><i class="fas fa-map-marker-alt"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Address</div>
//                                 <div class="detail-value">${c.address}, ${c.city}, ${c.postcode}</div>
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </div>

//             <!-- Vehicle Info -->
//             <div class="col-md-5">
//                 <div class="card">
//                     <div class="card-header car-header">
//                         <div class="card-title">
//                             <i class="fas fa-car"></i> Vehicle Information
//                         </div>
//                         ${$vehicleEditHtml}
//                     </div>
//                     <div class="card-content">
//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-car-side"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Make & Model</div>
//                                 <div class="detail-value">${v.make} ${v.model}</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-hashtag"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Registration</div>
//                                 <div class="detail-value">${v.registrationDate}</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-calendar-alt"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">First Used Date</div>
//                                 <div class="detail-value">${v.firstUsedDate}</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-gas-pump"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Fuel Type</div>
//                                 <div class="detail-value">${v.fuelType}</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-palette"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Color</div>
//                                 <div class="detail-value">${v.primaryColour}</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-cogs"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Engine Size</div>
//                                 <div class="detail-value">${v.engineSize} cc</div>
//                             </div>
//                         </div>

//                         <div class="detail-row">
//                             <div class="detail-icon car-icon"><i class="fas fa-calendar-check"></i></div>
//                             <div class="detail-content">
//                                 <div class="detail-label">Registration Expiry</div>
//                                 <div class="detail-value">
//                                     ${v.expiryDate}
//                                 </div>
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;
// }

function renderCustomerAndVehicle(data) {
    const c = data.data.customer;
    const v = data.data.existingRecord;

    let $vehicleEditHtml = "";
    if (v.is_manual == 1) $vehicleEditHtml = `<i class="fas fa-edit text-success edit-btn car-edit-btn cp" data-vehicle='${JSON.stringify(v)}'></i>`;

    return `
    <div class="compact-info-card">
        <div class="card-header">
            <div class="card-title mb-0">
                <i class="fas fa-id-card"></i> Customer & Vehicle Information
            </div>
            <div class="header-buttons">
                <button class="edit-btn change-customer-btn toggle-edit" title="Change Customer" data-vehicle='${JSON.stringify(v)}'>
                    <i class="fas fa-exchange-alt"></i> Change Customer
                </button>
                <a href="invoice?customer_id=${c.id}&vehicle_id=${v.id}" class="edit-btn" title="Generate Invoice">
                    <i class="fas fa-file-alt"></i> Generate Invoice
                </a>
            </div>
        </div>
        
        <div class="info-content">
            <!-- Customer Information Section -->
            <div class="customer-section">
                <div class="section-title">
                    <i class="fas fa-user"></i> Customer Details &nbsp; <i class="fas fa-edit text-success cp edit-btn customer-edit-btn toggle-edit" data-customer='${JSON.stringify(c)}'></i>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">${c.title} ${c.fname} ${c.lname}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">${c.email}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">${c.contact}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Address</div>
                            <div class="info-value">${c.address}, ${c.city}, ${c.postcode}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section-divider"></div>
            
            <!-- Vehicle Information Section -->
            <div class="vehicle-section">
                <div class="section-title">
                    <i class="fas fa-car"></i> Vehicle Details &nbsp; ${$vehicleEditHtml}
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Make & Model</div>
                            <div class="info-value">${v.make} ${v.model}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Registration</div>
                            <div class="info-value">${v.registrationDate}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">First Used Date</div>
                            <div class="info-value">${v.firstUsedDate}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Fuel Type</div>
                            <div class="info-value">${v.fuelType}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Color</div>
                            <div class="info-value">${v.primaryColour}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Engine Size</div>
                            <div class="info-value">${v.engineSize} cc</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Registration Expiry</div>
                            <div class="info-value highlight">${v.expiryDate}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
}




// vehicle history
tc.fn.cb.motHistoryCB = async (form, data) => {
    let $registerVehicleCon = $("#registrationCarContainer");
    // header
    $registerVehicleCon.find(".vehicle-information-header").removeClass("mb-4");
    $registerVehicleCon.addClass("d-none");
    $registerVehicleCon.find(".customer-field").removeClass("d-none");
    // already register vehicle details
    $(".already-register-details").addClass("d-none").html("");

    if (data.status === "success") {
        if (data.data.isExistingRecord) {
            // $registerVehicleCon.find("form").addClass("d-none");
            // // Generate HTML dynamically
            // const html = renderCustomerAndVehicle(data);
            // $(".already-register-details").html(html).removeClass("d-none");
            // $registerVehicleCon.removeClass("d-none");

            // if (data.data.existingRecord.is_manual == 1) {
            //     $registerVehicleCon.find(".vehicle-information-header").addClass("d-none");
            // } else {
            //     $registerVehicleCon.find(".vehicle-information-header").removeClass("d-none").addClass("mb-4");
            //     if (data.data.vehicleInfo.motTests) await renderMotHistory(data.data.vehicleInfo.motTests);
            // }
            let customerName = data.data.customer.fname + " " + data.data.customer.lname;
            sAlert('This Vehicle Is Already Linked to ' + customerName, "warning");
        } else {
            if (data.data.motTests) await renderMotHistory(data.data.motTests);
            // $(".mot-history-model").modal('show');
            appendFormFields(data);
            $registerVehicleCon.removeClass("d-none");
            if (GLOBAL_GET.customer_id) {
                $registerVehicleCon.find("#customersContainer").val(GLOBAL_GET.customer_id).trigger("change");
                $registerVehicleCon.find(".customer-field").addClass("d-none");
            }
        }
    } else {
        Swal.fire({
            title: 'No Vehicle History Found',
            text: "We couldn’t find MOT data for this registration.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Manually Register'
        }).then((result) => {
            if (result.value) {
                if (GLOBAL_GET.add_by) {
                    window.location.href = 'registration-vehicle?type=manually&add_by=invoice&customer_id=' + GLOBAL_GET.customer_id;
                } else {
                    window.location.href = 'registration-vehicle?type=manually';
                }
            }
        })
        $registerVehicleCon.addClass("d-none");
    }
};



// Add new customer callback
tc.fn.cb.addCustomerCB = async (form, data) => {
    console.log(data);

    if (data.status === 'success') {
        // Adjusted for correct structure
        let customers = data.customer || data.data?.customer || [];

        let $customerContainerSelList = $("#customersContainer");
        $customerContainerSelList.empty();

        customers.forEach(customer => {
            $customerContainerSelList.append(
                new Option(`${customer.title} ${customer.fname} ${customer.lname}`, customer.id)
            );
        });

        sAlert("Customer created successfully", 'success');
        form[0].reset();
        $('.add-new-customer-model').modal('hide');
        window.parent.postMessage({ type: "FORM_SUBMITTED" }, "*");
    } else {
        sAlert(data.data, 'error');
    }
};

// Add new customer callback
tc.fn.cb.addNewVehicleCB = async (form, data) => {
    if (data.status === 'success') {
        window.parent.postMessage({ type: "FORM_SUBMITTED" }, "*");
        location.href = data.redirect;
    } else {
        sAlert(data.data, 'error');
    }
};


// Edit Customer info
$(document).on("click", ".customer-edit-btn", function () {
    let data = $(this).data("customer");
    let $modal = $(".add-new-customer-model");
    let $form = $modal.find("form");
    $form.append(`<input type="hidden" name="customer_id" value=""><input type="hidden" name="updateCustomerInfo" value="true">`);
    $form.find(`[name="createCustomer"]`).remove();
    $modal.find(".modal-title").text("Update Customer Information");
    $form.attr("action", "customer");
    $form.attr("data-callback", "updateCustomerInfoCB");
    $form.find(`[name="password"]`).removeAttr("required");


    let formData = {
        "customer_id": data.id,
        "title": data.title,
        "gender": data.gender,
        "fname": data.fname,
        "lname": data.lname,
        "email": data.email,
        "contact": data.contact,
        "address": data.address,
        "postcode": data.postcode,
        "city": data.city,
    };

    fillFormValues($form, formData);
    $('.add-new-customer-model').modal('show');
});

// add customer btn
$(document).on("click", ".add-new-customer-btn", function () {
    let $modal = $(".add-new-customer-model");
    let $form = $modal.find("form");
    $form.find(`[name="customer_id"]`).remove();
    $form.find(`[name="updateCustomerInfo"]`).remove();
    $form.append(`<input type="hidden" name="createCustomer" value="true">`);
    $modal.find(".modal-title").text("Add Customer Information");
    $form.attr("action", "customer");
    $form.attr("data-callback", "addCustomerCB");
    $form.find(`[name="password"]`).attr("required", "true");
    $form.get(0).reset()

    $modal.modal('show');
});

// update customer callback
tc.fn.cb.updateCustomerInfoCB = async (form, data) => {
    if (data.status === 'success') {
        form.append(`<input type="hidden" name="createCustomer" value="true">`);
        form.find(`[name="customer_id"]`).remove();
        form.find(`[name="updateCustomerInfo"]`).remove();
        form.attr("action", "staff");
        form.attr("data-callback", "addCustomerCB");
        // form reset 
        form[0].reset();
        // popup close
        $('.add-new-customer-model').modal('hide');

        $(`[action="mot-history"]`).first().submit()
    }
    sAlert(data.data, data.status);
}

// vehicle edit
$(document).on("click", ".car-edit-btn", function () {
    let data = $(this).data("vehicle");
    let $modal = $(".update-vehicle-model"),
        $form = $modal.find("form");
    console.log(data);

    let formData = {
        "id": data.id,
        "customer_id": data.customer_id,
        "reg_number": data.reg_number,
        "make": data.make,
        "model": data.model,
        "firstUsedDate": data.firstUsedDate,
        "fuelType": data.fuelType,
        "primaryColour": data.primaryColour,
        "registrationDate": data.registrationDate,
        "manufactureDate": data.manufactureDate,
        "engineSize": data.engineSize,
        "hasOutstandingRecall": data.hasOutstandingRecall,
        "expiryDate": data.expiryDate,
    };

    fillFormValues($form, formData);
    $modal.modal('show');
});

// update customer callback
tc.fn.cb.vehicleInformationUpdateCB = async (form, data) => {
    if (data.status === 'success') {
        // form reset 
        form[0].reset();
        // popup close
        $('.update-vehicle-model').modal('hide');

        $(`[action="mot-history"]`).first().submit()
    }
    sAlert(data.data, data.status);
}

// Change Customer
$(document).on("click", ".change-customer-btn", function () {
    let data = $(this).data("vehicle");
    let $parent = $("#registrationCarContainer");
    let $alreadyRegCustomer = $parent.find(".already-register-details");

    $alreadyRegCustomer.addClass("d-none");
    $parent.find(`.customer-vehicle-form`).removeClass("d-none");
    $parent.find(`.customer-vehicle-form`).append(`<input type="hidden" name="vehicle_id" value="${data.id}">`);
    $("#motFields").html("");
});


// Initialize Select2 with garage customers
function initializeSelect2(data) {
    const options = data.customers.map(customer =>
        `<option value="${customer.id}" data-phone="${customer.contact}">
            ${customer.fname + " " + customer.lname} (${customer.contact})
        </option>`
    ).join('');

    $('#customerSelect').html(options).select2({
        placeholder: "Select customers ...",
        allowClear: true,
        width: '100%'
    });
}

// Document Ready
$(document).ready(function () {
    if (_GET.search_by === "name" || _GET.search_by === "phone") {
        $.ajax({
            url: "controllers/customer",
            method: "POST",
            data: { getCustomersData: true },
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    initializeSelect2(res.data);
                }
            },
            error: makeError
        });
    }
});
