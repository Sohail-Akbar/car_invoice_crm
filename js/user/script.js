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