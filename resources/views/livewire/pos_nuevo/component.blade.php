
@if(auth()->check() && auth()->user()->id === 1)
    @php
        return redirect('/users');
    @endphp
@endif

@if($caja_abierta == null)
<div class="row layout-top-spacing">
	<div class="col-sm-12 col-md-12">        
        <!-- Modal -->
       <?php /* 
	    @include('livewire.pos.partials.modal-email') 
        @include('livewire.pos.partials.modal-formulario')
		*/ ?>
        @include('livewire.pos_nuevo.partials.modal-abrir-caja')        
</div>
</div>
@else
<div>
   
    
    @include('common.form-cliente')
   
    @include('livewire.pos_nuevo.buscar-catalogo')
    @include('livewire.pos_nuevo.partials.modal-email')
    @include('livewire.pos_nuevo.partials.modal-formulario')

@if($nro_paso == 1)
<div class="row">
	<div class="col-sm-12 col-md-9">
	@include('livewire.pos_nuevo.partials.header') 
	<div style="background: white; padding:15px; border-radius:6px;" class="col-sm-12 col-md-12">
		<?php /**/?> 
		<?php /**/?>
		
	    @include('livewire.pos_nuevo.partials.barra-busqueda')
        @include('livewire.pos_nuevo.partials.detail')
	</div>
	</div>

<div class="col-sm-12 col-md-3">
        <!-- TOTAL -->
        @include('livewire.pos_nuevo.partials.total')

</div>
</div>
@else
<div class="row">
	<div class="col-sm-12 col-md-9">
	<div style="background: white; padding:15px; border-radius:6px;" class="col-sm-12 col-md-12">
		<?php /**/?> 
		<?php /**/?>
		<div class="row">
		    
		    @include('livewire.pos_nuevo.partials.barra-busqueda-cliente')
		    
		   <h6 style="border-bottom: solid 1px #eee;"><b>Tipo de factura</b></h6>
		   <div class="col-sm-12 col-md-6">
		    @include('livewire.pos_nuevo.partials.tipo-factura')   
		   </div>
		   <div class="col-sm-12 col-md-6">
		   </div>
		   <div class="col-sm-12 col-md-12">
		    @include('livewire.pos_nuevo.partials.metodo-pago')
		   </div> 
		   <div class="col-sm-12 col-md-12">
            @include('livewire.pos_nuevo.partials.metodo-envio')     
		   </div> 
		</div>
		</div>
	</div>

<div class="col-sm-12 col-md-3">
        <!-- TOTAL -->
        @include('livewire.pos_nuevo.partials.total')
  
    </div>
</div>
@endif

        @include('livewire.pos_nuevo.datos-cliente')
        @include('livewire.pos_nuevo.form-agregar-cliente')
		@include('livewire.pos_nuevo.partials.descuentos')
		@include('livewire.pos_nuevo.partials.comentarios')
		<?php /*
		@include('livewire.pos.form-hoja-ruta')
		
		@include('livewire.pos.form-hoja-ruta-nueva')
		@include('livewire.factura.form-pagos')
		@include('livewire.pos.agregar-pago')
		
		*/?>
		@include('livewire.pos_nuevo.partials.modal-pago-dividido')
		@include('livewire.pos_nuevo.cheque')
		
		<?php /*
		@include('livewire.reports.sales-detail2')
		@include('livewire.reports.sales-detail3')
		
		@include('livewire.pos.partials.form-alto')
		*/
		?>
		@include('livewire.pos_nuevo.estado-pedido-pos')
		
	
		<!-- @include('livewire.pos.form') -->		
		<!--Add Lucas -->
		<!-- Buscador de productos-->
		@include('livewire.pos_nuevo.form-products')
		
		@include('livewire.pos_nuevo.form-banco')
		@include('livewire.pos_nuevo.form-metodo-pago')
        @include('livewire.pos_nuevo.partials.imprimir')
		<?php /*
		


		@include('livewire.pos.ver-factura')
		
		@include('livewire.pos.info')
		
		@include('livewire.pos.partials.modal-info-product')
		*/?>
		@include('livewire.pos_nuevo.variaciones')

	</div>


@endif

<input type="hidden" id="completo_formulario" value="{{$completa_formulario->completo_formulario}}" >

