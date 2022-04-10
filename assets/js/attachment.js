(function( $ ) {
	'use strict';

	$('#attach-form').submit(function(e){
		e.preventDefault();

		const fd = new FormData();
        const files = $('#file')[0].files;
		const id_order = $('#order').val();

		if (files.length <= 0 ) {
			alert('Tienes que seleccionar algún archivo');
			return;
		}

		const size = (files[0].size / 1024 / 1024 ).toFixed(2);
		if ( size > 2){
			alert(`Tu archivo pesa ${size}MB. No puedes subir archivos mayores a 2MB`);
			return;
		}

		fd.append('file',files[0]);
		fd.append('action', 'dcms_ajax_add_file');
		fd.append('nonce', dcmsAttach.nonce);
		fd.append('id_order', id_order);

		$.ajax({
			url: dcmsAttach.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: fd,
			contentType: false,
			processData: false,
			beforeSend: function(){
				$('#message').text('Enviando...');
			},
			success: function(res){
				$('#message').text(res.message);
			}
		});


	});

})( jQuery );

