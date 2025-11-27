// Function to calculate subtotal, tax, total, due
function calculateTotals() {
    let subtotal = 0;

    $(".invoice-services-container .row").each(function () {
        let amount = parseFloat($(this).find(".service_amount").val()) || 0;
        let qty = parseInt($(this).find(".service_quantity").val()) || 1;
        subtotal += amount * qty;
    });

    // TAX
    const taxRate = parseFloat($("#tax_rate").val()) || 0;
    const tax = subtotal * (taxRate / 100);

    // BRANCH DISCOUNT %
    let branchDiscountPercentage = parseFloat($("#discountPercentage").val()) || 0;

    // CUSTOM DISCOUNT AMOUNT
    let customDiscountAmount = parseFloat($("#discountAmount").val()) || 0;

    // BRANCH % discount amount
    let branchDiscountAmount = (subtotal * branchDiscountPercentage) / 100;

    // TOTAL DISCOUNT = branch % + user custom discount
    let totalDiscount = branchDiscountAmount + customDiscountAmount;

    // FINAL TOTAL
    const totalBeforeDiscount = subtotal + tax;
    const total = totalBeforeDiscount - totalDiscount;

    // PAID / DUE
    const paid = parseFloat($("#paid_amount").val()) || 0;
    const due = total - paid;

    // OUTPUT
    $("#subtotal").text(subtotal.toFixed(2));
    $("#tax_amount").text(tax.toFixed(2));
    $("#discount_show").text(totalDiscount.toFixed(2));
    $("#total_amount").text(total.toFixed(2));
    $("#due_amount").text(due.toFixed(2));
}



$(document).on("input", ".service_quantity, #discountAmount", calculateTotals);

// Function to generate service options HTML from SERVICES array
function getServiceOptions() {
    let options = `<option value="">-- Select Service --</option>`;
    SERVICES.forEach(service => {
        options += `<option value="${service.id}" data-amount="${service.amount}">
                            ${service.text}
                        </option>`;
    });
    return options;
}

// Add a new row dynamically
function addInvoiceRow() {
    const newRow = `
        <div class="row mx-0">
            <div class="col-md-6 px-0 mb-2 col-12">
                <select  class="form-control service_id select2-list invoice-select-box" data-type="service" data-tags="tags" name="services_id[]">
                    ${getServiceOptions()}
                </select>
            </div>
            <div class="col-md-6 px-0 col-12">
                <div class="d-flex">
                    <input type="number" class="form-control service_quantity invoice-input-item ml-2" step="1" min="1" name="service_quantity[]" value="1">
                    <input type="number" class="form-control service_amount invoice-input-item" step="any" name="service_amount[]" value="0">
                    <button type="button" class="btn btn-sm remove-row">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.0638 0.506144L7.00014 4.56991C5.64566 3.2159 4.29075 1.86059 2.93606 0.506144C1.36892 -1.06096 -1.06026 1.36904 0.505569 2.93658C1.86048 4.29015 3.21583 5.64568 4.56921 7.00013C3.21521 8.35519 1.86066 9.70971 0.505569 11.0637C-1.06026 12.6303 1.36914 15.0597 2.93606 13.4941C4.29075 12.139 5.64537 10.7844 6.99992 9.43013L11.0636 13.4941C12.6307 15.0608 15.0605 12.6306 13.494 11.0637C12.1394 9.70887 10.7844 8.35421 9.42932 6.99969C10.7842 5.64474 12.1391 4.28993 13.494 2.93527C15.0608 1.36904 12.6309 -1.06096 11.0636 0.507018" fill="#EC1C24" />
                        </svg>
                    </button>
                </div>
            </div>
        </div> <hr class="mt-1">`;
    $(".invoice-services-container").prepend(newRow);
    select2();
}


// Event: Service change updates amount
$(document).on("change", ".service_id", function () {
    const amount = parseFloat($(this).find("option:selected").data("amount")) || 0;
    $(this).closest(".row").find(".service_amount").val(amount);
    calculateTotals();
});
// Event: Tax or Paid input changes
$(document).on("input", "#tax_rate, #paid_amount", calculateTotals);

