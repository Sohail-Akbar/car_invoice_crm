$(window).on("scroll", function () {
    let navbarPosition = $("nav.navbar").offset(),
        navbar = $("nav.navbar");

    if (navbarPosition.top > 20) {
        navbar.addClass('navbar-fill');
    } else {
        navbar.removeClass('navbar-fill');
    }
});