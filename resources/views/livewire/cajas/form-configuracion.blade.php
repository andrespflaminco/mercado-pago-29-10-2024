<div>
<div class="page-header">

						<div class="page-title">
						    
						    
							<h4>CONFIGURACIONES </h4>
							<h6>Setea las configuraciones de las cajas. </h6>
						
						</div>
					</div>
                    <!-- /add -->
                <div class="card">
    					<div class="card-body">
    
    						<div class="row mb-4"> 
    						    <h5 style="font-style: bold;">CONFIGURACION DE CAJAS </h5>
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>Cajas abiertas de forma simultanea</label>
    									<select class="form-control" wire:model="configuracion_cantidad_cajas">
    									    <option value="0">Permitir abrir una sola caja por Sucursal</option>
    									    <option value="1">Permitir abrir multiples cajas por sucursal (una caja por Usuario) </option>
    									</select>
    								</div>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    						</div>
    						
    						<div class="col-lg-12">
                        		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracionCaja()"  data-bs-dismiss="modal">Cancelar</a>
    					     	<a class="btn btn-submit me-2" wire:click="UpdateConfiguracionCaja()" >Guardar</a>
    						</div>
    					</div>
				</div>
				
				

				
</div>

