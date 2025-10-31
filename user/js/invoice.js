
$(document).ready(function () {

    // Function to calculate subtotal, tax, total, due
    function calculateTotals() {
        let subtotal = 0;

        $(".service_amount").each(function () {
            subtotal += parseFloat($(this).val()) || 0;
        });

        const taxRate = parseFloat($("#tax_rate").val()) || 0;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        const paid = parseFloat($("#paid_amount").val()) || 0;
        const due = total - paid;

        $("#subtotal").text(subtotal.toFixed(2));
        $("#tax_amount").text(tax.toFixed(2));
        $("#total_amount").text(total.toFixed(2));
        $("#due_amount").text(due.toFixed(2));
    }

    // Function to generate service options HTML from SERVICES array
    function getServiceOptions() {
        let options = `<option value="">-- Select Service --</option>`;
        SERVICES.forEach(service => {
            options += `<option value="${service.id}" data-amount="${service.amount}">
                            ${service.text} (${service.amount})
                        </option>`;
        });
        return options;
    }

    // Add a new row dynamically
    function addInvoiceRow() {
        const newRow = `
        <tr>
            <td>
                <select class="form-control service_id" name="services_id[]">
                    ${getServiceOptions()}
                </select>
            </td>
            <td><input type="number" class="form-control service_amount" name="service_amount[]" value="0" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
        </tr>`;
        $("#invoice_table tbody").append(newRow);
    }

    // Initialize first row
    addInvoiceRow();

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

    // Initial calculation
    calculateTotals();
});


// Customer's Mot History
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

                if (data.status === "success") {
                    let motHistorySelect = $("#motHistorySelectBox");
                    motHistorySelect.empty();
                    motHistorySelect.append('<option value="">-- Select MOT History --</option>');
                    $.each(data.data, function (index, item) {
                        console.log(item);

                        motHistorySelect.append('<option value="' + item.id + '">' + item.reg_number + '</option>');
                    });
                    $("#motHistoryDiv").removeClass("d-none");
                } else {
                    sAlert(data.data, data.status);
                }
            }
        });
    } else {
        sAlert("Please select a customer to fetch MOT history.", "warning");
    }
});
