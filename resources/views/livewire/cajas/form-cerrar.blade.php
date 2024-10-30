

<div wire:ignore.self class="modal fade" id="theModal2" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b> | CERRAR </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
						
						@if($nro_caja_modal != null)
						<div class="col-lg-12 col-sm-12 col-12">
						 <div class="form-group">
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height: 100% !important;" class="input-group-text input-gp">
                                NRO CAJA
                              </span>
                            </div>
                            <input readonly type="text" wire:model.lazy="nro_caja_modal" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                             @error('nro_caja_modal') <span style="color: #dc3545 !important;" class="error">{{ $message }}</span> @enderror
       
                        </div>
						</div>
						@endif
						
						<div class="col-lg-12 col-sm-12 col-12">
						 <div class="form-group">
                          <label>Monto final</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height: 100% !important;"  class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_final" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                             @error('monto_final') <span style="color: #dc3545 !important;" class="error">{{ $message }}</span> @enderror
       
                        </div>
						</div>
						</div>
						<div class="col-lg-12">
							
							<a class="btn btn-cancel" wire:click.prevent="resetUI()" data-bs-dismiss="modal">Cancelar</a>
							<a class="btn btn-submit me-2" id="btn-caja-abrir" type="button" wire:loading.attr="disabled" wire:click.prevent="CerrarCaja({{$caja_activa}})">Aceptar</a>
							
						</div>
					</div>
				</div>
			</div>
		</div>

<script>
    document.getElementById("btn-caja-abrir").addEventListener("click", btnDisable);

    function btnDisable() {
      document.getElementById("btn-caja-abrir").disabled = true;
    }
</script>