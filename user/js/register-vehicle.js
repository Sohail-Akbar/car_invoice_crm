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

// Render MOT history (jQuery)
async function renderMotHistory(data) {
    const $container = $("#modalMotContent").empty();

    if (!data.length) {
        $container.html("<p>No MOT history available.</p>");
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

function renderCustomerAndVehicle(data) {
    const c = data.data.customer;
    const v = data.data.existingRecord;

    let $vehicleEditHtml = "";
    if (v.is_manual == 1) $vehicleEditHtml = `<button class="edit-btn car-edit-btn" data-vehicle='${JSON.stringify(v)}'><i class="fas fa-edit"></i> Edit</button>`;

    return `
        <div class="content-grid row m-0">
            <!-- Customer Info -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header customer-header">
                        <div class="card-title">
                            <i class="fas fa-user"></i> Customer Information
                        </div>
                        <button class="edit-btn customer-edit-btn toggle-edit" data-customer='${JSON.stringify(c)}'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="edit-btn change-customer-btn toggle-edit" title="Change Customer" data-vehicle='${JSON.stringify(v)}'>
                            <i class="fas fa-edit"></i> Change Customer
                        </button>
                    </div>
                    <div class="card-content">
                        <div class="detail-row">
                            <div class="detail-icon customer-icon"><i class="fas fa-user-circle"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value">${c.title} ${c.fname} ${c.lname}</div>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon customer-icon"><i class="fas fa-envelope"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value">${c.email}</div>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon customer-icon"><i class="fas fa-phone"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Phone Number</div>
                                <div class="detail-value">${c.contact}</div>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon customer-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">${c.address}, ${c.city}, ${c.postcode}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Info -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header car-header">
                        <div class="card-title">
                            <i class="fas fa-car"></i> Vehicle Information
                        </div>
                        ${$vehicleEditHtml}
                    </div>
                    <div class="card-content">
                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-car-side"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Make & Model</div>
                                <div class="detail-value">${v.make} ${v.model}</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-hashtag"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Registration</div>
                                <div class="detail-value">${v.registrationDate}</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">First Used Date</div>
                                <div class="detail-value">${v.firstUsedDate}</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-gas-pump"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Fuel Type</div>
                                <div class="detail-value">${v.fuelType}</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-palette"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Color</div>
                                <div class="detail-value">${v.primaryColour}</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-cogs"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Engine Size</div>
                                <div class="detail-value">${v.engineSize} cc</div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-icon car-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="detail-content">
                                <div class="detail-label">Registration Expiry</div>
                                <div class="detail-value">
                                    ${v.expiryDate}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}




// mot history
tc.fn.cb.motHistoryCB = async (form, data) => {
    let $registerVehicleCon = $("#registrationCarContainer");
    $registerVehicleCon.find(".vehicle-information-header").removeClass("mb-4");
    $registerVehicleCon.addClass("d-none");
    $(".already-register-details").addClass("d-none")
    if (data.status === "success") {
        if (data.data.isExistingRecord) {
            $registerVehicleCon.find("form").addClass("d-none");
            // Generate HTML dynamically
            const html = renderCustomerAndVehicle(data);
            $(".already-register-details").html(html).removeClass("d-none");
            $registerVehicleCon.removeClass("d-none");

            if (data.data.existingRecord.is_manual == 1) {
                $registerVehicleCon.find(".vehicle-information-header").addClass("d-none");
            } else {
                $registerVehicleCon.find(".vehicle-information-header").removeClass("d-none").addClass("mb-4");
                if (data.data.vehicleInfo.motTests) await renderMotHistory(data.data.vehicleInfo.motTests);
            }
        } else {
            if (data.data.motTests) await renderMotHistory(data.data.motTests);
            // $(".mot-history-model").modal('show');
            appendFormFields(data);
            $registerVehicleCon.removeClass("d-none");
        }
    } else {
        Swal.fire({
            title: 'No MOT History Found',
            text: "We couldn’t find MOT data for this registration.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Manually Register'
        }).then((result) => {
            if (result.value) {
                window.location.href = 'registration-vehicle?type=manually';
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
    } else {
        sAlert(data.message, 'error');
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
    console.log(data);

    $alreadyRegCustomer.addClass("d-none");
    $parent.find(`.customer-vehicle-form`).removeClass("d-none");
    $parent.find(`.customer-vehicle-form`).append(`<input type="hidden" name="vehicle_id" value="${data.id}">`);
    $("#motFields").html("");
});