import './bootstrap';

import $ from 'jquery';

//Bootstrap 5
import 'bootstrap/dist/js/bootstrap.bundle.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css';

$(document).ready(function() {
	$('.perm_panel_switch').click(function() {
		var panelId = $(this).attr('panel'); // Получаем значение атрибута panel

		// Используем slideUp для скрытия соответствующей панели
		$('#' + panelId).slideToggle();
	});

	$('#repost_xlsx_checkbox').change(function(){
		if($(this).is(':checked')) {
			$('.repost_xlsx_required_inputs').prop('required', false); // Устанавливаем атрибут required
			// $('.repost_xlsx_required_inputs').prop('disabled', false); // Включаем текстовый инпут
		} else {
			$('.repost_xlsx_required_inputs').prop('required', true); // Убираем атрибут required
			// $('.repost_xlsx_required_inputs').prop('disabled', true); // Выключаем текстовый инпут
		}
	});
});
