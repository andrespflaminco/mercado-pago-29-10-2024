	                <div class="page-header">
					<div class="page-title">
					    @if($selected_id < 1)
							<h4>Agregar banco/plataforma</h4>
							<h6>Agregue un nuevo banco, plataforma o metodo de cobro</h6>
						@else
						    <h4>Editar</h4>
							<h6>Edite el tipo de cobro</h6>
						@endif
						</div>
						<div class="page-btn">
						</div>
					</div>
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre</label>
									   <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Banco nacion" >
                                       @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									 <div class="form-group">
                                      <label>CBU</label>
                                      <input type="text" wire:model.lazy="CBU" class="form-control" placeholder="Ej: 2009XXX " >
                                    @error('CBU') <span class="text-danger er">{{ $message }}</span> @enderror
                                    </div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
                                 <div class="form-group">
                                  <label>CUIT</label>
                                    <input type="text" wire:model.lazy="cuit" class="form-control" placeholder="Ej: 20-32XXX "  >
                                  @error('cuit') <span class="text-danger er">{{ $message }}</span> @enderror
                                </div>
								</div>
									
								<div class="col-lg-6 col-sm-6 col-12">
                                 <div class="form-group">
                                  <label>Tipo</label>
                                    <select wire:model='tipo' class="form-control">
                                      <option value="Elegir" disabled >Elegir</option>
                                      <option value="2">Banco</option>
                                      <option value="3">Plataforma de pago</option>
                                    </select>
                                    @error('tipo') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>
								</div>
								
								<div class="col-lg-6 col-sm-12 col-12">
                                 <div class="form-group">
                                  <label>Saldo inicial</label>
                                    <input type="number" class="form-control" wire:model="saldo_inicial">
                                    @error('saldo_inicial') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>
								</div>
								
								<div class="col-lg-6 col-sm-12 col-12">
                                </div>
							
                               
                                
                                <div class="col-sm-12 col-md-12">
                                  <label for="">Se muestra:</label>
                                  <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
                                    @if(auth()->user()->sucursal != 1)
                                    <div class="d-flex form-group">
                                    <input type="checkbox"  wire:model="muestra_sucursales.{{ $casa_central_id }}"  ><label style="margin-left: 10px; margin-bottom: 0px;">Casa central</label>
                                    </div>
                                    @endif
                                    
                                   @if(count($sucursales))
                                    @foreach($sucursales as $s)
                                   <div class="d-flex form-group">
                                   
                                   <input type="checkbox"  wire:model="muestra_sucursales.{{ $s->id }}" ><label style="margin-left: 10px; margin-bottom: 0px;">{{$s->nombre_sucursal}}</label>
                                  </div>
                                  @endforeach
                                  @endif
                                  </div>
                                
                                </div>
                                
                                
                                <br><br>
								<div class="col-lg-12">
								    <br>
								    
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
