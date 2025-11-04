// #region Profile Picture
$('.sidebar .user-info .user-img-file').on('change', function () {
    let file = this.files;
    if (file.length < 1) return false;
    file = file[0];
    if (!isImageFile(file)) {
        sAlert('Invalid File type. Please upload an image file', 'error');
        return false;
    }
    let image = URL.createObjectURL(file),
        parent = $(this).parents('.user-image-container');
    parent.find('.user-img').attr('src', image);
    parent.addClass('active');
});
$('.sidebar .user-info .save-img').on('click', function () {
    let imagecontainer = $(this).parent().find('.user-image-container'),
        file = imagecontainer.find('.user-img-file').get(0).files,
        btn = $(this),
        btnText = $(this).html();
    if (file.length < 1) {
        imagecontainer.removeClass('active');
        return false;
    }
    file = file[0];
    if (!isImageFile) {
        sAlert('Invalid File type. Please upload an image file', 'error');
        return false;
    }
    let formData = new FormData();
    formData.append('changeUserImage', file);
    $.ajax({
        url: "controllers/user",
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            disableBtn(btn);
        },
        success: function (response) {
            enableBtn(btn, btnText);
            imagecontainer.removeClass('active');
            sAlert('Image Changed Successfully!', 'success');
        },
        error: function () {
            enableBtn(btn, btnText);
            makeError();
        }
    })
});
// #endregion Profile Picture

// #region Sidebar links dropdown
$(document).on("click", ".sidebar .nav .nav-item.with-sub-menu > .nav-link", function (e) {
    e.preventDefault();
    let $li = $(this).parent();
    let $submenu = $li.find(".sub-menu");
    $submenu.slideToggle(300);
    $li.toggleClass("active", !$li.is(".active"));
});
// #endregion Sidebar links dropdown


// #region Sidebar links active
function activeSidebarLink() {
    let url = window.location.href.split("/").pop(),
        $li = $(`.sidebar .nav .nav-link[href="${url}"]`).parent();
    if ($li.parents(".nav-item").length > 0) {
        $li.parents(".nav-item").children('.nav-link').trigger("click");
    }
    $li.addClass("active");
    l($li.parents(".nav-item"))
}
activeSidebarLink();
// #endregion Sidebar links active


$(document).on("click", ".sidebar .nav .dropdown-toggle", function (e) {
    e.preventDefault();
    let $parent = $(this).parent(".dropdown");

    // Toggle active class
    $parent.toggleClass("active");

    // Slide toggle submenu
    $parent.find(".dropdown-menu").first().slideToggle(200);

    // Close others
    $parent.siblings(".dropdown.active").removeClass("active").find(".dropdown-menu").slideUp(200);
});



if ($('.dataTable').length) $('.dataTable').DataTable();


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


// mot history
tc.fn.cb.motHistoryCB = async (form, data) => {

    if (data.status === 'success') {
        await renderMotHistory(data.data.motTests);
        // $(".mot-history-model").modal('show');
        appendFormFields(data);
        $("#registrationCarContainer").show();
    } else {
        sAlert(data.data, data.status);
    }

};


// Add new customer callback
tc.fn.cb.addCustomerCB = async (form, data) => {
    if (data.status === 'success') {
        // Handle success
        let $customerContainerSelList = $("#customersContainer");
        $customerContainerSelList.empty(); // Clear existing options
        // loop customer
        data.customer.forEach(customer => {
            $customerContainerSelList.append(new Option(customer.title + " " + customer.fname + " " + customer.lname, customer.id));
        });
        sAlert("Customer created successfully", 'success');
        // form reset 
        form[0].reset();
        // popup close
        $('.add-new-customer-model').modal('hide');
    } else {
        sAlert(data.message, 'error');
    }
}