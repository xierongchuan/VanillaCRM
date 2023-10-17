import './bootstrap';

import $ from 'jquery';

//Bootstrap 5
import 'bootstrap/dist/js/bootstrap.bundle.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css';

$(document).ready(function(){
	$('#worker_department').on('change', function(){
		let depValue = $(this).val();
		$.ajax({
			url: '/company/'+$('#company_id').val()+'/department/'+depValue+'/posts',
			type: 'POST',
			dataType: 'json',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(data){
				$('#worker_post').empty();
				$.each(data, function(index, item){
					$('#worker_post').append('<option value="" selected>Select Post</option>');
					if($('#worker_post_id').val() === item.id) {
						$('#worker_post').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
					} else {
						$('#worker_post').append('<option value="'+item.id+'">'+item.name+'</option>');
					}
				});
			}
		});
	});

	$('.perm_panel_switch').click(function() {
		let panelId = $(this).attr('panel'); // Получаем значение атрибута panel

		// Используем slideUp для скрытия соответствующей панели
		$('#' + panelId).slideToggle(400);
	});
});
