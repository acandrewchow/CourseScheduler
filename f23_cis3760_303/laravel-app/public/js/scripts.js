$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    // Navbar functionality
    const mobileMenuButton = $("#mobile-menu-button");
    const closeMobileMenuButton = $("#close-mobile-menu");
    const mobileMenu = $("#mobile-menu");

    const screenMd = 768; // md screen break point

    // Enables menu
    mobileMenuButton.click(function () {
        mobileMenu.toggleClass("hidden");
    });

    // Hides menu when clicked
    closeMobileMenuButton.click(function () {
        mobileMenu.addClass("hidden");
    });

    // Changes the mobile menu on screen size
    $(window).resize(function () {
        if ($(window).width() >= screenMd) {
            mobileMenu.addClass("hidden");
        }
    });
});
