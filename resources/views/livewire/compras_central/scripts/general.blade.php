<script>

	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('add-product', msg => {
			$('#ModalProductos').modal('show')
		});


		$('.tblscroll').niceScroll({
			cursoscolor: "#515365",
			cursorwidth: "30px",
			background: "rgba(20,20,20,0.3)",
			cursorborder: "0px",
			cursorborderradius:3

		})

	})



	function Confirm(id, eventName, text)
	{
		swal({
			title: 'CONFIRMAR',
			text: text,
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if(result.value){
				window.livewire.emit(eventName, id)
				swal.close()
			}

		})
	}


	function doAction(barcode)
	{
		swal({
			title: '',
			text: '¿QUIERES REGISTRAR EL PRODUCTO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'NO',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'SI'
		}).then(function(result) {
			if(result.value){
               //$('#theModal').modal('show')
							 window.livewire.emit('set-barcode', barcode)
                swal.close()
            }

        })
	}

</script>
