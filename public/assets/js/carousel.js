
if ($(".tf-swiper").length > 0) {
    var spacing = $(".tf-swiper").data("space-between");
    var preview = $(".tf-swiper").data("preview");
    var tablet = $(".tf-swiper").data("tablet");
    var desktop = $(".tf-swiper").data("desktop");
    var swiper4 = new Swiper(".tf-swiper", {
        speed: 1500,
        slidesPerView: preview,
        loop: false,
        spaceBetween: spacing,
        observer: true,
        observeParents: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            1024: {
                slidesPerView: desktop,
            },
            768: {
                slidesPerView: tablet,
            },
        },
    });
}

if ($(".tf-swiper2").length > 0) {
    var spacing = $(".tf-swiper2").data("space-between");
    var preview = $(".tf-swiper2").data("preview");
    var tablet = $(".tf-swiper2").data("tablet");
    var desktop = $(".tf-swiper2").data("desktop");
    var initial_slide = $(".tf-swiper2").data("initial-slide");
    var swiper4 = new Swiper(".tf-swiper2", {
        speed: 1500,
        initialSlide: initial_slide,
        slidesPerView: preview,
        loop: false,
        spaceBetween: spacing,
        observer: true,
        observeParents: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            1024: {
                slidesPerView: desktop,
            },
            768: {
                slidesPerView: tablet,
            },
        },
    });
}
