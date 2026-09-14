(function ($) {
	"use strict";

	var rangeTwoKeypress = function () {
		if ($("#keypress-two-val").length > 0) {
			var skipSlider = document.getElementById("keypress-two-val");
			var skipValues = [
				document.getElementById("ip-val-lower"),
				document.getElementById("ip-val-upper"),
			];

			noUiSlider.create(skipSlider, {
				start: [15, 1200000],
				connect: true,
				tooltips: [true, true],
				step: 1,
				range: {
					min: 15,
					max: 4000000,
				},
				format: {
					from: function (value) {
						return parseInt(value);
					},
					to: function (value) {
						return parseInt(value);
					},
				},
			});

			skipSlider.noUiSlider.on("update", function (val, e) {
				if (e === 1) {
					skipValues[e].value = "$" + val[e];
				} else {
					skipValues[e].value = val[e] + "%";
				}
			});
		}
	};

	var rangeTwoKeypress1 = function () {
		if ($("#keypress-two-val-1").length > 0) {
			var skipSlider = document.getElementById("keypress-two-val-1");
			var skipValues = [
				document.getElementById("ip-val-lower1"),
				document.getElementById("ip-val-upper1"),
			];

			noUiSlider.create(skipSlider, {
				start: [0, 5],
				connect: true,
				tooltips: [true, true],
				step: 1,
				range: {
					min: 0,
					max: 7,
				},
				format: {
					from: function (value) {
						return parseInt(value);
					},
					to: function (value) {
						return parseInt(value);
					},
				},
			});

			skipSlider.noUiSlider.on("update", function (val, e) {
				if (e === 1) {
					skipValues[e].value = val[e] + " year";
				} else {
					skipValues[e].value = "Tenor";
				}
			});
		}
	};

	$(function () {
		rangeTwoKeypress();
		rangeTwoKeypress1();
	});
})(jQuery);
