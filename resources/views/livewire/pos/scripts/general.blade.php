<script>

	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('add-product', msg => {
			$('#ModalProductos').modal('show')
		});
		
	// 27-12-2023
	
		window.livewire.on('add-product-hide', msg => {
			$('#ModalProductos').modal('hide')
		});

		window.livewire.on('imprimir-nueva-ventana', url => {
                window.open(url, '_blank');
            });

      
    //  

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

// 27-12-2023

	function doAction(barcode)
	{
		swal({
			title: '',
			text: 'EL PRODUCTO NO ESTA REGISTRADO',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'CANCELAR',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'AGREGAR'
		}).then(function(result) {
			if(result.value){
              window.livewire.emit('set-barcode',barcode)
			  swal.close()
            }

        })
	}

</script>
