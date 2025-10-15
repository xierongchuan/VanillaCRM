import "./bootstrap";

import $ from "jquery";

//Bootstrap 5
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap-icons/font/bootstrap-icons.min.css";

// Импорт библиотеки Slick Carousel
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import "slick-carousel";

$(document).ready(function () {
	$("#worker_department").on("change", function () {
		let depValue = $(this).val();
		$.ajax({
			url:
				"/company/" +
				$("#company_id").val() +
				"/department/" +
				depValue +
				"/posts",
			type: "POST",
			dataType: "json",
			headers: {
				"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
			},
			success: function (data) {
				$("#worker_post").empty();
				$.each(data, function (index, item) {
					$("#worker_post").append(
						'<option value="" selected>Select Post</option>'
					);
					if ($("#worker_post_id").val() === item.id) {
						$("#worker_post").append(
							'<option value="' +
								item.id +
								'" selected>' +
								item.name +
								"</option>"
						);
					} else {
						$("#worker_post").append(
							'<option value="' + item.id + '">' + item.name + "</option>"
						);
					}
				});
			},
		});
	});

	$(".perm_panel_switch").click(function () {
		let panelId = $(this).attr("panel"); // Получаем значение атрибута panel

		// Используем slideUp для скрытия соответствующей панели
		$("#" + panelId).slideToggle(400);
	});

	$(document).ready(function () {
		$(".delete-user").on("click", function (event) {
			event.preventDefault();
			event.stopPropagation();

			var userId = $(this).data("id");
			var userName = $(this).data("name");
			var link = $(this).attr("href");

			var confirmed = confirm("Вы точно хотите удалить " + userName + "?");

			if (confirmed) {
				window.location.href = link;
			}
		});
	});
});

// ===================================================================
// STAGE 4: Slick Carousel code commented out - replaced with Vue 3
// ===================================================================
// The carousel functionality is now handled by Vue 3 components
// See: public/js/components/ReportsCarousel.js
// And: resources/views/home.blade.php (Vue components integration)
// ===================================================================

/*
$(document).ready(function () {
	// Инициализация слайдера
	$('[id^="perm_panel_"]').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false, // Отключаем стрелочки
		dots: false, // Отключаем точки
		adaptiveHeight: true,
	});

	// Обработка нажатия кнопок
	$(".slider_next_button").on("click", function () {
		// Получаем значение атрибута data-section-id
		var slideId = $(this).attr("section-id");

		// console.log("Slide ID=" + slideId + " Next");

		// Переходим к следующему слайду с указанным ID
		$("#perm_panel_" + slideId).slick("slickNext");
	});

	// Обработка нажатия кнопок
	$(".slider_prev_button").on("click", function () {
		// Получаем значение атрибута data-section-id
		var slideId = $(this).attr("section-id");

		// console.log("Slide ID=" + slideId + " Prev");

		// Переходим к следующему слайду с указанным ID
		$("#perm_panel_" + slideId).slick("slickPrev");
	});
});
*/
