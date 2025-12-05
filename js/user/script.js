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

//#region Sidebar
$(document).ready(function () {
    var $sidebar = $('#sidebar');
    var $overlay = $('#overlay'); // Make sure this exists in your HTML
    var $toggleSidebar = $('#toggleSidebar'); // Your actual toggle button
    var $userSidebar = $('#userSidebar');
    var $userProfile = $('#userProfile');

    // Toggle left sidebar on mobile
    $toggleSidebar.on('click', function () {
        if ($(window).width() <= 768) {
            $sidebar.toggleClass('mobile-open');
            $overlay.toggleClass('active');
            $userSidebar.removeClass('open'); // Close user sidebar if open
        }
    });

    // Toggle user sidebar
    $userProfile.on('click', function () {
        $userSidebar.toggleClass('open');
        if ($(window).width() <= 768) {
            $overlay.toggleClass('active');
            $sidebar.removeClass('mobile-open'); // Close left sidebar if open
        }
    });

    // Close sidebars when clicking overlay
    $overlay.on('click', function () {
        $sidebar.removeClass('mobile-open');
        $userSidebar.removeClass('open');
        $overlay.removeClass('active');
    });

    // Toggle submenus
    $('.has-submenu > .menu-item').on('click', function (e) {
        e.preventDefault();
        var $submenu = $(this).next('.submenu');
        $submenu.toggleClass('open');
        $(this).parent().toggleClass('open');
    });

    // Close sidebars when clicking outside on mobile
    $(document).on('click', function (e) {
        if ($(window).width() <= 768) {
            if (!$sidebar.is(e.target) && $sidebar.has(e.target).length === 0 &&
                !$toggleSidebar.is(e.target) && $sidebar.hasClass('mobile-open')) {
                $sidebar.removeClass('mobile-open');
                $overlay.removeClass('active');
            }

            if (!$userSidebar.is(e.target) && $userSidebar.has(e.target).length === 0 &&
                !$userProfile.is(e.target) && $userSidebar.hasClass('open')) {
                $userSidebar.removeClass('open');
                $overlay.removeClass('active');
            }
        }
    });

    // Highlight active menu item including parent main menu
    // var currentUrl = window.location.href;
    var currentUrl = MAIN_PATH;
    $('.sidebar .menu-item, .sidebar .submenu-item').each(function () {
        if (this.href === currentUrl) {
            $(this).addClass('active');

            // If inside submenu, open parent submenu and mark main menu active
            var $parentSubmenu = $(this).closest('.submenu');
            if ($parentSubmenu.length) {
                $parentSubmenu.addClass('open');
                $parentSubmenu.prev('.menu-item').addClass('active');
                $parentSubmenu.closest('.has-submenu').addClass('open');
            }
        }
    });
});

//#region Sidebar

// $(document).ready(function () {
//     // Use event delegation to handle dropdowns inside scrollBody
//     $('.table-responsive').on('show.bs.dropdown', '.dropdown', function () {
//         $(this).closest('.dataTables_scrollBody').css("overflow", "visible");
//         $(this).closest('.table-responsive').css("overflow", "visible");
//         $(this).closest('.table-responsive').css("overflow-x", "visible");
//     });

//     $('.table-responsive').on('hide.bs.dropdown', '.dropdown', function () {
//         $(this).closest('.dataTables_scrollBody').css("overflow", "auto");
//         $(this).closest('.table-responsive').css("overflow", "auto");
//         $(this).closest('.table-responsive').css("overflow-x", "auto");
//         console.log(this);

//     });
// })

// Hide ele
$(document).on("click", ".target-element-to-hide", function () {
    let showTarget = $(this).data("show-target"),
        hideTarget = $(this).data("hide-target");
    console.log(this);

    $(showTarget).removeClass("d-none");
    $(hideTarget).addClass("d-none");
});
