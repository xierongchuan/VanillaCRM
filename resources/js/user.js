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
});
