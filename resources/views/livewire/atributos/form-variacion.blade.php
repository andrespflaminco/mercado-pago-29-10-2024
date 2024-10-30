<div wire:ignore.self class="modal fade" id="theModalVariacion" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >		<b>{{$componentName}}</b> | {{ $id_variacion > 0 ? 'EDITAR VARIACION' : 'CREAR VARIACION' }} </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
									<label>Nombre de la variacion</label>
									<input type="text" wire:model.lazy="name_variacion" class="form-control" placeholder="ej: Small" maxlength="255">
	                                @error('name_variacion') <span class="text-danger er">{{ $message }}</span> @enderror
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							
							 @if($selected_id < 1)
							 <a class="btn btn-submit me-2" wire:click.prevent="StoreVariacion()" >Guardar</a>
                             @else
                             <a class="btn btn-submit me-2" wire:click.prevent="UpdateVariacion()" >Actualizar</a>
                             @endif
							<a class="btn btn-cancel" wire:click.prevent="resetUI()"  data-bs-dismiss="modal">Cancelar</a>
						</div>
					</div>
				</div>
			</div>
		</div>