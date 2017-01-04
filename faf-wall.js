$(document).ready(function () {
    var wall = new Freewall( wallId );
    wall.reset({
        selector: ".cell",
        animate: false,
        cellW: wallHeight,
        cellH: wallHeight,
        gutterX: 20,
        gutterY: 20,
        onResize: function () {
            wall.refresh();
        }
    });
    wall.fitWidth();
    $(window).trigger("resize");

    var gallery = $(".free-wall .free-wall-link").simpleLightbox({
        animationSlide: false,
        close: false,
        captions: true,
        captionSelector: "self",
        spinner: false,
        showCounter: false,
        nav: false
    });

    $(document).on("click", ".sl-image img", function () {
        gallery.next();
    });
});