// Event: Add new row button
$("#add_row").click(function () {
    addInvoiceRow();
});

// Event: Remove row
$(document).on("click", ".remove-row", function () {
    $(this).closest(".row").remove();
    calculateTotals();
});

$(document).ready(function () {
    // if customer id and vehicle id is existing 
    if (_GET.customer_id) {
        $("#customerSelectBox").val(_GET.customer_id).trigger("change");
    }
    // Initialize Select2 for all selects
    select2();

    // ✅ Load invoice items automatically if available
    if (typeof INVOICE_ITEMS !== "undefined" && INVOICE_ITEMS.length > 0) {

        INVOICE_ITEMS.reverse().forEach(item => {
            addInvoiceRow();

            const $lastRow = $(".invoice-services-container .row:first");

            const $select = $lastRow.find(".service_id");

            $select.find(`option[value="${item.id}"]`).prop("selected", true).trigger("change");
            // Set the quantity for the current row only
            $lastRow.find(".service_quantity").val(item.quantity);
        });
    } else {
        // Add one empty row if no invoice items exist
        addInvoiceRow();
    }
    // Initial total calculation
    calculateTotals();
});



// Customer's vehicle History
$(document).on("change", "#customerSelectBox", function () {
    let customerId = $(this).val();
    if (customerId) {
        $.ajax({
            url: "controllers/invoice",
            method: "POST",
            data: { customer_id: customerId, fetchCustomerMotHistory: true },
            dataType: "json",
            success: function (data) {
                console.log(data);

                let motHistorySelect = $("#motHistorySelectBox");
                if (data.status === "success") {
                    motHistorySelect.empty();
                    motHistorySelect.append('<option value="">-- Select Vehicle History --</option>');
                    $.each(data.data, function (index, item) {
                        motHistorySelect.append('<option value="' + item.id + '">' + item.reg_number + '</option>');
                    });
                    $("#motHistoryDiv").removeClass("d-none");
                    if (_GET.vehicle_id) {
                        $("#motHistorySelectBox").val(_GET.vehicle_id).trigger("change");
                    }

                    $(".customer-name").text(data.fname + " " + data.lname);
                    $(".customer-address").text(data.address);
                    $(".customer-phone .contact").text(data.contact);

                } else {
                    sAlert(data.data, data.status);
                    motHistorySelect.empty();
                }
            }
        });
    } else {
        sAlert("Please select a customer to fetch vehicle history.", "warning");
    }
});


// When user selects or adds a new option
// Handle new service creation via Select2
$(document).on('select2:select', '.select2-list', function (e) {
    const selectedData = e.params.data;
    const $select = $(this);
    const type = $select.data('type');

    // Trigger AJAX only for new tags
    if (selectedData.id === selectedData.text) {
        $.ajax({
            url: 'controllers/services',
            type: 'POST',
            data: {
                type: type,
                value: selectedData.text,
                addNewServices: true
            },
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    let serviceData = res.data;
                    const newOption = new Option(serviceData.text, serviceData.id, true, true);
                    $select.append(newOption).trigger('change');
                    sAlert('Service added successfully', "success");
                    SERVICES = serviceData.services;
                    console.log(SERVICES);
                } else {
                    sAlert(res.message || 'Duplicate or error', "error");
                }
            },
            error: function (xhr, status, err) {
                console.log("Error:", err);
            }
        });
    }
});


// Service Amount Update on focus out (blur)
$(document).on("focusout", ".service_amount", function () {
    let $input = $(this);
    let amount = $input.val().trim();
    let id = $input.closest(".row").find(".service_id").val();

    if (!id) {
        sAlert("Please select a service properly.", "warning");
        calculateTotals();
        return;
    }

    if (amount === "" || isNaN(amount)) {
        sAlert("Please enter a valid amount.", "warning");
        $input.val(0);
        calculateTotals();
        return;
    }

    $.ajax({
        url: "controllers/services",
        method: "POST",
        data: {
            amount,
            id,
            updateAmount: true
        },
        dataType: "json",
        success: function (res) {
            calculateTotals();
        },
        error: makeError
    });
});
