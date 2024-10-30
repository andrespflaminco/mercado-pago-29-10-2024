	                <div class="page-header">
					<div class="page-title">
					    @if($selected_id < 1)
							<h4>Agregar</h4>
							<h6>Agregue un nuevo metodo de cobro</h6>
						@else
						    <h4>Editar</h4>
							<h6>Edite el metodo de cobro</h6>
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
									   <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Credito a 30 dias" >
                                       @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
                                 <div class="form-group">
                                  <label>Categoría</label>
                                    <select wire:model='categoria' class="form-control">
                                      <option value="Elegir" disabled >Elegir</option>
                                      <option value="1">Efectivo</option>
                                      <option value="2">Bancos</option>
                                      <option value="3">Plataformas de pago</option>
                                    </select>
                                    @error('categoria') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
                                 <div class="form-group">
                                  <label>Recargo</label>
                                  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                
                                    <input type="text" wire:model.lazy="recargo" class="form-control" placeholder="Ej: 10" >
                                    <div class="input-group-append">
                                      <span class="input-group-text input-gp" style="height: 100% !important;">
                                        %
                                      </span>
                                    </div>
                                      </div>
                                
                                  @error('recargo') <span class="text-danger er">{{ $message }}</span> @enderror
                                </div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
                                
								@if($categoria == 2)
								
								 <div class="form-group">
                                  <label>Banco</label>
                                    <select wire:model='cuenta' class="form-control">
                                      <option value="Elegir" disabled >Elegir</option>
                                      @foreach($bancos as $b)
                                        <option value="{{$b->id}}" >{{$b->nombre}}</option>
                                      @endforeach
                                    </select>
                                    @error('cuenta') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>
								
								@else

                                @if($categoria == 3)
								 <div class="form-group">
                                  <label>Plataforma</label>
                                    <select wire:model='cuenta' class="form-control">
                                      <option value="Elegir" disabled >Elegir</option>
                                      @foreach($plataformas as $p)
                                        <option value="{{$p->id}}" >{{$p->nombre}}</option>
                                      @endforeach
                                    </select>
                                    @error('cuenta') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>
								@endif
								
								@endif
							    </div>
							
							<div class="col-lg-6 col-sm-6 col-12">
							    <div class="form-group">
                                  <label>Plazo acreditacion</label>
                                    <select wire:model='acreditacion_inmediata' class="form-control">
                                      <option value="1">Acreditacion Inmediata</option>
                                      <option value="0">Acreditacion a Plazo</option>
                                    </select>
                                    @error('acreditacion_inmediata') <span class="text-danger err">{{ $message }}</span> @enderror
                                </div>    
							</div>    
							<div class="col-sm-12 col-md-12 mb-4">
                                <label for="">Deducciones</label>
                                <br>
                                <a href="#" wire:click.prevent="addDeduccion">+ Agregar</a>
                                @if(0 < count($deducciones))
                                <div style="width: 50%;" class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Deduccion</th>
                                                <th class="text-center">%</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deducciones as $index => $deduccion)
                                                <tr>
                                                    <td>
                                                        <input type="text" wire:model="deducciones.{{ $index }}.nombre" class="form-control" />
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" wire:model="deducciones.{{ $index }}.porcentaje" class="form-control" />
                                                    </td>
                                                    <td>
                                                        <a href="#" wire:click.prevent="removeDeduccion({{ $index }})">✖</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>

                            <div class="col-sm-12 col-md-12"></div>
                            
							@if (($cuenta != "Elegir") || ($categoria == 1))
							
						 
                            <div class="col-sm-12 col-md-12">
                              <label for="">Se muestra en:</label>
                              <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
                                @if(auth()->user()->sucursal != 1)
                                <div class="d-flex form-group">
                                <input type="checkbox"  wire:model="muestra_sucursales.{{$casa_central_id}}"  ><label style="margin-left: 10px; margin-bottom: 0px;">Casa central</label>
                                </div>
                                @endif
                            	@if(count($sucursales))
                           
                                @foreach($sucursales as $s)
                               <div class="d-flex form-group">
                                <input type="checkbox" wire:model="muestra_sucursales.{{ $s->id }}" ><label style="margin-left: 10px; margin-bottom: 0px;">{{$s->nombre_sucursal}}</label>
                              </div>
                              @endforeach
                              @endif
                              </div>
                            
                            </div>
                            
                            
                            
                            
                            @endif

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
