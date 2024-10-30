
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Cuenta Origen</label>
										<select class="form-control" wire:model="banco_origen_form">
										    <option value="Elegir" selected>Elegir</option>
										    <option value="1">Efectivo</option>
										    @foreach($bancos as $b)
										    <option value="{{$b->id}}">{{$b->nombre}}</option>
										    @endforeach
										</select>
										@error('banco_origen_form') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Cuenta Destino</label>
										<select class="form-control" wire:model="banco_destino_form">
										    <option value="Elegir" selected>Elegir</option>
										    <option value="1">Efectivo</option>
										    @foreach($bancos as $b)
										    <option value="{{$b->id}}">{{$b->nombre}}</option>
										    @endforeach
										</select>
										@error('banco_destino_form') <span class="text-danger er">{{ $message }}</span> @enderror

									</div>
								</div>
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Monto ($)</label>
										<input class="form-control" wire:model="monto_form">
										@error('monto_form') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								
								<div class="col-lg-12">
								    
                                       <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
								      @if($selected_id < 1)
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
                                       @else
                                       <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                       @endif
									
									
								</div>
							</div>
						</div>
					</div>
					<!-- /add -->
