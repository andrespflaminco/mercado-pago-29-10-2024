
<div wire:ignore.self class="modal fade" id="theModalEditar" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b> | EDITAR CAJA </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
						
						<div class="col-lg-12 col-sm-12 col-12">
						  
						  
						  
						  <div class="form-group">
                          <label>Usuario</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <select disabled class="form-control" wire:model="usuario_caja">
                                @foreach($usuarios as $u)
                                <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            
                           </div>
                        </div>
                        
                        
						  <div class="form-group">
                        
                          <label>Numero de caja</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height: 100%;" class="input-group-text input-gp">
                                CAJA NRO
                              </span>
                            </div>
                            <input type="text" disabled wire:model.lazy="nro_caja_form" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                        
                         <div class="form-group">
                         <label>Fecha incial</label>
                         <input type="datetime-local" wire:model="fecha_inicial_form" class="form-control" placeholder="Click para elegir">
                         </div>
                                              
                                              
                         <div class="form-group">
                        
                          <label>Monto inicial</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height: 100%;" class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>
                        
                         <div class="form-group">
                         <label>Fecha final</label>
                         <input type="datetime-local" wire:model="fecha_final_form" class="form-control" placeholder="Click para elegir">
                         </div>
                         
                         <div class="form-group">
                        
                          <label>Monto final</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height: 100%;" class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_final" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                        </div>

						</div>
						</div>
						<div class="col-lg-12">
							
							<a class="btn btn-cancel" wire:click.prevent="resetUI()" data-bs-dismiss="modal">Cancelar</a>
							 <a class="btn btn-submit me-2" id="btn-caja-ami" type="button" wire:loading.attr="disabled" wire:click.prevent="ActualizarCaja({{$caja_id}})">Aceptar</a>
							
						</div>
					</div>
				</div>
			</div>
		</div>

<script>
    document.getElementById("btn-caja-ami").addEventListener("click", btnDisableAmi);

    function btnDisableAmi() {
      document.getElementById("btn-caja-ami").disabled = true;
    }
</script>