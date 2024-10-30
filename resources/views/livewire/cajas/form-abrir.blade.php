<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b> | ABRIR </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
						<div class="col-lg-12 col-sm-12 col-12">
						
						<div class="form-group">
                          <label>Usuario responsable</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <select disabled class="form-control" wire:model="usuario_caja">
                                @foreach($usuarios as $u)
                                <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            
                           </div>
                        </div>
                        
                        
						 <div class="form-group">
                          <label>Monto inicial</label>
                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                            <div class="input-group-prepend">
                              <span style="height:100%" class="input-group-text input-gp">
                                $
                              </span>
                            </div>
                            <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >
                        
                              </div>
                              @error('monto_inicial') <span style="color: #dc3545 !important;" class="error">{{ $message }}</span> @enderror
       
                        </div>
						</div>
						</div>
						<div class="col-lg-12">
							
							<a class="btn btn-cancel" wire:click.prevent="resetUI()" data-bs-dismiss="modal">Cancelar</a>
							 <a class="btn btn-submit me-2" id="btn-caja-abrir" type="button" wire:loading.attr="disabled" wire:click.prevent="AbrirCaja({{$usuario_caja}})">Aceptar</a>
							
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