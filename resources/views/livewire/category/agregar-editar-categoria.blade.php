
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre de la categoria</label>
										<input type="text" wire:model.defer="name" class="form-control" placeholder="ej: Cursos" maxlength="255">
										@error('name') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
							
								<div class="col-lg-12">
								      @if($selected_id < 1)
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
                                       @else
                                       <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                       @endif
                                       <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
									
									
								</div>
							</div>
						</div>
					</div>
					<!-- /add -->
