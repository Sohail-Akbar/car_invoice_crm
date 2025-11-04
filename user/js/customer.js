// Tab functionality
$(document).ready(function () {
    $('.tab').on('click', function () {
        var targetTab = $(this).data('tab');
        var activeFirstTab = $(this).data('callback');

        // Remove active class from all tabs and contents
        $('.tab').removeClass('active');
        $('.tab-content').removeClass('active');

        // Add active class to current tab and related content
        $(this).addClass('active');
        $('#' + targetTab).addClass('active');
        console.log(this);

        if (activeFirstTab) {
            $("#carTabs").find(".nav-item").first().find(".nav-link").click()
        }
    });
});


// On tab click
$(document).on('click', '#carTabs .nav-link', function (e) {
    e.preventDefault();
    $('#carTabs .nav-link').removeClass('active');
    $(this).addClass('active');

    let carId = $(this).data('id'),
        customerId = _GET['id']; // assuming _GET provides customer id

    let $container = $(".cars-info-container");
    $container.html('<div class="text-center py-4 text-muted">Loading...</div>');

    let data = {
        car_id: carId,
        customer_id: customerId,
        fetchCarInfo: true
    };

    $.ajax({
        url: "controllers/customer",
        method: "POST",
        data: data,
        success: function (res) {
            // show HTML directly in container
            $container.html(res);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $container.html('<div class="alert alert-danger">Error loading car details.</div>');
        }
    });
});



// Add new customer callback
tc.fn.cb.assignedStaffCB = async (form, data) => {
    sAlert(data.data, data.status);
    if (data.status === 'success') {

        // form reset 
        form[0].reset();
        // popup close
        $('.modal').modal('hide');
        setTimeout(() => {
            $("#cars").find(".nav-link.active").first().click();
        }, 200);
    }
}