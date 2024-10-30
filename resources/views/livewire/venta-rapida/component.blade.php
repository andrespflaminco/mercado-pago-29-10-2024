@inject('cart', 'App\Services\CartCobros')



<div>
	<div class="row layout-top-spacing">
		<div class="col-sm-12 col-md-12">
    	
    	    @if($caja_abierta == null)
    
    		<br><br>
            <div class="row">
            <div class="col-sm-12 col-md-2">
            
             </div>
            <div class="col-sm-12 col-md-8">
            <div class="form-group">
            	<h5>Abrir Caja</h5>
             <div style="margin-bottom: 0 !important;" class="input-group mb-4">
            	 <div class="input-group-prepend">
            		 <span class="input-group-text input-gp">
            			 Monto inicial: $
            		 </span>
            	 </div>
            	 <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >
            
            		 </div>
            </div>
            </div>
            <div class="col-sm-12 col-md-2">
             </div>
             <div class="col-sm-12 col-md-2">
            
              </div>
             <div class="col-sm-12 col-md-8">
            	 <button style="float:right;" type="button" wire:click="AbrirCaja()" class="btn btn-dark close-modal" >GUARDAR</button>
            
             </div>
             <div class="col-sm-12 col-md-2">
            
              </div>
            
            
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
            
            
            
            </div>
            
            @else
            
            <div class="page-header">
    		<div class="page-title">
    			<h4>COBRO RAPIDO</h4>
    			<h6>Agregue un cobro rapido</h6>
    		</div>
    		<div class="page-btn">
    			<a href="{{ url('reporte-venta-rapida') }}"  class="btn btn-added">
    			Volver al Resumen
    			</a>
    		</div>
    	    </div>

        </div>
        
        <div class="col-sm-12 col-md-12">
        	<div id="connect-sorting" class="connect-sorting">
        			    <div style="width:100%;" class="row">
        			        
        			   
        	<div class="col-lg-4 mt-2 col-md-12 col-sm-12">
        				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
        					<div class="input-group-prepend">
        						<span style="height:100%;" class="input-group-text input-gp">
        							<i class="fas fa-users"></i>
        						</span>
        					</div>
        						<input
        								style="font-size:14px !important;"
        								type="text"
        								class="form-control"
        								placeholder="CUIT o DNI del cliente"
        								wire:model="cliente_cuit"
        								wire:keydown.escape="resetCliente"
        								wire:keydown.tab="resetCliente"
        								wire:keydown.enter="BuscarClienteAFIP"
        						/>
        						<div class="input-group-prepend">
        						<button wire:click="BuscarClienteAFIP" class="input-group-text input-gp">
        							Validar
        						</button>
        						

        					</div>
        						</div>
                                
        	        			<button hidden wire:click="BuscarCuitDNI" class="input-group-text input-gp">
        							- 
        						</button>
        	</div>
        	<div class="col-lg-4 col-md-12 col-sm-12 mt-2">
        
                <button type="button"  wire:click="AbrirModalConcepto()" style="width: 100%;" class="btn btn-success mt-lg-0 mt-sm-2" > + AGREGAR CONCEPTO NUEVO</button>
        
        		</div>
        		
        		 </div>
        		 
        		</div>
        
        		</div>
        
        @if($cuit_agregar != null)	
        
        <div class="card row" style="padding: 15px !important; margin: 10px !important;">
        <p>
        CUIT: {{$cuit_agregar}}     -     NOMBRE: {{$nombre_cliente_agregar}}  <a href="javascript:void(0)" wire:click="ResetearCuit()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a>    
        </p>
        </div>
        @endif
        
        
        
        <div class="col-sm-12 col-md-8">
        			<!-- DETALLES -->
        
        
        			@include('livewire.venta-rapida.partials.detail')
        
        
        		</div>
        
        <div class="col-sm-12 col-md-4">
        			<!-- TOTAL -->
        			@include('livewire.venta-rapida.partials.total')
        
        
        
        		</div>
        
        
            <!-- Modal -->
        <div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          x
                        </button>
                    </div>
                    <div class="modal-body" style="width:100%;">
                    <label>Mail</label>
                    <input type="text" wire:model.defer="mail_ingresado" class="form-control" >
                     </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                        <button class="btn btn-dark" wire:click="EnviarMail()"> Enviar </button>
                  
                    </div>
                </div>
            </div>
        </div>


