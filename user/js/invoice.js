// Function to calculate subtotal, tax, total, due
function calculateTotals() {
    let subtotal = 0;

    $("#invoice_table tbody tr").each(function () {
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
        <tr>
            <td>
                <select class="form-control service_id select2-list" data-type="service" name="services_id[]">
                    ${getServiceOptions()}
                </select>
            </td>
            <td><input type="number" class="form-control service_quantity" step="1" min="1" name="service_quantity[]" value="1"></td>
            <td><input type="number" class="form-control service_amount" step="any" name="service_amount[]" value="0"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
        </tr>`;
    $("#invoice_table tbody").append(newRow);
    select2();
}


// Event: Service change updates amount
$(document).on("change", ".service_id", function () {
    const amount = parseFloat($(this).find("option:selected").data("amount")) || 0;
    $(this).closest("tr").find(".service_amount").val(amount);
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
    $(this).closest("tr").remove();
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
        INVOICE_ITEMS.forEach(item => {
            addInvoiceRow();

            const $lastRow = $("#invoice_table tbody tr:last");

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
    let id = $input.closest("tr").find(".service_id").val();

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
