var j = jQuery.noConflict();

var vmattach = new Vue({
    el: '#attachment-container',
    data: {
        results: [],
        status: 0,
        loadingFiles: true,
		uploadingFile: null,
		message:''
    },
    created: function(){
            this.loadListFiles()
    },
    methods: {
        loadListFiles() {
            this.loadingFiles = true

            j.ajax({
                type:"post",
                url: dcmsAttach.ajaxurl,
                dataType: 'json',
                data: {
					id_order: j('#order').val(),
                    action : 'dcms_ajax_get_files',
                    nonce : dcmsAttach.nonce,
                },
                success:function(res){
                    if ( res.status == 0 ) {
                        console.log(res)
                        return
                    }
                    vmattach.status = res.status
                    vmattach.loadingFiles = false
                    vmattach.results = res.data
                }
            })
        },
		onSubmit(){

			const fd = new FormData()
			const files = j('#file')[0].files
			const id_order = j('#order').val()

			if (files.length <= 0 ) {
				alert('Tienes que seleccionar algún archivo')
				return
			}

			const size = (files[0].size / 1024 / 1024 ).toFixed(2)
			if ( size > 2){
				alert(`Tu archivo pesa ${size}MB. No puedes subir archivos mayores a 2MB`)
				return;
			}

			this.uploadingFile = true
			this.message = ''
			j('#file').prop('disabled', true)
			j('#submit').prop('disabled', true)

			fd.append('file',files[0])
			fd.append('action', 'dcms_ajax_add_file')
			fd.append('nonce', dcmsAttach.nonce)
			fd.append('id_order', id_order)

			j.ajax({
				url: dcmsAttach.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: fd,
				contentType: false,
				processData: false,
				success: function(res){
					vmattach.status = res.status
					vmattach.uploadingFile = false
					vmattach.message = res.message

					j('#file').prop('disabled', false).val('')
					j('#submit').prop('disabled', false)

					if ( vmattach.status == 1 ){
						vmattach.loadListFiles()
					}

				}
			});
		}
    }
})