<script src="{{ asset('js/keypress.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script>
/*
try{

    onScan.attachTo(document, {
    suffixKeyCodes: [13],
    onScan: function(barcode) {
        console.log(barcode)
        window.livewire.emit('scan-code', barcode)
    },
    onScanError: function(e){
        //console.log(e)
    }
})

    console.log('Scanner ready!')


} catch(e){
    console.log('Error de lectura: ', e)
}

*/
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
                    
                    
								window.livewire.on('ver-catalogo', Msg => {
										$('#catalogo').modal('show')
								})                    
								window.livewire.on('ver-catalogo-hide', Msg => {
										$('#catalogo').modal('hide')
								})
								window.livewire.on('modal-agregar-cliente-hide', Msg => {
								//	alert("hola");
								})
								
                                livewire.on('scan-code', action => {
                                    $('#code').val('')
                                })

        						window.livewire.on('modal-formulario', Msg => {
        							$('#ModalFormulario').modal('show')
        							document.getElementById('ModalFormulario').style.display = 'block';
        
        						})
        						
        						 window.livewire.on('modal-formulario-hide', Msg => {
        							$('#ModalFormulario').modal('hide')
        							document.getElementById('ModalFormulario').style.display = 'none';
        
        						})
        
        						window.livewire.on('cliente-agregado', Msg => {
        								$('#theModal-cliente').modal('hide')
        								noty(Msg)
        						})

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})
								
								window.livewire.on('datos-cliente', Msg => {
										$('#datos-cliente').modal('show')
								})
								
								window.livewire.on('datos-cliente-hide', Msg => {
										$('#datos-cliente').modal('hide')
								})


								window.livewire.on('variacion-elegir', Msg => {
										$('#Variaciones').modal('show')
								})

								window.livewire.on('variacion-elegir-hide', Msg => {
										$('#Variaciones').modal('hide')
										document.getElementById('Variaciones').style.display = 'none';
								})

								window.livewire.on('formulario', Msg => {
									$('#ModalFormulario').modal('show');
									document.getElementById('ModalFormulario').style.display = 'block';
								})

								window.livewire.on('formulario-hide', Msg => {
										$('#ModalFormulario').modal('hide');
										document.getElementById('ModalFormulario').style.display = 'none';
										noty(Msg);
								})

								window.livewire.on('agregar-pago', Msg =>{
										$('#AgregarPago').modal('show')
								})

								window.livewire.on('agregar-pago-hide', Msg =>{
										$('#AgregarPago').modal('hide')
								})

								window.livewire.on('show-modal', Msg =>{
										$('#modalDetails').modal('show')
								})
								
								
								window.livewire.on('show-modal-descuentos', Msg =>{
										$('#Descuentos').modal('show')
								})
								
								window.livewire.on('hide-modal-descuentos', Msg =>{
										$('#Descuentos').modal('hide')
								})

								window.livewire.on('show-modal-alto', Msg =>{
										$('#modalDetailsAlto').modal('show')
								})

								window.livewire.on('show-modal2', Msg =>{
										$('#modalDetails2').modal('show')
								})

								window.livewire.on('tipo-pago-nuevo-show', Msg =>{
										$('#ModalBanco').modal('show')
								})

								window.livewire.on('tipo-pago-nuevo-hide', Msg =>{
										$('#ModalBanco').modal('hide')
								})

								window.livewire.on('metodo-pago-nuevo-show', Msg =>{
										$('#ModalMetodoPago').modal('show')
								})

								window.livewire.on('metodo-pago-nuevo-hide', Msg =>{
										$('#ModalMetodoPago').modal('hide')
								})

								window.livewire.on('hide-modal2', Msg =>{
										$('#modalDetails2').modal('hide')
								})

								window.livewire.on('cerrar-factura', Msg =>{
										$('#theModal1').modal('hide')
								})

								window.livewire.on('modal-show', msg => {
									$('#theModal1').modal('show')
								})


								window.livewire.on('show-modal3', Msg =>{
										$('#modalDetails3').modal('show')
								})

								window.livewire.on('hide-modal3', Msg =>{
										$('#modalDetails3').modal('hide')
								})

								window.livewire.on('productos-hide', Msg =>{
										$('#ModalProductos').modal('hide')
								})

								window.livewire.on('metodo-pago-hide', Msg =>{
										$('#ModalMetodoPago').modal('hide')
								})

								window.livewire.on('cliente-hide', Msg =>{
										$('#ModalCliente').modal('hide')
								})

								window.livewire.on('banco-hide', Msg =>{
										$('#ModalBanco').modal('hide')
								})

								window.livewire.on('info-prod', Msg =>{
										$('#InfoProducto').modal('show')
								})

								window.livewire.on('hide-info-prod', Msg =>{
										$('#InfoProducto').modal('hide')
								})


								window.livewire.on('hr-added', Msg => {
									noty(Msg)
								})

								window.livewire.on('modal-estado', Msg =>{
										$('#modalDetails-estado-pedido').modal('show')

								})

								window.livewire.on('modal-estado-hide', Msg =>{
										$('#modalDetails-estado-pedido').modal('hide')
								})

								window.livewire.on('hr-asignada', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-agregado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-actualizado', Msg => {
									noty(Msg)
								})

                                window.livewire.on('mail-modal', Msg =>{
                                $('#modalImprimir').modal('hide')
                                $('#MailModal').modal('show')
                                })


								window.livewire.on('pago-eliminado', Msg => {
									noty(Msg)
								})




								window.livewire.on('buscar-stock', id => {
									swal({
										title: 'BUSCAR STOCK EN OTRA SUCURSAL',
										text: '¿DESEA BUSCAR EN OTRAS SUCURSALES ESTE PRODUCTO?',
										showCancelButton: true,
										cancelButtonText: 'Cerrar',
										cancelButtonColor: '#fff',
										confirmButtonColor: '#3B3F5C',
										confirmButtonText: 'Aceptar'
									}).then(function(result) {
										if (result.value) {
											window.livewire.emit('info-producto', id)
											swal.close()
										}

									})

								})

                                // 8-1-2024
								window.livewire.on('volver-stock', id => {
								var stock = $("#q"+id).val();
								$("#r"+id).val(stock);
								})

								window.livewire.on('no-stock', Msg => {
									noty(Msg, 2)
								})



								window.livewire.on('imprimir-show', Msg => {
									$('#modalImprimir').modal('show')
								})

    			    	window.livewire.on('no-factura', id => {
                swal({
                title: 'IMPORTATE',
                text: 'DEBE CONFIGURAR SUS DATOS FISCALES ANTES DE FACTURAR',
                showCancelButton: true,
                cancelButtonText: 'CERRAR',
                cancelButtonColor: '#fff',
                confirmButtonColor: '#3B3F5C',
                confirmButtonText: 'IR A CONFIGURAR'
                }).then(function(result) {
                if (result.value) {
                window.location.href = '/mi-comercio';
                swal.close()
                }

                })

                })

    			window.livewire.on('falta-monto', id => {
                swal({
                title: 'IMPORTANTE',
                text: 'EN PAGO TOTAL, EL MONTO A COBRAR DEBE SER MAYOR O IGUAL AL TOTAL',
                confirmButtonColor: '#3B3F5C',

                })

                })
                






								window.livewire.on('mensaje-facturar', id => {
									swal({
										title: 'FACTURAR',
										text: '¿DESEA EMITIR LA FACTURA DE AFIP?',
										showCancelButton: true,
										cancelButtonText: 'Cerrar',
										cancelButtonColor: '#fff',
										confirmButtonColor: '#3B3F5C',
										confirmButtonText: 'Aceptar'
									}).then(function(result) {
									    if (result.value === true) {
											window.livewire.emit('emitir-factura', id)
											swal.close()
										} else {
										    window.livewire.emit('print', id)
											swal.close()
										}

									})

								})
						


							window.livewire.on('update-cliente-modal', data  => {
                            swal({
                                title: 'CLIENTE CON LISTA DE PRECIOS DISTINTA',
                                text: data.mensaje,
                                showCancelButton: true,
                                cancelButtonText: 'NO',
                                cancelButtonColor: '#fff',
                                confirmButtonColor: '#3B3F5C',
                                confirmButtonText: 'SI'
                            }).then(function(result) {
                                if (result.value) {
                                    window.livewire.emit('update-cliente', data.query_id)
                                    swal.close()
                                } else {
                                    window.livewire.emit('no-update-cliente', data.query_id)
                                    swal.close()
                                }
                            });
                        });
                        


								var total = $('#suma_totales').val();
								$('#ver_totales').html('Ventas: '+total);


    });
