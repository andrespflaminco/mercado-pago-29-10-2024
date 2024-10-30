
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre de la lista</label>
									  <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="" >
                                         @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>WooCommerce Key de la lista de precios</label>
									     <input type="text" wire:model="wc_key" class="form-control" placeholder="" >
                                         @error('wc_key') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Descripcion</label>
									     <textarea wire:model="descripcion" name="name" class="form-control" style="width:100%;" rows="8" cols="80">
                                		</textarea>
                                		@error('descripcion') <span class="text-danger er">{{ $message }}</span> @enderror
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
