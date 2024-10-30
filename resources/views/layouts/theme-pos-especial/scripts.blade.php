        <script>
        $(function () {
          $('.example-popover').popover({
            container: 'body'
          })
        })   
        </script>
        
        <script>
     
            // Obten el botè´—n por su ID
            var boton = document.getElementById("miBoton");
        
            // Agrega un evento "click" al botè´—n
            boton.addEventListener("click", function() {
              alert("Hiciste clic en el botè´—n");
            });
          </script>
        
        </script>
        
        
		<!-- jQuery -->
        <script src="{{ asset('assets/pos/js/jquery-3.6.0.min.js') }}"></script>

        <!-- Feather Icon JS -->
		<script src="{{ asset('assets/pos/js/feather.min.js') }}"></script>

		<!-- Slimscroll JS -->
		<script src="{{ asset('assets/pos/js/jquery.slimscroll.min.js') }}"></script>

		<!-- Datatable JS -->
		<script src="{{ asset('assets/pos/js/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('assets/pos/js/dataTables.bootstrap4.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="{{ asset('assets/pos/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Select2 JS -->
		<script src="{{ asset('assets/pos/plugins/select2/js/select2.min.js') }}"></script>

        <!-- Select2 JS -->
		<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/js/custom-select.js') }}"></script>


		<!-- Sweetalert 2 -->
		<script src="{{ asset('pos/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
		
        <script src="{{ asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
		<script src="{{ asset('assets/pos/plugins/sweetalert/sweetalerts.min.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('assets/pos/js/script.js') }}"></script>
		
		<script src="{{ asset('assets/js/apps/notes.js')}}"></script>
		
		        <!-- Mask JS -->
		<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
		<script src="{{ asset('assets/plugins/toastr/toastr.js') }}"></script>
	
	       	<!-- Wizard JS -->
		<script src="{{ asset('assets/pos/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
		<script src="{{ asset('assets/pos/plugins/twitter-bootstrap-wizard/prettify.js') }}"></script>
		<script src="{{ asset('assets/pos/plugins/twitter-bootstrap-wizard/form-wizard.js') }}"></script>
	
		
<script src="{{ asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
		
		
<!-- Date Range Picker -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/pos/js/daterangepicker/daterangepicker.css') }}" />

<!-- Moment.js -->
<script type="text/javascript" src="{{ asset('assets/pos/js/daterangepicker/moment.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/pos/js/daterangepicker/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/pos/js/daterangepicker/daterangepicker.js') }}"></script>



<script>
    $(document).ready(function() {
        App.init();
    });
</script>


<script>
    $(document).ready(function() {
        $('#date-range-picker').daterangepicker({
            startDate: moment().startOf('year'),
            endDate: moment(),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Esta Semana': [moment().startOf('week'), moment().endOf('week')],
                'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Rango Personalizado',
                daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1,
                format: 'DD/MM/YYYY' // Establece el formato de fecha deseado
            }
        });

        // Agregar el controlador de eventos para el evento apply.daterangepicker
        $('#date-range-picker').on('apply.daterangepicker', function(ev, picker) {
            // Emite un evento de Livewire con las fechas seleccionadas
            Livewire.emit('FechaElegida', picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
        });
        
        Livewire.on('set-fecha', function (fecha1,fecha2) {
   
           $('#date-range-picker').daterangepicker({
            startDate: moment().startOf('year'),
            endDate: moment(),
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Esta Semana': [moment().startOf('week'), moment().endOf('week')],
                'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Rango Personalizado',
                daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1,
                format: 'DD/MM/YYYY' // Establece el formato de fecha deseado
            }
        });
    
    });
    });
</script>

<script>
    function noty(msg, option = 1)
    {
        Snackbar.show({
            text: msg.toUpperCase(),
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: option == 1 ? '#3b3f5c' : '#e7515a',
            pos: 'top-right'
        });
    }
    
    function MsgError(msg1,msg2){
                swal({
                title: msg1,
                text: msg2,
                type: 'warning',
                confirmButtonColor: '#3B3F5C',
            })
    }
    
    
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('global-msg', msg => {
            noty(msg)
        });
        
        window.livewire.on('msg-error', msg => {
        swal({
                title: 'ATENCION',
                text: msg,
                type: 'warning',
                confirmButtonColor: '#3B3F5C',
            })
        });
        
        
                
        window.livewire.on('msg-error-afip', msg => {

            
            swal({
            	title: '!Algo salio mal!',
            	text: msg,
            	type: 'warning',
            	showCancelButton: true,
            	cancelButtonText: 'Cerrar',
            	cancelButtonColor: '#fff',
            	confirmButtonColor: '#3B3F5C',
            	confirmButtonText: 'Obtener ayuda'
            	}).then(function(result) {
            	if (result.value) {
            		 window.location.href = '/ayuda';
            		swal.close()
            	}
            })
                		
        });
        
        
    })