</script>

<script>


	function QuitarPromo(id) {

		swal({
			title: 'ATENCION',
			text: '¿CONFIRMAS QUITAR LA PROMOCION? ',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('QuitarPromo', id)
				swal.close()
			}

		})
	}  
	
	function ConfirmCaja(id) {

		swal({
			title: 'GUARDAR VENTA EN CAJA ANTERIOR',
			text: '¿CONFIRMAS GUARDAR LA VENTA?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('saveSale', id)
				swal.close()
			}

		})
	}    
</script>
<script type="text/javascript">

function cerrarModalDescuentos() {
  // Oculta el modal con ID "Descuentos"
  $("#Descuentos").modal('hide');
}

function cerrarModalClientes() {
  // Oculta el modal con ID "Descuentos"
  $("#ModalAgregarCliente").modal('hide');
}

function mostrar()  {

var viewed =	localStorage.getItem("viewed");
var completo_formulario = document.getElementById('completo_formulario').value;


if (completo_formulario != 1) {

if (viewed != 'true') {

$('#ModalFormulario').modal('show');
localStorage.setItem("viewed", 'true');

}

}

}
		 window.onload = mostrar;
</script>
<script type="text/javascript">
	function mostrar_empleador() {
		$('#ModalFormulario').modal('show');
		document.getElementById('ModalFormulario').style.display = 'block';
	}
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

 
    const div1 = document.getElementById("div1");
    const div2 = document.getElementById("div2");
    const mostrarDiv1Button = document.getElementById("mostrarDiv1");
    const mostrarDiv2Button = document.getElementById("mostrarDiv2");

    mostrarDiv1Button.addEventListener("click", function () {
        div1.style.display = "block";
        div2.style.display = "none";
        divb1.style.display = "block";
        divb2.style.display = "none";
    });

    mostrarDiv2Button.addEventListener("click", function () {
        div1.style.display = "none";
        div2.style.display = "block";
        divb1.style.display = "none";
        divb2.style.display = "block";
    });
});

</script>
@include('livewire.pos_nuevo.scripts.shortcuts')
@include('livewire.pos_nuevo.scripts.events')
@include('livewire.pos_nuevo.scripts.general')
