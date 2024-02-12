import "./bootstrap";

import $ from "jquery";

//Bootstrap 5
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap-icons/font/bootstrap-icons.min.css";

$(document).ready(function () {
	$(".perm_panel_switch").click(function () {
		var panelId = $(this).attr("panel"); // Получаем значение атрибута panel

		// Используем slideUp для скрытия соответствующей панели
		$("#" + panelId).slideToggle();
	});

	$("#repost_xlsx_checkbox").change(function () {
		if ($(this).is(":checked")) {
			$(".repost_xlsx_required_inputs").prop("required", false); // Устанавливаем атрибут required
			// $('.repost_xlsx_required_inputs').prop('disabled', false); // Включаем текстовый инпут
		} else {
			$(".repost_xlsx_required_inputs").prop("required", true); // Убираем атрибут required
			// $('.repost_xlsx_required_inputs').prop('disabled', true); // Выключаем текстовый инпут
		}
	});

	function recalculatePercentages() {
		let total = 0;

		// Считаем общую сумму
		$(".repost_xlsx_required_inputs").each(function () {
			total += parseFloat($(this).val());
		});

		// Рассчитываем и выводим проценты
		$(".repost_xlsx_required_inputs").each(function (index) {
			let workerMonth = parseFloat($(this).val());
			let percent = ((workerMonth / total) * 100).toFixed(1) + " %";
			$("#report_worker_percent_" + (index + 1)).text(percent);
		});
	}

	// При изменении инпута, пересчитываем проценты
	$(".repost_xlsx_required_inputs").on("input", function () {
		recalculatePercentages();
	});

	// Вызываем функцию один раз при загрузке страницы
	recalculatePercentages();
});