@include('livewire.venta-rapida.dni')
@include('livewire.venta-rapida.form')
@include('livewire.venta-rapida.datos-cliente')
@include('livewire.venta-rapida.partials.imprimir')
@include('livewire.venta-rapida.concepto-rapido')
@include('livewire.venta-rapida.form2')

@endif

</div>
    
</div>

	<script type="text/javascript">
	var listener = new window.keypress.Listener();
	var cantidad = 10;
	var i

	for (i=0; i<=cantidad; i++) {
		listener.simple_combo("f"+i , function() {
			alert(id)
			window.livewire.emit(TeclaRapida, id)
		})
	}

	</script>


	<script>
	    document.addEventListener('DOMContentLoaded', function(){

							window.livewire.on('sale-ok', Msg => {
									$('#theModal2').modal('hide')
									noty(Msg)
							})
                                    
									window.livewire.on('dni-show', Msg => {
											$('#DNI').modal('show')
									})
									
									window.livewire.on('dni-hide', Msg => {
											$('#DNI').modal('hide')
									})
									
									window.livewire.on('agregar-concepto', Msg => {
											$('#theModal').modal('show')
									})
									
									window.livewire.on('agregar-concepto-hide', Msg => {
											$('#theModal').modal('hide')
									})
									
									window.livewire.on('agregar-cliente', Msg => {
											$('#theModal-cliente').modal('show')
									})

									window.livewire.on('agregar-pago', Msg =>{
											$('#AgregarPago').modal('show')
									})

									window.livewire.on('show-modal2', Msg =>{
											$('#theModal2').modal('show')
									})

									window.livewire.on('agregar-pago-hide', Msg =>{
											$('#AgregarPago').modal('hide')
									})

									window.livewire.on('pago-dividido', Msg =>{
											$('#PagoDividido').modal('show')
									})
									window.livewire.on('datos-cliente', Msg =>{
											$('#datos-cliente').modal('show')
									})

									window.livewire.on('modal-imprimir', Msg =>{
											$('#modalImprimir').modal('show')
									})

									window.livewire.on('datos-cliente-hide', Msg =>{
											$('#datos-cliente').modal('hide')
									})

									window.livewire.on('pago-dividido-hide', Msg =>{
											$('#PagoDividido').modal('hide')
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

									window.livewire.on('abrir-hr-nueva', msg => {
										$('#theModal').modal('show')
									})
									window.livewire.on('show-modal', Msg => {
										$('#theModal').modal('show')
									})
									window.livewire.on('show-modal3', Msg =>{
											$('#modalDetails3').modal('show')
									})

									window.livewire.on('hide-modal3', Msg =>{
											$('#modalDetails3').modal('hide')
									})



									window.livewire.on('modal-hr-hide', Msg =>{
											$('#theModal').modal('hide')
									})

									window.livewire.on('hr-added', Msg => {
										noty(Msg)
									})

									window.livewire.on('modal-estado', Msg =>{
											$('#modalDetails-estado-pedido').modal('show')
									})


									window.livewire.on('tabs-show', Msg =>{
									$('#tabsModal').modal('show')
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

									window.livewire.on('pago-eliminado', Msg => {
										noty(Msg)
									})
									
									window.livewire.on('mail-modal', Msg =>{
                                        $('#modalImprimir').modal('hide')
                                         $('#MailModal').modal('show')
                                    })
									//events
									window.livewire.on('product-added', Msg => {
										$('#theModal').modal('hide')
										noty(Msg)
									})

									window.livewire.on('no-stock', Msg => {
										noty(Msg, 2)
									})

									var total = $('#suma_totales').val();
									$('#ver_totales').html('Ventas: '+total);


	    });
	</script>

<script>
					    
	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: 'CONFIRMAS ELIMINAR LOS REGISTROS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('clearCart', id)
				swal.close()
			}

		})
	}
</script>
