/*
 * Back Page
 * show pass
 * otp input
 * selectImages
 * fillterCheck
 * favoriteActive
 * clear text
 * tab slide
 * datePicker
 * activeSuggest
 * changeValue
 * uploadFlie
 * handleMessage
 * Modal Second
 * optionLink
 * RTL
 * preloader
 */
(function ($) {
    "use strict";

    /* Back Page
     ------------------------------------------------------------------------------------- */
    var backPage = function () {
        $(".back-btn").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            window.history.go(-1);
        });
    };

    /* show pass
    ------------------------------------------------------------------------------------- */
    var showPass = function () {
        $(".show-pass").on("click", function () {
            $(this).toggleClass("active");
            if ($(".password-field").attr("type") == "password") {
                $(".password-field").attr("type", "text");
            } else if ($(".password-field").attr("type") == "text") {
                $(".password-field").attr("type", "password");
            }
        });
        $(".show-pass2").on("click", function () {
            $(this).toggleClass("active");
            if ($(".password-field2").attr("type") == "password") {
                $(".password-field2").attr("type", "text");
            } else if ($(".password-field2").attr("type") == "text") {
                $(".password-field2").attr("type", "password");
            }
        });
    };

    /* otp input
    ------------------------------------------------------------------------------------- */
    var otpInput = function () {
        if ($(".digit-group").length > 0) {
            $(".digit-group")
                .find("input")
                .each(function () {
                    $(this).attr("maxlength", 1);
                    $(this).on("keyup", function (e) {
                        var valNum = $(this).val();
                        var parent = $($(this).parent());

                        if (e.keyCode === 8 || e.keyCode === 37) {
                            var prev = parent.find("input#" + $(this).data("previous"));

                            if (prev.length) {
                                $(prev).select();
                            }
                        } else if (
                            (e.keyCode >= 48 && e.keyCode <= 57) ||
                            (e.keyCode >= 65 && e.keyCode <= 90) ||
                            (e.keyCode >= 96 && e.keyCode <= 105) ||
                            e.keyCode === 39
                        ) {
                            var next = parent.find("input#" + $(this).data("next"));
                            if (!$.isNumeric(valNum)) {
                                $(this).val("");
                                return false;
                            }

                            if (next.length) {
                                $(next).select();
                            } else {
                                if (parent.data("autosubmit")) {
                                    parent.submit();
                                }
                            }
                        }
                    });
                });
        }
    };

    /* selectImages
    ------------------------------------------------------------------------------------- */
    var selectImages = function () {
        if ($(".image-select").length > 0) {
            const selectIMG = $(".image-select");

            selectIMG.find("option").each((idx, elem) => {
                const selectOption = $(elem);
                const imgURL = selectOption.attr("data-thumbnail");
                if (imgURL) {
                    selectOption.attr(
                        "data-content",
                        `<img src="${imgURL}" /> ${selectOption.text()}`
                    );
                }
            });
            selectIMG.selectpicker();
        }
    };

    /* fillterCheck
    ------------------------------------------------------------------------------------- */
    var fillterCheck = function () {
        $(".btn-fillter-active").on("click", function () {
            $(".fillter-item").hide();
            $(".fillter-item").filter(".fillter-active").show();
        });

        $(".btn-all").on("click", function () {
            $(".fillter-item").show();
        });

    };

    /* favoriteActive
    ------------------------------------------------------------------------------------- */

    var favoriteActive = function () {
        var $btnAll = $(".btn-all");
        var $btnFilter = $(".btn-fillter-active");
        var $items = $(".list-fillter-item .profile-contact");

        var showFavorites = function () {
            $items.hide();
            $items.filter(".fillter-active").show();
        };

        var showAll = function () {
            $items.show();
        };

        $btnAll.on("click", function (e) {
            e.preventDefault();
            $btnAll.addClass("active");
            $btnFilter.removeClass("active");
            showAll();
        });

        $btnFilter.on("click", function (e) {
            e.preventDefault();
            $btnFilter.addClass("active");
            $btnAll.removeClass("active");
            showFavorites();
        });

        if ($btnFilter.hasClass("active")) {
            showFavorites();
        } else {
            showAll();
        }

        $(".favorite-btn").on("click", function (e) {
            e.preventDefault();
            $(this).closest(".profile-contact").toggleClass("fillter-active");
        });

        $('.tf-btn-add-ticket').on('click', function (e) {
            e.preventDefault();
            $('.list-btn-ticket').toggleClass('active');
        });
    };

    /* clear text
    ------------------------------------------------------------------------------------- */
    var clearInput = function () {
        $(".delect-btn").on("click", function () {
            $(".clear-ip").val("");
        });
    };

    /* tab slide 
    ------------------------------------------------------------------------------------- */
    var tabSlide = function () {
        $(".tab-slide").each(function () {
            var $wrap = $(this);
            var $items = $wrap.find("li");
            var $effect = $wrap.find(".item-slide-effect");

            var isRTL = $("body").hasClass("rtl");

            function update($el) {
                var width = $el.outerWidth();
                var left = $el.position().left;
                var transformValue;

                if (isRTL) {
                    var containerWidth = $wrap.width();
                    var rightPosition = containerWidth - left - width;
                    transformValue = "translateX(" + rightPosition + "px)";
                } else {
                    transformValue = "translateX(" + left + "px)";
                }

                $effect.css({
                    width: width,
                    transform: transformValue
                });
            }
2
            function waitForVisible(callback) {
                function check() {
                    if ($wrap.is(":visible") && $wrap.outerWidth() > 0) {
                        callback();
                    } else {
                        requestAnimationFrame(check);
                    }
                }
                check();
            }

            waitForVisible(function () {
                var $active = $items.filter(".active");
                if (!$active.length) $active = $items.first().addClass("active");
                update($active);
            });

            $items.on("click", function () {
                $items.removeClass("active");
                $(this).addClass("active");
                update($(this));
            });

            $(window).on("resize", function () {
                update($items.filter(".active"));
            });
        });
    };

    /* datePicker 
    ------------------------------------------------------------------------------------- */

    var datePicker = function () {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        if ($("#datepicker1").length > 0) {
            $("#datepicker1").datepicker({
                firstDay: 1,
                dateFormat: "dd/mm/yy",
                autoclose: true,
            });
        }
        if ($("#datepicker2").length > 0) {
            $("#datepicker2").datepicker({
                firstDay: 1,
                dateFormat: "dd/mm/yy",
            });
        }
        if ($("#datepicker3").length > 0) {
            $("#datepicker3").datepicker({
                firstDay: 1,
                dateFormat: "dd/mm/yy",
            });
        }
    };

    /* activeSuggest 
    ------------------------------------------------------------------------------------- */

    var activeSuggest = function () {

        $(".tag-money,.accent-box-v5,.item-time").click(function () {
            $(".tag-money.active,.accent-box-v5.active,.item-time.active").removeClass("active");
            $(this).addClass("active");
        });

        $(".item-category").click(function () {
            $(".item-category.active").removeClass("active");
            $(this).addClass("active");
        });

    };

    /* changeValue
    ------------------------------------------------------------------------------------- */

    var changeValue = function () {
        $(".tag-money").on("click", function () {
            var val = $(this).text();
            var str = val.slice(1);
            $(".value_input").val(str);
        });
    };

    /* uploadFlie
    ------------------------------------------------------------------------------------- */

    var uploadFlie = function () {
        $(".btn-update").on("click", function (e) {
            document.getElementById("file-input").click();
        });
        if ($("#file-input").length) {
            document
                .getElementById("file-input")
                .addEventListener("change", function (event) {
                    var file = event.target.files[0];
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        var imgElement = document.getElementById("profile-img");
                        if (imgElement) {
                            imgElement.src = e.target.result;
                        }
                        var filename = document.getElementById("name-file");
                        if (filename) {
                            filename.textContent = file.name;
                        }
                    };

                    if (file) {
                        reader.readAsDataURL(file);
                    }
                });
        }
    };

    /* handleMessage
    ------------------------------------------------------------------------------------- */

    var handleMessage = function () {
        $(".btn-message").on("click", function () {
            var ipMessage = $(".val-message");
            var messValue = ipMessage.val();
            var currentTime = new Date();
            var hours = currentTime.getHours() >= 12 ? "PM" : "AM";
            var realTime =
                (currentTime.getHours() % 12) +
                ":" +
                currentTime.getMinutes() +
                " " +
                hours;

            var domMessage =
                '<div class="bubble bubble-me box-buble-me">' +
                '<div class="content">' +
                '<span class="time text-small">' +
                realTime +
                "</span>" +
                '<p class="text-item text-medium">' +
                messValue +
                "</p>" +
                "</div>" +
                "</div>";

            if (messValue.length > 0) {
                $(".chat-area").append(domMessage);
            }
            window.scrollTo(0, document.body.scrollHeight);
            ipMessage.val("");
        });
        $(".val-message").on("keydown", function (e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                $(".btn-message").click();
            }
        });

    };

    /* Modal Second
    ------------------------------------------------------------------------------------- */
    var clickModalSecond = function () {
        $(".btn-choose-page").click(function () {
            $("#modalPage").modal("show");
        });
        $(".btn-choose-component").click(function () {
            $("#modalComponent").modal("show");
        });
    };

    /* optionLink
    ------------------------------------------------------------------------------------- */

    var optionLink = function () {
        $(".option-link").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            window.location.href = $(this).data("url");
        });
    };

    /* Toggle RTL
    ------------------------------------------------------------------------------------- */

    var RTL = function () {
        var isRTL = $("body").hasClass("rtl") || localStorage.getItem("dir") === "rtl";

        applyDir(isRTL ? "rtl" : "ltr");

        $(".toggle-rtl")
            .off("click")
            .on("click", function () {
                var nextRTL = !$("body").hasClass("rtl");
                localStorage.setItem("dir", nextRTL ? "rtl" : "ltr");
                applyDir(nextRTL ? "rtl" : "ltr");
            });
    }
    function applyDir(dir) {
        var useRTL = dir === "rtl";
        $("html").attr("dir", dir);
        $("body").toggleClass("rtl", useRTL);
        $("#switchRTLTheme").prop("checked", useRTL);
    }

    /* preloader 
    ------------------------------------------------------------------------------------- */
    var preloader = function () {
        setTimeout(function () {
            $(".preload").fadeOut("slow", function () {
                $(this).remove();
            });
        }, 500);
    };

    $(function () {
        backPage();
        showPass();
        otpInput();
        selectImages();
        fillterCheck();
        favoriteActive();
        clearInput();
        tabSlide();
        datePicker();
        activeSuggest();
        changeValue();
        uploadFlie();
        handleMessage();
        clickModalSecond();
        optionLink();
        RTL();
        preloader();
    });
})(jQuery);