</script>



<script>

	function ConfirmAccionEnLote() {
    
   
    var id_accion = $('#id_accion').val();
    if(id_accion == 0) { var msg = 'ELIMINAR';} else { var msg = 'RESTAURAR';}
    
    
		swal({
			title: 'CONFIRMAR',
			text: '¬øCONFIRMAS '+msg+' LOS ITEMS SELECCIONADOS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				AccionProductos();
				swal.close()
			}

		})
	}
	
function AccionProductos() {

  const ids = [];

  $('.mis-checkboxes').each(function() {
    if($(this).hasClass('mis-checkboxes')) {
      
      if($(this).is(':checked')) {
      ids.push($(this).attr('tu-attr-id'));    
      }
       
    }
  });
  
  var id_accion = $('#id_accion').val();
  if(id_accion == 0) { var msg = 'Eliminar';} else { var msg = 'Restaurar';}
     
  window.livewire.emit('accion-lote', ids , id_accion);

document.querySelectorAll('.mis-check_todos').forEach(function(checkElement) {
checkElement.checked = false;
});
  
}    

function CheckTodosLote() {

	     if($(".check_todos").is(":checked")) {
		 	document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = true;
            });
          }else{
			document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = false;
            });
         }
         
}
</script>
<script>
    document.getElementById("caja-abrir").addEventListener("click", btnDisable);

    function btnDisable() {
      document.getElementById("caja-abrir").disabled = true;
    }
</script>

<script>
  function  MejorarPlan() {
    
    $miCheckbox = document.querySelector("#checkbox"),
    
     		swal({
			title: 'CAMBIO DE PLAN',
			text: 'ESTA FUNCIONALIDAD NO SE ENCUENTRA HABILITADA EN ESTE PLAN',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'CERRAR',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'MEJORAR PLAN'
		}).then(function(result) {
			if (result.value) {
				$miCheckbox.checked = false;
				swal.close()
			}

		}) 
  }
</script>

<!-- APARTIR DE ACA SON LOS SCRIPTS DE LO VIEJO -->

<!--  BEGIN CUSTOM SCRIPTS FILE  -->
<script src="{{ asset('assets/js/scrollspyNav.js')}} "></script>
<script src="{{ asset('plugins/select2/select2.min.js')}} "></script>
<script src="{{ asset('plugins/select2/custom-select2.js')}} "></script>
<!--  BEGIN CUSTOM SCRIPTS FILE  -->

<script src="{{ asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
<script src="{{ asset('assets/js/apps/notes.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
var ss = $(".basic").select2({
    tags: true,
});
</script>

<script>
    function noty(msg, option = 1)
    {
        Snackbar.show({
            text: msg.toUpperCase(),
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: option == 1 ? '#3b3f5c' : '#e7515a',
            pos: 'top-right'
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('global-msg', msg => {
            noty(msg)
        });
        
                
//10-12

        window.livewire.on('modal-agregar-cliente', msg => {
           $('#ModalAgregarCliente').modal('show')
        });
        window.livewire.on('modal-agregar-cliente-hide', msg => {
           $('#ModalAgregarCliente').modal('hide')
           noty(msg)
        });
        window.livewire.on('modal-agregar-proveedor', msg => {
           $('#ModalAgregarProveedor').modal('show')
        });        
        window.livewire.on('modal-agregar-proveedor-hide', msg => {
           $('#ModalAgregarProveedor').modal('hide')
           noty(msg)
        });                
        
    })


</script>


<script src="{{ asset('plugins/flatpickr/flatpickr.js')}}"></script>
<script src="{{ asset('plugins/file-upload/file-upload-with-preview.min.js')}}"></script>


@livewireScripts
