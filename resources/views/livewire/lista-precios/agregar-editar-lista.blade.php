
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre de la lista</label>
									  <input {{$es_lista_defecto == 1 ? 'readonly' : ''}} type="text" wire:model.lazy="nombre" class="form-control" placeholder="" >
                                         @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Destinatario de la lista:</label>
									  <select {{$es_lista_defecto == 1 ? 'disabled' : ''}} class="form-control" wire:model="tipo">
									      <option value="1">Clientes finales</option>
									      <option value="2">Sucursales</option>
									  </select>
                                         @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>

								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Configuracion de la lista</label>
									     <select class="form-control" wire:model="regla_precio">
									         <option value="1">Ingreso de precio manualmente (precio fijo)</option>
									         <option value="2">% de utilidad sobre el costo</option>
									     </select>
                                         @error('regla_precio') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								@if($regla_precio == 2)
								<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>% de margen sobre el costo por defecto</label>
									     <input type="number" wire:model="porcentaje_regla_precio" class="form-control" placeholder="" >
                                         @error('porcentaje_regla_precio') <span class="text-danger er">{{ $message }}</span> @enderror
                                    @if(0 < $selected_id) 
                                    <input type="checkbox" wire:model="modificar_porcentajes_lista_todos"> Modificar los % de utilidad y precios de los productos ya creados.
                                    <p hidden style="color:red;"> No se moficaran los % sobre los productos ya creados.</p> 
                                    @endif
    								
    								@if($es_lista_defecto == 1) 
    								  <input type="checkbox" wire:model="modificar_porcentajes_lista_todos"> Modificar los % de utilidad y precios de los productos ya creados.
    								@endif
									</div>
								</div>
								@endif

								@if( $regla_precio == 2 && ( 0 < $selected_id  ) )
								<div hidden style="margin-top: 28px !important;" class="col-lg-6 col-sm-6 col-12">
									<a style="background: #FF9F43; padding: 7px 15px; color: #fff; font-weight: 700; font-size: 14px;" href="{{ url('products-precios') }}" target="_blank" class="btn btn-added">Modificar % de utilidad en los productos ya creados</a>
								</div>
								@endif
								

								
								
								@if($regla_precio == 1)
								<div class="col-lg-6 col-sm-6 col-12">
								</div>
								@endif
								

								@if($es_lista_defecto != 1)
								<div class="col-lg-12 col-sm-12 col-12 mt-3">
									<div class="form-group">
										<label>Sucursales donde se muestra:</label>
                                    <table>

                                    <tr>
                                        <td style="margin-right:15px;">
                                        <input type="checkbox"  wire:model="sucursal_lista.{{ auth()->user()->casa_central_user_id }}" >
                                        </td>
                                        <td>
                                        @php
                                           $nombre_casa_central = \App\Models\User::find(Auth::user()->casa_central_user_id)->name;
                                        @endphp
                                        
                                        {{ $nombre_casa_central }}
                                        </td>
                                    </tr>
                                    
					            @foreach($sucursales as $sucursal)
                                    <tr>
                                        <td>
                                        <input type="checkbox"  wire:model="sucursal_lista.{{ $sucursal->sucursal_id }}" style="margin-right:15px;">
                                        </td>
                                        <td>{{ $sucursal->name }}</td>
                                    </tr>

                                @endforeach
                                        
                                    </table>

                                 </div>
                                </div>
                    			@endif
                    			
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Descripcion</label>
									     <textarea {{$es_lista_defecto == 1 ? 'readonly' : ''}} wire:model="descripcion" name="name" class="form-control" style="width:100%;" rows="8" cols="80">
                                		</textarea>
                                		@error('descripcion') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-12 col-sm-6 col-12">
									<div class="form-group">
										<label>WooCommerce Key de la lista de precios</label>
									     <input {{$es_lista_defecto == 1 ? 'readonly' : ''}} type="text" wire:model="wc_key" class="form-control" placeholder="" >
                                         @error('wc_key') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
							
								<div class="col-lg-12">
								    
								    
                                       <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
								      @if($es_lista_defecto == 0)
								      @if($selected_id < 1)
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
                                       @else
                                       <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                       @endif
                                       @endif
									   @if($es_lista_defecto == 1)
									   <a wire:click.prevent="UpdateListaDefecto()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
									   @endif
									   
									   @if($modificar_porcentajes_lista_todos == true)
									    <!-- Mensaje de cargando mientras la solicitud está en progreso -->
                                        <div wire:loading>
                                            <p>Actualizando precios, por favor espera...</p>
                                        </div>
                                    
                                        @endif
									
								</div>
							</div>
						</div>
					</div>
					<!-- /add -->
