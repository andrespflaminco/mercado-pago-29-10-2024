  <script>
    function mostrarOcultarDiv(index) {
      var div = document.querySelector(".miDiv:nth-child(" + index + ")");
      if (div.style.display === "none") {
        div.style.display = "block";
      } else {
        div.style.display = "none";
      }
    }
  </script>


<script>
                	 function ConfirmConfiguracion(id) {
                        var accion = id;
                        if(accion == 1) {
                        var msg = "ESTO ACTUALIZARA LOS PRECIOS INTERNOS CON EL VALOR DE LOS COSTOS";    
                        } else {
                        var msg = "";    
                        }
                		swal({
                			title: 'CONFIRMAS ACTUALIZAR ',
                			text: msg,
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cerrar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('UpdateConfiguracion', id)
                				swal.close()
                			}
                
                		})
                	}                   					 
                	function Confirm(id) {
                
                		swal({
                			title: 'CONFIRMAR',
                			text: '多CONFIRMAS ELIMINAR EL REGISTRO?',
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cerrar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('deleteRow', id)
                				swal.close()
                			}
                
                		})
                	}   
                	
                	
                	function ElegirTipoProducto(){
                	swal({
        				title: 'IMPORTANTE',
        				text: 'DEBE ELEGIR EL TIPO DE PRODUCTO',
        				type: 'warning',
        				confirmButtonColor: '#3B3F5C'
        			})
                	}                	
                	
                	function FaltaVariacion(){
                	swal({
        				title: 'IMPORTANTE',
        				text: 'NO PUEDE GUARDAR UN PRODUCTO VARIABLE SIN VARIACIONES ASOCIADAS',
        				type: 'warning',
        				confirmButtonColor: '#3B3F5C'
        			})
                	}
					</script>
					
					
					

<script>
	document.addEventListener('DOMContentLoaded', function() {
	    
	 function ClickChatGpt() {
        alert("click");
        }

        window.livewire.on('modal-lista-precios-show', msg => {
			$('#theModalListaPrecios').modal('show')
			$('#theModal').modal('hide')
		});
		
		window.livewire.on('modal-lista-precios-hide', msg => {
			$('#theModalListaPrecios').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});
		
		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
			noty(msg)
		});

        // Exportadores 
        
		window.livewire.on('modal-export-listas-show', msg => {
			$('#ExportarLista').modal('show')
		});
		
		window.livewire.on('modal-export-listas-hide', msg => {
			$('#ExportarLista').modal('hide')
		});


		window.livewire.on('modal-export-stocks-show', msg => {
			$('#ExportarStock').modal('show')
		});
		
		window.livewire.on('modal-export-stocks-hide', msg => {
			$('#ExportarStock').modal('hide')
		});

        //
		window.livewire.on('modal-stock', msg => {
			$('#theModalStock').modal('show')
		});
		
		window.livewire.on('modal-stock-hide', msg => {
			$('#theModalStock').modal('hide')
		});

		window.livewire.on('category-added', msg => {
			$('#Categoria').modal('hide')
			noty(msg)
		});

		// 6-6-2024
		window.livewire.on('modal-marca-show', msg => {
			$('#Marcas').modal('show')
		});
        //
        
  
		window.livewire.on('marca-added', msg => {
			$('#Marcas').modal('hide')
			noty(msg)
		});
		
		window.livewire.on('almacen-added', msg => {
			$('#Almacen').modal('hide')
			noty(msg)
		});


		window.livewire.on('category-hide', msg => {
			$('#Categoria').modal('hide')
		});

		window.livewire.on('almacen-hide', msg => {
			$('#Almacen').modal('hide')
		});


		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')

			noty(msg)
		});
		
		window.livewire.on('mensajes', msg => {
		noty(msg, 2)
		});
		
		window.livewire.on('product-deleted', msg => {
			noty(msg)
		});

		window.livewire.on('exportar-lista', msg => {
			$('#ExportarLista').modal('show')
		});
				
		window.livewire.on('exportar-lista-hide', msg => {
			$('#ExportarLista').modal('hide')
		});
		
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-imagen-show', msg => {
			$('#Imagenes').modal('show')
		});

		window.livewire.on('modal-imagen-hide', msg => {
			$('#Imagenes').modal('hide')
		});
		
		window.livewire.on('modal-proveedor-show', msg => {
			$('#Proveedor').modal('show')
		});
		
		
		window.livewire.on('proveedor-added', msg => {
			$('#Proveedor').modal('hide')
			noty(msg)
		});
		
				
		window.livewire.on('proveedor-hide', msg => {
			$('#Proveedor').modal('hide')
		});

		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-cambio-sucursal', msg => {
			$('#ModalCambioSucursal').modal('show')
		});
		
		window.livewire.on('hide-cropp', msg => {
			$('#ModalCroppr').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})

		window.livewire.on('credenciales-invalidas', id => {

			swal({
				title: 'ATENCION',
				text: 'LAS CREDENCIALES DE WOOCOMMERCE ASOCIADAS SON INVALIDAS.',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'IR A CONFIGURAR'
			}).then(function(result) {
			    window.location.href = '/woocommerce';
				swal.close()
			})

		});	
		
		window.livewire.on('producto-repetido', selected_id => {

                        
                		swal({
                			title: 'EXISTEN PRODUCTOS CON EL MISMO NOMBRE',
                			text: '¿DESEAS CONTINUAR DE TODOS MODOS?',
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cancelar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('Swicth', selected_id)
                				swal.close()
                			}
                
                		})

		});	
		
		
		window.livewire.on('msg-error', msg => {
			swal({
				title: 'ATENCION',
				type: 'warning',
				text: msg,
				confirmButtonColor: '#3B3F5C'
			})
		});	
		
		window.livewire.on('cambiar-tipo-producto', id => {

			swal({
				title: 'CONFIRMAR',
				text: 'CONFIRMAS CAMBIAR EL TIPO DE PRODUCTO? ESTO ELIMINARA LAS VARIACIONES ASOCIADAS',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cancelar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value == true) {
				  //  alert(result.value)
					window.livewire.emit('CambiarProductoTipo', id)
					swal.close()
				} else {
				    var id = 1;
				    window.livewire.emit('VolverProductotipo', id)
				//	swal.close()
				}

			})

		});	
		

		
		
		window.livewire.on('confirm-eliminar', id => {

			swal({
				title: 'CONFIRMAR',
				text: '多CONFIRMAS ELIMINAR LOS REGISTROS?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('ConfirmCheck', id)
					swal.close()
				}

			})

		});



	});


	function RestaurarProducto(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL PRODUCTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarProducto', id)
        swal.close()
      } 

    })
  }




		function ConfirmVariacion(id) {

			swal({
				title: 'CONFIRMAR',
				text: '¿CONFIRMAS ELIMINAR LA VARIACION?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('deleteVariacion', id)
					swal.close()
				}

			})
		}

</script>

<script>
    function changeClass() {
    var modal = document.getElementById('ExportarStock');

    // Verifica si el modal tiene la clase 'show'
    if (modal.classList.contains('show')) {
        // Remueve la clase 'show' y agrega la clase 'hide'
        modal.classList.remove('show');
        modal.classList.add('hide');
    } else {
        // Remueve la clase 'hide' y agrega la clase 'show'
        modal.classList.remove('hide');
        modal.classList.add('show');
    }
}

</script>
