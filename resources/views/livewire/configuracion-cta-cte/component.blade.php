<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Configuracion de Cuenta corriente</h4>
							<h6></h6>
						</div>
						<div class="page-btn">               											    
						</div>
					</div>
					
                    
					<!-- /product list -->
                    @include('common.tab-configuracion')
					
					<div class="card">
                    	<div class="card-body">
                        
                        						<div class="row mb-4"> 
                        						   
                        							<div class="col-lg-6 col-sm-12 col-12">
                        								<div class="form-group">
                        									<label>Forma de gestionar la cuenta corriente</label>
                        									<select class="form-control" wire:model="configuracion_valor">
                        									    <option value="por_sucursal">Cada sucursal tiene su cuenta corriente con el cliente</option>
                        									    <option value="compartido">Cuenta corriente compartida para toda la cadena </option>
                        									</select>
                        								</div>
                        							</div>
                        							<div class="col-lg-6 col-sm-12 col-12">
                        							</div>
                        						    <div class="col-lg-6 col-sm-12 col-12">
                        								    <input type="checkbox" wire:model="configuracion_sucursales_agregan_pago">
                        									<label>Las sucursales pueden cobrar en ventas de otras sucursales</label>
                        							</div>
                        							<div class="col-lg-6 col-sm-12 col-12">
                        							</div>
                        						</div>
                        						
                        						<div class="col-lg-12">
                                            		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracion()"  data-bs-dismiss="modal">Cancelar</a>
                        					     	<a class="btn btn-submit me-2" wire:click="UpdateConfiguracion()" >Guardar</a>
                        						</div>
                    	</div>
                    </div>
                </div>

<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('msg', msg => {
			noty(msg)
		});

	});

</script>
