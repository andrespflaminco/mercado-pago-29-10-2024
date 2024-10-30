<div wire:ignore.self class="modal fade" id="theModalCaja" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b>  | AGREGAR CAJA ANTERIOR  </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
						<div class="col-lg-12 col-sm-12 col-12">
						 <div class="form-group">
                        
                          <label>Numero de caja</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span class="input-group-text input-gp">
                                CAJA NRO
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="numero_caja_ca" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                         <div class="form-group">
                             
                          <label>Fecha inicio caja</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                            </div>
                            <input type="datetime-local" wire:model.lazy="fecha_inicial_ca" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                        
                        <div class="form-group">
                        
                          <label>Monto inicial</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_inicial_ca" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                        
                        <div class="form-group">
                             
                          <label>Fecha final caja</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                            </div>
                            <input type="datetime-local" wire:model.lazy="fecha_final_ca" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                        
                         <div class="form-group">
                        
                          <label>Monto final</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_final_ca" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div> 
						</div>
						</div>
						<div class="col-lg-12">
							
							 <a class="btn btn-submit me-2" id="btn-caja-abrir" type="button" wire:loading.attr="disabled" wire:click.prevent="AbrirCajaAnterior()">Aceptar</a>
							<a class="btn btn-cancel" wire:click.prevent="resetUI()" data-bs-dismiss="modal">Cancelar</a>
							
						</div>
					</div>
				</div>
			</div>
		</div>


