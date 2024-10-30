<div class="modal fade" id="theModal" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }} </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
									<label>Nombre del atributo</label>
									<input type="text" wire:model.lazy="name" class="form-control" placeholder="ej: Talle" maxlength="255">
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							
							 @if($selected_id < 1)
							 <a class="btn btn-submit me-2" wire:click.prevent="Store()" >Guardar</a>
                             @else
                             <a class="btn btn-submit me-2" wire:click.prevent="Update()" >Actualizar</a>
                             @endif
							<a class="btn btn-cancel" wire:click.prevent="resetUI()"  data-bs-dismiss="modal">Cancelar</a>
						</div>
					</div>
				</div>
			</div>
		</div>

