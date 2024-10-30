
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">

								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre</label>
										<input type="text" wire:model.defer="nombre" class="form-control" maxlength="255">
										@error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>

								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Plan</label>
										<select wire:model.defer="plan_id" class="form-control">
										    <option value="1">Emprendedor</option>
										    <option value="2">Pequeñas empresas</option>
										    <option value="3">Medianas empresas</option>
										    <option value="4">Grandes empresas</option>
										</select>
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Origen</label>
										<input type="text" wire:model.defer="origen" class="form-control" maxlength="255">
										@error('origen') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Monto $</label>
										<input type="text" wire:model.defer="monto" class="form-control" maxlength="255">
										@error('monto') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Plan id de Mercado Pago</label>
										<input type="text" wire:model.defer="preapproval_plan_id" class="form-control" maxlength="255">
										@error('preapproval_plan_id') <span class="text-danger er">{{ $message }}</span> @enderror
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
